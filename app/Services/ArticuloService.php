<?php

namespace App\Services;

use App\Enums\EstatusArticulo;
use App\Mail\ArticuloRechazado;
use App\Mail\ArticuloRecibido;
use App\Models\Articulo;
use App\Models\Autor;
use App\Models\AutorArticulo;
use App\Models\Comunicacion;
use App\Models\Persona;
use App\Models\TipoComunicacion;
use App\Models\Usuario;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Envío de artículos (6.3), historial del autor (6.5) y rechazo (6.4).
 * La aprobación vive en ConstanciaService.
 */
class ArticuloService
{
    public function __construct(private PalabraClaveService $palabras) {}

    /**
     * Guarda el artículo, su PDF, autores y palabras clave en UNA transacción.
     * Si algo falla se borra el PDF guardado.
     *
     * @param  array{titulo: string, tema_id_tema: int|string, palabras: list<string>,
     *               tiene_coautores?: bool, coautores?: list<array<string, ?string>>}  $datos
     */
    public function enviar(Usuario $remitente, array $datos, UploadedFile $pdf): Articulo
    {
        $disco = Storage::disk('local');
        $ruta = null;

        try {
            $articulo = DB::transaction(function () use ($remitente, $datos, $pdf, $disco, &$ruta) {
                // El nombre del PDF usa el id del artículo: se reserva antes del INSERT.
                $id = (int) DB::selectOne(
                    "select nextval(pg_get_serial_sequence('articulo', 'arti_id_autor')) as id"
                )->id;
                $ruta = config('emprende.carpeta_articulos')."/{$id}.pdf";

                $articulo = (new Articulo)->forceFill([
                    'arti_id_autor' => $id,
                    'tema_id_tema' => (int) $datos['tema_id_tema'],
                    'arti_titulo' => $datos['titulo'],
                    'arti_url_pdf' => $ruta,
                    'arti_status' => EstatusArticulo::EN_REVISION,
                    'arti_fecha_registro' => today(),
                    'arti_fecha_modificacion' => today(),
                ]);
                $articulo->save();

                if ($disco->putFileAs(dirname($ruta), $pdf, basename($ruta)) === false) {
                    throw new \RuntimeException('No se pudo guardar el PDF del artículo.');
                }

                AutorArticulo::create([
                    'auar_id_autor' => $this->autorDe($remitente)->auto_id_autor,
                    'arti_id_autor' => $id,
                    'auar_principal' => true,
                ]);

                if (! empty($datos['tiene_coautores'])) {
                    foreach ($datos['coautores'] ?? [] as $coautor) {
                        AutorArticulo::create([
                            'auar_id_autor' => $this->coautor($coautor)->auto_id_autor,
                            'arti_id_autor' => $id,
                            'auar_principal' => false,
                        ]);
                    }
                }

                $this->palabras->asignarAArticulo($articulo, $datos['palabras']);

                return $articulo;
            });
        } catch (Throwable $e) {
            if ($ruta !== null) {
                $disco->delete($ruta);
            }

            throw $e;
        }

        if ($correo = $remitente->correo()) {
            Mail::to($correo)->queue(new ArticuloRecibido($remitente->persona->pers_nombre, $articulo->arti_titulo));
        }

        return $articulo;
    }

    /**
     * Artículos del usuario, como autor principal o como coautor.
     * Cada artículo trae en "papel" la fila de autor_articulo del usuario.
     */
    public function historialDe(Usuario $usuario, int $porPagina): LengthAwarePaginator
    {
        $usuario->loadMissing('persona.autor');
        $idAutor = $usuario->persona?->autor?->auto_id_autor;

        return Articulo::query()
            ->whereHas('autores', fn ($q) => $q->where('autor.auto_id_autor', $idAutor))
            ->with([
                'tema',
                'constancia',
                'autores' => fn ($q) => $q->where('autor.auto_id_autor', $idAutor),
            ])
            ->orderByDesc('arti_fecha_registro')
            ->orderByDesc('arti_id_autor')
            ->paginate($porPagina);
    }

    /**
     * Todos los artículos, para el administrador (RF-ADM-01): filtros por
     * estatus, tema y fecha de envío, y búsqueda por título, autor (principal
     * o coautor) o palabra clave, sin importar mayúsculas ni acentos.
     *
     * @param  array{estatus?: ?string, tema?: int|string|null, desde?: ?string, hasta?: ?string, q?: ?string}  $filtros
     */
    public function listarTodos(array $filtros, int $porPagina): LengthAwarePaginator
    {
        $texto = trim((string) ($filtros['q'] ?? ''));

        return Articulo::query()
            ->when($filtros['estatus'] ?? null, fn ($q, $estatus) => $q->where('arti_status', $estatus))
            ->when($filtros['tema'] ?? null, fn ($q, $tema) => $q->where('tema_id_tema', (int) $tema))
            ->when($filtros['desde'] ?? null, fn ($q, $desde) => $q->whereDate('arti_fecha_registro', '>=', $desde))
            ->when($filtros['hasta'] ?? null, fn ($q, $hasta) => $q->whereDate('arti_fecha_registro', '<=', $hasta))
            ->when($texto !== '', function ($q) use ($texto) {
                $patron = BuscadorService::patronLike($texto);
                $q->where(fn ($w) => $w
                    ->whereRaw(BuscadorService::sinAcentos('arti_titulo').' like ?', [$patron])
                    ->orWhereHas('palabras', fn ($p) => $p->whereRaw(BuscadorService::sinAcentos('pala_descripcion').' like ?', [$patron]))
                    ->orWhereHas('autores.persona', fn ($p) => $p->whereRaw(
                        BuscadorService::sinAcentos("concat_ws(' ', pers_nombre, pers_primer_apellido, pers_segundo_apellido)").' like ?',
                        [$patron],
                    )));
            })
            ->with(['tema', 'autores' => fn ($q) => $q->wherePivot('auar_principal', true)->with('persona')])
            ->orderByRaw("case when arti_status = 'EN_REVISION' then 0 else 1 end")
            ->orderByDesc('arti_fecha_registro')
            ->orderByDesc('arti_id_autor')
            ->paginate($porPagina)
            ->withQueryString();
    }

