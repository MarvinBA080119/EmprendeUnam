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
     * Artículos aprobados que aún no forman parte de ningún número: los que el
     * equipo editorial descarga para maquetar.
     *
     * @return Collection<int, Articulo>
     */
    public function aprobadosSinNumero(): Collection
    {
        return Articulo::aprobados()
            ->whereDoesntHave('entradasRevista')
            ->orderBy('arti_id_autor')
            ->get(['arti_id_autor', 'arti_titulo', 'arti_url_pdf']);
    }

    /** Nombre con el que viaja el manuscrito: «12-titulo-del-articulo.docx». */
    public static function nombreManuscrito(Articulo $articulo): string
    {
        $extension = strtolower(pathinfo($articulo->arti_url_pdf, PATHINFO_EXTENSION)) ?: 'pdf';

        return $articulo->arti_id_autor.'-'.Str::limit(Str::slug($articulo->arti_titulo), 60, '').'.'.$extension;
    }

    /**
     * .zip temporal con los manuscritos de los artículos aprobados sin número.
     * Cada nombre empieza con el id del artículo: si el PDF maquetado conserva
     * ese número al inicio, la carga por .zip lo vincula sola.
     *
     * @return string|null ruta del .zip (el que llama lo borra) o null si no hay manuscritos
     */
    public function zipManuscritos(): ?string
    {
        $articulos = $this->aprobadosSinNumero()
            ->filter(fn (Articulo $a) => Storage::disk('local')->exists($a->arti_url_pdf));
        if ($articulos->isEmpty()) {
            return null;
        }

        $ruta = tempnam(sys_get_temp_dir(), 'manuscritos');
        $zip = new \ZipArchive;
        $zip->open($ruta, \ZipArchive::OVERWRITE);
        foreach ($articulos as $articulo) {
            $zip->addFile(Storage::disk('local')->path($articulo->arti_url_pdf), self::nombreManuscrito($articulo));
        }
        $zip->close();

        return $ruta;
    }

    /**
     * Carga un .zip con los PDF maquetados de los artículos del número y vincula
     * cada uno con su artículo:
     *  1. por el número al inicio del nombre («12-…pdf» = artículo 12 del sistema,
     *     que debe estar aprobado; si aún no está en el número, se agrega con su
     *     título, autores, tema y sus 5 palabras clave);
     *  2. si no, por el título (nombre del archivo y título iguales al quitar
     *     acentos, mayúsculas y signos), entre los artículos del número y los
     *     aprobados sin número.
     * Solo se aceptan PDF reales; se ignoran carpetas, rutas y archivos ocultos.
     *
     * @return array{vinculados: list<string>, agregados: list<string>, no_reconocidos: list<string>}
     */
    public function cargarZip(Revista $revista, UploadedFile $archivo, Usuario $administrador): array
    {
        $zip = new \ZipArchive;
        if ($zip->open($archivo->getRealPath(), \ZipArchive::RDONLY) !== true) {
            throw ValidationException::withMessages(['zip' => 'No se pudo abrir el .zip. Vuelve a comprimir los PDF.']);
        }

        $maxPdf = (int) config('emprende.zip_pdf_max_kb') * 1024;
        $resultado = ['vinculados' => [], 'agregados' => [], 'no_reconocidos' => []];
        $nuevos = [];
        $anteriores = [];

        try {
            $entradas = [];
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $info = $zip->statIndex($i);
                $nombre = basename(str_replace('\\', '/', (string) $info['name']));
                if (str_ends_with((string) $info['name'], '/') || $nombre === '' || str_starts_with($nombre, '.')
                    || str_contains((string) $info['name'], '__MACOSX')) {
                    continue;
                }
                if (strtolower(pathinfo($nombre, PATHINFO_EXTENSION)) !== 'pdf') {
                    $resultado['no_reconocidos'][] = "{$nombre} (no es PDF)";

                    continue;
                }
                $entradas[] = [$i, $nombre, (int) $info['size']];
            }

            if (count($entradas) > (int) config('emprende.zip_max_archivos')) {
                throw ValidationException::withMessages(['zip' => 'El .zip trae más de '.config('emprende.zip_max_archivos').' PDF.']);
            }

            DB::transaction(function () use ($revista, $zip, $entradas, $maxPdf, &$resultado, &$nuevos, &$anteriores) {
                foreach ($entradas as [$indice, $nombre, $tamano]) {
                    if ($tamano > $maxPdf) {
                        $resultado['no_reconocidos'][] = "{$nombre} (pesa más de ".round($maxPdf / 1048576).' MB)';

                        continue;
                    }

                    [$entrada, $agregado] = $this->entradaParaArchivo($revista, $nombre);
                    if ($entrada === null) {
                        $resultado['no_reconocidos'][] = $nombre;

                        continue;
                    }

                    $contenido = $this->leerEntrada($zip, $indice, $maxPdf);
                    if ($contenido === null || ! str_starts_with($contenido, '%PDF')) {
                        $resultado['no_reconocidos'][] = "{$nombre} (no es un PDF válido)";

                        continue;
                    }

                    $ruta = config('emprende.carpeta_ediciones').'/articulos/'.Str::random(40).'.pdf';
                    Storage::disk('local')->put($ruta, $contenido);
                    $nuevos[] = $ruta;
                    $anteriores[] = $entrada->arre_url_pdf;
                    $entrada->forceFill(['arre_url_pdf' => $ruta])->save();

                    $resultado[$agregado ? 'agregados' : 'vinculados'][] = "{$nombre} → {$entrada->arre_titulo}";
                }
            });
        } catch (Throwable $e) {
            Storage::disk('local')->delete($nuevos);

            throw $e;
        } finally {
            $zip->close();
        }

        Storage::disk('local')->delete(array_values(array_filter($anteriores)));
        $this->registrar('PDF maquetados cargados por .zip', $administrador, [
            'revista' => $revista->revi_id_revista,
            'vinculados' => count($resultado['vinculados']) + count($resultado['agregados']),
            'no_reconocidos' => count($resultado['no_reconocidos']),
        ]);

        return $resultado;
    }

    /**
     * Entrada del número a la que corresponde un archivo, creándola si el
     * archivo nombra un artículo aprobado que aún no está en el número.
     *
     * @return array{0: ?ArticuloRevista, 1: bool} entrada y si se agregó
     */
    private function entradaParaArchivo(Revista $revista, string $nombre): array
    {
        $base = pathinfo($nombre, PATHINFO_FILENAME);

        if (preg_match('/^(\d+)/', $base, $m)) {
            $articulo = Articulo::aprobados()->with('tema')->find((int) $m[1]);
            if ($articulo) {
                $entrada = $revista->articulos()->where('arti_id_autor', $articulo->arti_id_autor)->first();

                return $entrada ? [$entrada, false] : [$this->agregarDelSistema($revista, $articulo), true];
            }
        }

        $clave = Str::slug(preg_replace('/^\d+[\s._-]*/', '', $base));
        if ($clave === '') {
            return [null, false];
        }

        $entrada = $revista->articulos()->get()->first(fn ($e) => Str::slug($e->arre_titulo) === $clave);
        if ($entrada) {
            return [$entrada, false];
        }

        $articulo = $this->aprobadosSinNumero()->first(fn ($a) => Str::slug($a->arti_titulo) === $clave);

        return $articulo ? [$this->agregarDelSistema($revista, $articulo->load('tema')), true] : [null, false];
    }

    private function agregarDelSistema(Revista $revista, Articulo $articulo): ArticuloRevista
    {
        return ArticuloRevista::create([
            'revi_id_revista' => $revista->revi_id_revista,
            'arti_id_autor' => $articulo->arti_id_autor,
            'arre_titulo' => $articulo->arti_titulo,
            'arre_autores' => $this->autoresDe($articulo),
            'arre_seccion' => $articulo->tema?->tema_nombre,
            'arre_descripcion' => $articulo->arti_resumen ?: null,
        ]);
    }

    /** Contenido de una entrada del .zip, leído con tope (no se confía en el tamaño declarado). */
    private function leerEntrada(\ZipArchive $zip, int $indice, int $maximo): ?string
    {
        $flujo = $zip->getStream((string) $zip->getNameIndex($indice));
        if ($flujo === false) {
            return null;
        }
        $contenido = stream_get_contents($flujo, $maximo + 1);
        fclose($flujo);

        return $contenido === false || strlen($contenido) > $maximo ? null : $contenido;
    }

    /**
     * Número principal (en portada) para el inicio del sitio, con sus
     * destacados en orden; principal y resto quedan vacíos mientras no se
     * elijan. Null si ningún número está en portada.
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

        return ['revista' => $revista, 'principal' => $indice->first(), 'resto' => $indice->slice(1)->values()];
    }

    /**
     * Números visibles que ya no son el principal: pasan al histórico con su
     * portada, su PDF completo y su eBook. El más reciente primero.
     *
     * @return Collection<int, Revista>
     */
    public function historicos(): Collection
    {
        return Revista::visibles()->where('revi_en_portada', false)
            ->orderByDesc('revi_fecha_publicacion')
            ->orderByDesc('revi_id_revista')
            ->get();
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
