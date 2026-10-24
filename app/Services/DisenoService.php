<?php

namespace App\Services;

use App\Enums\EstatusArticulo;
use App\Mail\DisenoListo;
use App\Mail\DisenoPendiente;
use App\Models\Articulo;
use App\Models\Rol;
use App\Models\Usuario;
use App\Support\Manuscrito;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Mesa de diseño: los artículos aprobados pasan a la cuenta de diseño, que
 * descarga el texto original y sube el PDF diseñado (articulo.arti_url_pdf_diseno).
 * El PDF pertenece al artículo, no al número: cuando el administrador agrega
 * el artículo a un número, ese PDF se publica solo (ArticuloRevista::rutaPdf).
 */
class DisenoService
{
    public const FILTROS = [
        'pendientes' => 'Por diseñar',
        'disenados' => 'Diseñados',
        'todos' => 'Todos',
    ];

    /**
     * Artículos aprobados con lo que la mesa de diseño necesita ver: tema,
     * palabras clave, autores (solo nombres) y en qué número salen. Los que
     * faltan por diseñar van primero; dentro de cada grupo, el más reciente.
     *
     * @return Collection<int, Articulo>
     */
    public function articulos(string $filtro = 'todos', bool $soloSinNumero = false): Collection
    {
        return Articulo::aprobados()
            ->with(['tema', 'palabras', 'autores.persona', 'entradasRevista.revista'])
            ->when($filtro === 'pendientes', fn ($q) => $q->whereNull('arti_url_pdf_diseno'))
            ->when($filtro === 'disenados', fn ($q) => $q->whereNotNull('arti_url_pdf_diseno'))
            ->when($soloSinNumero, fn ($q) => $q->whereDoesntHave('entradasRevista'))
            ->orderByRaw('arti_url_pdf_diseno is not null')
            ->orderByDesc('arti_fecha_modificacion')
            ->orderByDesc('arti_id_autor')
            ->get();
    }

    /** @return array{pendientes: int, disenados: int, total: int} */
    public function conteos(): array
    {
        $fila = Articulo::aprobados()
            ->selectRaw('count(*) as total, count(arti_url_pdf_diseno) as disenados')
            ->first();

        $total = (int) $fila->total;
        $disenados = (int) $fila->disenados;

        return ['pendientes' => $total - $disenados, 'disenados' => $disenados, 'total' => $total];
    }

    /** Nombres de los autores en orden (principal primero), sin correos. */
    public static function autores(Articulo $articulo): string
    {
        return $articulo->autores
            ->sortByDesc(fn ($autor) => (bool) $autor->pivot->auar_principal)
            ->map(fn ($autor) => $autor->persona?->nombre_completo)
            ->filter()
            ->join(', ');
    }

    /** «12-titulo-del-articulo.docx»: el id primero, para reconocerlo al regresar. */
    public static function nombreOriginal(Articulo $articulo): string
    {
        $extension = strtolower(pathinfo($articulo->arti_url_pdf, PATHINFO_EXTENSION)) ?: 'pdf';

        return $articulo->arti_id_autor.'-'.Str::limit(Str::slug($articulo->arti_titulo), 60, '').'.'.$extension;
    }

    /** «12-titulo-del-articulo-diseno.pdf». */
    public static function nombreDiseno(Articulo $articulo): string
    {
        return $articulo->arti_id_autor.'-'.Str::limit(Str::slug($articulo->arti_titulo), 60, '').'-diseno.pdf';
    }

