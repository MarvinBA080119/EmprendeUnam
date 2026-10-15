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
     * Artículos de los que el usuario es autor principal.
     */
    public function historialDe(Usuario $usuario, int $porPagina): LengthAwarePaginator
    {
        $usuario->loadMissing('persona.autor');
        $idAutor = $usuario->persona?->autor?->auto_id_autor;

        return Articulo::query()
            ->whereHas('autores', fn ($q) => $q
                ->where('autor.auto_id_autor', $idAutor)
                ->where('autor_articulo.auar_principal', true))
            ->with(['tema', 'constancia'])
            ->orderByDesc('arti_fecha_registro')
            ->orderByDesc('arti_id_autor')
            ->paginate($porPagina);
    }

    /**
     * Todos los artículos, para el administrador (filtros completos en la fase D).
     */
    public function listarTodos(?string $estatus, int $porPagina): LengthAwarePaginator
    {
        return Articulo::query()
            ->when($estatus, fn ($q) => $q->where('arti_status', $estatus))
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

    /** ¿El usuario es el autor principal del artículo? */
    public function esPrincipal(Usuario $usuario, Articulo $articulo): bool
    {
        $usuario->loadMissing('persona.autor');
        $idAutor = $usuario->persona?->autor?->auto_id_autor;

        return $idAutor !== null && AutorArticulo::where('arti_id_autor', $articulo->arti_id_autor)
            ->where('auar_id_autor', $idAutor)
            ->where('auar_principal', true)
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