    /** Artículo con todo lo que muestra el detalle del administrador. */
    public function detalle(Articulo $articulo): Articulo
    {
        return $articulo->load([
            'tema',
            'palabras',
            'constancia',
            'autores' => fn ($q) => $q->orderByDesc('autor_articulo.auar_principal')->orderBy('autor_articulo.auar_id_autor_articulo'),
            'autores.persona.correo',
            'autores.persona.usuario',
        ]);
    }

    /**
     * Rechaza un artículo en revisión. Es definitivo.
     *
     * @throws ValidationException si el artículo ya fue dictaminado.
     */
    public function rechazar(Articulo $articulo, string $comentario, Usuario $administrador): Articulo
    {
        $articulo = DB::transaction(function () use ($articulo, $comentario) {
            $articulo = Articulo::whereKey($articulo->getKey())->lockForUpdate()->firstOrFail();
            $this->exigirEnRevision($articulo);

            $articulo->forceFill([
                'arti_status' => EstatusArticulo::RECHAZADO,
                'arti_comentario_rechazo' => $comentario,
                'arti_fecha_modificacion' => today(),
            ])->save();

            return $articulo;
        });

        Log::channel('admin')->info('Artículo rechazado', [
            'administrador' => $administrador->usua_nombre_usuario,
            'arti_id_autor' => $articulo->arti_id_autor,
            'titulo' => $articulo->arti_titulo,
        ]);

        if ($principal = $this->principalDe($articulo)) {
            $correo = $principal->persona?->correo?->comu_valor_comunicacion;
            if ($correo) {
                Mail::to($correo)->queue(new ArticuloRechazado(
                    $principal->persona->pers_nombre,
                    $articulo->arti_titulo,
                    $comentario,
                ));
            }
        }

        return $articulo;
    }

    /** @throws ValidationException */
    public function exigirEnRevision(Articulo $articulo): void
    {
        if ($articulo->arti_status !== EstatusArticulo::EN_REVISION) {
            throw ValidationException::withMessages([
                'articulo' => 'Este artículo ya fue dictaminado; su estatus no puede cambiar.',
            ]);
        }
    }

    public function principalDe(Articulo $articulo): ?Autor
    {
        return $articulo->autores()
            ->wherePivot('auar_principal', true)
            ->with('persona.correo')
            ->first();
    }

    /** ¿El usuario es autor (principal o coautor) del artículo? */
    public function esAutor(Usuario $usuario, Articulo $articulo): bool
    {
        $usuario->loadMissing('persona.autor');
        $idAutor = $usuario->persona?->autor?->auto_id_autor;

        return $idAutor !== null && AutorArticulo::where('arti_id_autor', $articulo->arti_id_autor)
            ->where('auar_id_autor', $idAutor)
            ->exists();
    }

    /**
     * Registro de autor de quien envía. Los autores registrados ya lo tienen;
     * si quien envía no tiene (p. ej. el administrador), se crea.
     */
    private function autorDe(Usuario $usuario): Autor
    {
        return Autor::firstOrCreate(['pers_id_persona' => $usuario->pers_id_persona]);
    }

    /**
     * Coautor: si su correo ya existe se reutiliza la persona (y su autor, que
     * se crea si no lo tiene); si no, persona + correo (etiqueta Coautor) + autor.
     * Los coautores no reciben cuenta de usuario.
     *
     * @param  array<string, ?string>  $datos
     */
    private function coautor(array $datos): Autor
    {
        $correo = Str::lower(trim((string) $datos['correo']));
        $existente = Comunicacion::correos()->conValor($correo)->first();

        if ($existente && $existente->pers_id_persona) {
            return Autor::firstOrCreate(
                ['pers_id_persona' => $existente->pers_id_persona],
                ['auto_institucion' => $datos['institucion'] ?? null],
            );
        }

        $persona = Persona::create([
            'pers_nombre' => $datos['nombre'],
            'pers_primer_apellido' => $datos['primer_apellido'],
            'pers_segundo_apellido' => $datos['segundo_apellido'] ?? null,
        ]);

        Comunicacion::create([
            'pers_id_persona' => $persona->pers_id_persona,
            'comu_id_tipo_comunicacion' => (int) TipoComunicacion::correo()->value('comu_id_tipo_comunicacion'),
            'comu_valor_comunicacion' => $correo,
            'comu_etiqueta' => 'Coautor',
            'comu_activo' => true,
        ]);

        return Autor::create([
            'pers_id_persona' => $persona->pers_id_persona,
            'auto_institucion' => $datos['institucion'] ?? null,
        ]);
    }
}
