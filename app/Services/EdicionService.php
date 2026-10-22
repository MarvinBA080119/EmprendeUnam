<?php

namespace App\Services;

use App\Enums\EstatusArticulo;
use App\Models\Articulo;
use App\Models\ArticuloRevista;
use App\Models\Curso;
use App\Models\Revista;
use App\Models\Usuario;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Gestor de la portada del sitio: números de la revista (portada, PDF
 * completo y Flipbook), sus artículos, los 5 que se ven en el índice y los
 * cursos de cada uno.
 *
 * Los archivos se guardan fuera de public/ (disco local, nombres aleatorios)
 * y se entregan con EdicionArchivoController solo si el número es visible.
 */
class EdicionService
{
    /** Cursos sugeridos por artículo cuando el administrador no ha elegido. */
    public const MAX_SUGERIDOS = 3;

    /** Palabras que no sirven para relacionar artículos con cursos. */
    private const VACIAS = [
        'para', 'como', 'desde', 'hasta', 'sobre', 'entre', 'esta', 'este', 'estos', 'estas', 'cada', 'pero',
        'donde', 'cuando', 'porque', 'mediante', 'segun', 'sin', 'con', 'una', 'uno', 'unos', 'unas', 'los',
        'las', 'del', 'que', 'por', 'sus', 'mas', 'muy', 'ante', 'bajo', 'tras', 'otro', 'otra', 'curso',
        'cursos', 'diplomado', 'taller', 'unam', 'empresa', 'empresas', 'nuevo', 'nueva',
    ];

    public function __construct(private BuscadorService $buscador) {}

    /** @return Collection<int, Revista> */
    public function numeros(): Collection
    {
        return Revista::query()
            ->withCount(['articulos', 'articulos as en_indice_count' => fn ($q) => $q->whereNotNull('arre_posicion_index')])
            ->orderByDesc('revi_fecha_publicacion')
            ->orderByDesc('revi_id_revista')
            ->get();
    }

    /**
     * Alta o cambio de un número. La portada y el PDF son obligatorios al
     * crear; al editar, solo se reemplazan si se suben de nuevo.
     *
     * @param  array<string, mixed>  $datos
     */
    public function guardarNumero(array $datos, Usuario $administrador, ?Revista $revista = null, ?UploadedFile $portada = null, ?UploadedFile $pdf = null): Revista
    {
        $revista ??= new Revista(['usua_id_usuario' => $administrador->usua_id_usuario]);
        $nuevos = [];
        $anteriores = [];

        try {
            DB::transaction(function () use ($revista, $datos, $portada, $pdf, &$nuevos, &$anteriores) {
                $visible = (bool) ($datos['visible'] ?? false);

                $revista->fill([
                    'revi_titulo' => $datos['titulo'],
                    'revi_num_edicion' => $datos['numero'],
                    'revi_anio' => $datos['anio'],
                    'revi_mes' => $datos['periodo'],
                    'revi_fecha_publicacion' => $datos['fecha_publicacion'],
                    'revi_url_flipbook' => $datos['flipbook'] ?? null,
                    'revi_visible' => $visible,
                ]);

                // Un número oculto deja de estar en portada (ck_revi_portada_visible).
                if (! $visible) {
                    $revista->forceFill(['revi_en_portada' => false]);
                }

                foreach (['revi_url_portada' => $portada, 'revi_url_revista_pdf' => $pdf] as $columna => $archivo) {
                    if ($archivo) {
                        $anteriores[] = $revista->getOriginal($columna);
                        $nuevos[] = $revista->{$columna} = $this->guardarArchivo($archivo, 'numeros');
                    }
                }

                $revista->save();
            });
        } catch (Throwable $e) {
            Storage::disk('local')->delete($nuevos);

            throw $e;
        }

        Storage::disk('local')->delete(array_filter($anteriores));
        $this->registrar('Número guardado', $administrador, ['revista' => $revista->revi_id_revista]);

        return $revista;
    }