    /**
     * Guarda el PDF diseñado de un artículo aprobado. Si ya tenía uno, lo
     * reemplaza (no se guardan versiones). Avisa por correo al administrador.
     */
    public function subir(Articulo $articulo, UploadedFile $pdf, Usuario $usuario): Articulo
    {
        if (Manuscrito::detectar((string) $pdf->getRealPath()) !== 'pdf') {
            throw ValidationException::withMessages(['pdf' => 'El archivo no es un PDF válido. Exporta el diseño a PDF y vuelve a subirlo.']);
        }

        $disco = Storage::disk('local');
        $ruta = config('emprende.carpeta_disenos').'/'.$articulo->arti_id_autor.'-'.Str::random(32).'.pdf';
        $disco->putFileAs(dirname($ruta), $pdf, basename($ruta));
        $anterior = null;

        try {
            $articulo = DB::transaction(function () use ($articulo, $ruta, $usuario, &$anterior) {
                $articulo = Articulo::whereKey($articulo->getKey())->lockForUpdate()->firstOrFail();

                if ($articulo->arti_status !== EstatusArticulo::APROBADO) {
                    throw ValidationException::withMessages(['pdf' => 'Solo se diseña un artículo aprobado.']);
                }

                $anterior = $articulo->arti_url_pdf_diseno;
                $articulo->forceFill([
                    'arti_url_pdf_diseno' => $ruta,
                    'arti_fecha_diseno' => now(),
                    'arti_id_disenador' => $usuario->usua_id_usuario,
                ])->save();

                return $articulo;
            });
        } catch (Throwable $e) {
            $disco->delete($ruta);

            throw $e;
        }

        if ($anterior !== null) {
            $disco->delete($anterior);
        }

        Log::channel('admin')->info($anterior ? 'PDF diseñado reemplazado' : 'PDF diseñado subido', [
            'usuario' => $usuario->usua_nombre_usuario,
            'arti_id_autor' => $articulo->arti_id_autor,
        ]);

        foreach ($this->correosDe(Rol::ADMINISTRADOR) as $correo) {
            Mail::to($correo)->queue(new DisenoListo(
                $articulo->arti_titulo,
                $anterior !== null,
                route('admin.ediciones.manuscritos'),
            ));
        }

        return $articulo;
    }

    /** Correo a la cuenta de diseño: hay un artículo aprobado por diseñar. */
    public function avisarAprobado(Articulo $articulo): void
    {
        $articulo->loadMissing('tema');

        foreach ($this->correosDe(Rol::DISENO) as $correo) {
            Mail::to($correo)->queue(new DisenoPendiente(
                $articulo->arti_titulo,
                $articulo->tema?->tema_nombre,
                route('diseno.panel'),
            ));
        }
    }

    /**
     * .zip temporal con los textos originales o con los PDF diseñados.
     *
     * @param  Collection<int, Articulo>  $articulos
     * @param  'originales'|'disenos'  $tipo
     * @return string|null ruta del .zip (el que llama lo borra) o null si no hay archivos
     */
    public function zip(Collection $articulos, string $tipo): ?string
    {
        $disco = Storage::disk('local');
        $archivos = $articulos
            ->map(fn (Articulo $a) => $tipo === 'disenos'
                ? [$a->arti_url_pdf_diseno, self::nombreDiseno($a)]
                : [$a->arti_url_pdf, self::nombreOriginal($a)])
            ->filter(fn (array $par) => $par[0] !== null && $disco->exists($par[0]));

        if ($archivos->isEmpty()) {
            return null;
        }

        $ruta = tempnam(sys_get_temp_dir(), 'diseno');
        $zip = new \ZipArchive;
        $zip->open($ruta, \ZipArchive::OVERWRITE);
        foreach ($archivos as [$archivo, $nombre]) {
            $zip->addFile($disco->path($archivo), $nombre);
        }
        $zip->close();

        return $ruta;
    }

    /**
     * Correos activos de las cuentas con el rol. Se omiten los de relleno
     * (dominio .invalid, como el del administrador en datos_iniciales.sql).
     *
     * @return list<string>
     */
    private function correosDe(string $rol): array
    {
        return Usuario::query()
            ->where('usua_activo', true)
            ->whereHas('roles', fn ($q) => $q->where('rol_nombre', $rol))
            ->with('persona.correo')
            ->get()
            ->map(fn (Usuario $u) => $u->persona?->correo?->comu_valor_comunicacion)
            ->filter(fn (?string $correo) => $correo && ! str_ends_with(strtolower($correo), '.invalid'))
            ->unique()
            ->values()
            ->all();
    }
}
