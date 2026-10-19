<?php

namespace App\Services;

use App\Models\Articulo;
use App\Models\Curso;
use App\Models\Palabra;
use Illuminate\Support\Facades\DB;

/**
 * Palabras clave de artículos y cursos (RN-03): se normalizan (minúsculas,
 * sin espacios sobrantes) y se reutilizan si ya existen en la tabla palabra.
 */
class PalabraClaveService
{
    public function normalizar(string $palabra): string
    {
        return mb_strtolower(trim((string) preg_replace('/\s+/u', ' ', $palabra)));
    }

    /**
     * @param  list<string>  $palabras
     * @return list<string> normalizadas, sin vacías ni repetidas, en el orden recibido
     */
    public function normalizarLista(array $palabras): array
    {
        return array_values(array_unique(array_filter(
            array_map(fn ($p) => $this->normalizar((string) $p), $palabras),
            fn (string $p) => $p !== '',
        )));
    }

    /**
     * Devuelve los ids de las palabras, creando las que no existan.
     * Usa ON CONFLICT sobre el índice único uq_palabra_descripcion
     * (lower(pala_descripcion)) para no fallar si otra petición la crea a la vez.
     *
     * @param  list<string>  $palabras
     * @return list<int>
     */
    public function idsDe(array $palabras): array
    {
        $ids = [];

        foreach ($this->normalizarLista($palabras) as $palabra) {
            DB::insert(
                'insert into palabra (pala_descripcion) values (?) on conflict ((lower(pala_descripcion))) do nothing',
                [$palabra],
            );

            $ids[] = (int) Palabra::whereRaw('lower(pala_descripcion) = lower(?)', [$palabra])
                ->value('palabra_id_palabra');
        }

        return $ids;
    }

    /**
     * Liga las palabras a un artículo (palabra_articulo). El trigger
     * tr_max_palabras_articulo rechaza más de 5.
     *
     * @param  list<string>  $palabras
     */
    public function asignarAArticulo(Articulo $articulo, array $palabras): void
    {
        $articulo->palabras()->attach($this->idsDe($palabras));
    }

    /**
     * Reemplaza las palabras de un curso (palabra_curso, máximo 5).
     *
     * @param  list<string>  $palabras
     */
    public function asignarACurso(Curso $curso, array $palabras): void
    {
        $curso->palabras()->sync($this->idsDe($palabras));
    }
}