    /** Lo pone en el inicio del sitio y quita al que estaba (uno solo: uq_revista_en_portada). */
    public function ponerEnPortada(Revista $revista, Usuario $administrador): void
    {
        if (! $revista->revi_visible) {
            throw ValidationException::withMessages(['revista' => 'Primero marca el número como visible.']);
        }

        DB::transaction(function () use ($revista) {
            Revista::where('revi_en_portada', true)->whereKeyNot($revista->getKey())->update(['revi_en_portada' => false]);
            $revista->forceFill(['revi_en_portada' => true])->save();
        });

        $this->registrar('Número en portada', $administrador, ['revista' => $revista->revi_id_revista]);
    }

    public function eliminarNumero(Revista $revista, Usuario $administrador): void
    {
        $archivos = [$revista->revi_url_portada, $revista->revi_url_revista_pdf];
        foreach ($revista->articulos as $entrada) {
            array_push($archivos, $entrada->arre_url_pdf, $entrada->arre_url_imagen);
        }

        // articulo_revista y articulo_curso se borran en cascada.
        $revista->delete();

        Storage::disk('local')->delete(array_filter($archivos));
        $this->registrar('Número eliminado', $administrador, ['revista' => $revista->revi_id_revista]);
    }

    /** @return Collection<int, Articulo> artículos aprobados que aún no están en el número */
    public function aprobadosDisponibles(Revista $revista, ?ArticuloRevista $actual = null): Collection
    {
        return Articulo::aprobados()
            ->whereNotIn('arti_id_autor', $revista->articulos()
                ->whereNotNull('arti_id_autor')
                ->when($actual?->exists, fn ($q) => $q->whereKeyNot($actual->getKey()))
                ->pluck('arti_id_autor'))
            ->orderByDesc('arti_fecha_modificacion')
            ->get(['arti_id_autor', 'arti_titulo']);
    }

    /**
     * Alta o cambio de un artículo del número, con su PDF, imagen y los cursos
     * que eligió el administrador.
     *
     * @param  array<string, mixed>  $datos  incluye cursos (elegidos) y sugeridos (mostrados como sugerencia)
     */
    public function guardarArticulo(Revista $revista, array $datos, Usuario $administrador, ?ArticuloRevista $entrada = null, ?UploadedFile $pdf = null, ?UploadedFile $imagen = null): ArticuloRevista
    {
        $entrada ??= new ArticuloRevista(['revi_id_revista' => $revista->revi_id_revista]);
        $sistema = ! empty($datos['arti_id_autor']) ? Articulo::find($datos['arti_id_autor']) : null;

        if ($sistema && $sistema->arti_status !== EstatusArticulo::APROBADO) {
            throw ValidationException::withMessages(['arti_id_autor' => 'Solo un artículo aprobado puede formar parte de un número.']);
        }

        $nuevos = [];
        $anteriores = [];

        try {
            DB::transaction(function () use ($entrada, $datos, $sistema, $pdf, $imagen, &$nuevos, &$anteriores) {
                $entrada->fill([
                    'arti_id_autor' => $sistema?->arti_id_autor,
                    'arre_seccion' => $datos['seccion'] ?? null,
                    'arre_titulo' => ($datos['titulo'] ?? null) ?: $sistema?->arti_titulo,
                    'arre_descripcion' => $datos['descripcion'] ?? null,
                    'arre_autores' => ($datos['autores'] ?? null) ?: ($sistema ? $this->autoresDe($sistema) : ''),
                    'arre_paginas' => $datos['paginas'] ?? null,
                ]);

                foreach (['arre_url_pdf' => $pdf, 'arre_url_imagen' => $imagen] as $columna => $archivo) {
                    if ($archivo) {
                        $anteriores[] = $entrada->getOriginal($columna);
                        $nuevos[] = $entrada->{$columna} = $this->guardarArchivo($archivo, 'articulos');
                    }
                }

                $entrada->save();
                $this->guardarCursos($entrada, $datos['cursos'] ?? [], $datos['sugeridos'] ?? []);
            });
        } catch (Throwable $e) {
            Storage::disk('local')->delete($nuevos);

            throw $e;
        }

        Storage::disk('local')->delete(array_filter($anteriores));
        $this->registrar('Artículo del número guardado', $administrador, ['articulo_revista' => $entrada->arre_id_articulo_revista]);

        return $entrada;
    }

    public function eliminarArticulo(ArticuloRevista $entrada, Usuario $administrador): void
    {
        $archivos = array_filter([$entrada->arre_url_pdf, $entrada->arre_url_imagen]);
        $entrada->delete();
        Storage::disk('local')->delete($archivos);

        $this->registrar('Artículo del número eliminado', $administrador, ['articulo_revista' => $entrada->arre_id_articulo_revista]);
    }

    /**
     * Guarda qué artículos se ven en el índice y en qué posición. Deben ser
     * exactamente 5 (o todos, si el número tiene menos). Los que entran hoy
     * reciben la fecha; los que siguen conservan la suya.
     *
     * @param  array<int|string, int|string|null>  $posiciones  arre_id => posición (1 a 5) o vacío
     */
    public function guardarIndice(Revista $revista, array $posiciones, Usuario $administrador): void
    {
        $entradas = $revista->articulos()->get()->keyBy('arre_id_articulo_revista');
        $elegidas = collect($posiciones)
            ->map(fn ($p) => $p === null || $p === '' ? null : (int) $p)
            ->filter()
            ->only($entradas->keys()->all());

        $requeridas = min(ArticuloRevista::POSICIONES, $entradas->count());

        if ($elegidas->count() !== $requeridas) {
            throw ValidationException::withMessages([
                'posiciones' => "Elige exactamente {$requeridas} artículos para el índice (llevas {$elegidas->count()}).",
            ]);
        }
        if ($elegidas->contains(fn ($p) => $p < 1 || $p > ArticuloRevista::POSICIONES)) {
            throw ValidationException::withMessages(['posiciones' => 'Las posiciones van de 1 a '.ArticuloRevista::POSICIONES.'.']);
        }
        if ($elegidas->unique()->count() !== $elegidas->count()) {
            throw ValidationException::withMessages(['posiciones' => 'Cada posición del índice se usa una sola vez.']);
        }

        // uq_arre_posicion es diferible: se revisa al confirmar, así se pueden intercambiar posiciones.
        DB::transaction(function () use ($entradas, $elegidas) {
            foreach ($entradas as $id => $entrada) {
                $posicion = $elegidas->get($id);
                $entrada->forceFill([
                    'arre_posicion_index' => $posicion,
                    'arre_fecha_index' => $posicion === null ? $entrada->arre_fecha_index
                        : ($entrada->arre_posicion_index === null ? today() : $entrada->arre_fecha_index),
                ])->save();
            }
        });

        $this->registrar('Índice del número actualizado', $administrador, [
            'revista' => $revista->revi_id_revista,
            'posiciones' => $elegidas->all(),
        ]);
    }

    /**
     * Cursos vigentes sugeridos para un artículo: comparten palabras clave con
     * el artículo del sistema o palabras del título, la sección y la
     * descripción con el nombre o las palabras clave del curso.
     *
     * @return Collection<int, Curso> ordenados por coincidencias
     */
    public function sugeridos(ArticuloRevista $entrada): Collection
    {
        $palabrasArticulo = $entrada->arti_id_autor
            ? $entrada->articulo?->palabras->pluck('pala_descripcion')->map(fn ($p) => BuscadorService::normalizar($p))->all() ?? []
            : [];
        $terminos = $this->terminos(implode(' ', [$entrada->arre_titulo, $entrada->arre_seccion, $entrada->arre_descripcion]));

        return Curso::vigentes()->with('palabras')->get()
            ->map(function (Curso $curso) use ($palabrasArticulo, $terminos) {
                $palabrasCurso = $curso->palabras->pluck('pala_descripcion')->map(fn ($p) => BuscadorService::normalizar($p))->all();
                $terminosCurso = $this->terminos($curso->curs_nombre_curso.' '.implode(' ', $palabrasCurso));
                $curso->setAttribute('coincidencias',
                    count(array_intersect($palabrasArticulo, $palabrasCurso)) * 2 + count(array_intersect($terminos, $terminosCurso)));

                return $curso;
            })
            ->filter(fn (Curso $curso) => $curso->coincidencias > 0)
            ->sortByDesc('coincidencias')
            ->take(self::MAX_SUGERIDOS)
            ->values();
    }

    /**
     * Cursos que se muestran con el artículo: los que agregó el administrador
     * más los sugeridos que no descartó. Solo cursos vigentes.
     *
     * @return Collection<int, Curso>
     */
    public function cursosDe(ArticuloRevista $entrada): Collection
    {
        $entrada->loadMissing('cursos');
        $agregados = $entrada->cursos->filter(fn ($c) => $c->pivot->arcu_incluido);
        $descartados = $entrada->cursos->reject(fn ($c) => $c->pivot->arcu_incluido)->pluck('curs_id_curso');

        return $agregados
            ->merge($this->sugeridos($entrada)->reject(fn ($c) => $descartados->contains($c->curs_id_curso)))
            ->filter(fn (Curso $c) => $c->curs_fecha === null || $c->curs_fecha->gte(today()))
            ->unique('curs_id_curso')
            ->values();
    }

    /**
     * Guarda en articulo_curso lo que decidió el administrador: un curso
     * marcado que no se sugiere queda incluido; un sugerido que vio en el
     * formulario y desmarcó queda descartado. Una sugerencia nueva que aún no
     * vio (por ejemplo, al crear el artículo) no se descarta.
     *
     * @param  list<int>  $elegidos
     * @param  list<int>  $sugeridosMostrados  cursos marcados como «sugerido» en el formulario
     */
    public function guardarCursos(ArticuloRevista $entrada, array $elegidos, array $sugeridosMostrados = []): void
    {
        $elegidos = array_map('intval', $elegidos);
        $sugeridos = $this->sugeridos($entrada)->pluck('curs_id_curso')->all();
        $filas = [];

        foreach ($elegidos as $curso) {
            // Un sugerido marcado no necesita fila: ya se muestra solo.
            if (! in_array($curso, $sugeridos, true)) {
                $filas[$curso] = ['arcu_incluido' => true];
            }
        }
        foreach (array_map('intval', $sugeridosMostrados) as $curso) {
            if (! in_array($curso, $elegidos, true)) {
                $filas[$curso] = ['arcu_incluido' => false];
            }
        }

        $entrada->cursos()->sync($filas);
        $entrada->unsetRelation('cursos');
    }

    /**
     * Datos del número en portada para el inicio del sitio, o null si no hay
     * número en portada con artículos en el índice.
     *
     * @return array{revista: Revista, principal: ?ArticuloRevista, resto: Collection<int, ArticuloRevista>}|null
     */
    public function portada(): ?array
    {
        $revista = Revista::visibles()->where('revi_en_portada', true)->first();
        if ($revista === null) {
            return null;
        }

        $indice = $revista->articulos()->whereNotNull('arre_posicion_index')
            ->with(['articulo.palabras', 'cursos'])
            ->orderBy('arre_posicion_index')
            ->get();

        if ($indice->isEmpty()) {
            return null;
        }

        return ['revista' => $revista, 'principal' => $indice->first(), 'resto' => $indice->slice(1)->values()];
    }

    /** «Nombre Apellido, Otra Persona» del artículo del sistema (el inicio pone una por renglón). */
    private function autoresDe(Articulo $articulo): string
    {
        return $articulo->autores()->with('persona')
            ->orderByDesc('autor_articulo.auar_principal')
            ->get()
            ->map(fn ($a) => $a->persona?->nombre_completo)
            ->filter()
            ->join(', ');
    }

    /** @return list<string> palabras normalizadas de 4 letras o más, sin las vacías */
    private function terminos(string $texto): array
    {
        return array_values(array_unique(array_filter(
            $this->buscador->terminos($texto),
            fn (string $t) => mb_strlen($t) >= 4 && ! in_array($t, self::VACIAS, true),
        )));
    }

    /** Nombre aleatorio con la extensión que corresponde al contenido real del archivo. */
    private function guardarArchivo(UploadedFile $archivo, string $carpeta): string
    {
        $extension = match ($archivo->getMimeType()) {
            'application/pdf' => 'pdf',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => throw ValidationException::withMessages(['archivo' => 'Tipo de archivo no permitido.']),
        };

        $ruta = config('emprende.carpeta_ediciones')."/{$carpeta}/".Str::random(40).".{$extension}";
        Storage::disk('local')->putFileAs(dirname($ruta), $archivo, basename($ruta));

        return $ruta;
    }

    /** @param  array<string, mixed>  $datos */
    private function registrar(string $accion, Usuario $administrador, array $datos): void
    {
        Log::channel('admin')->info($accion, ['administrador' => $administrador->usua_nombre_usuario] + $datos);
    }
}
