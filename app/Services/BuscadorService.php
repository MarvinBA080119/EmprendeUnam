<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as Paginador;
use Illuminate\Support\Facades\DB;

/**
 * Buscador público (6.9): artículos APROBADOS (título, palabras clave, autores,
 * coautores, tema y resumen) y cursos vigentes (nombre, institución y palabras
 * clave).
 *
 * Usa búsqueda de texto completo de PostgreSQL con la configuración "spanish".
 * Mientras no se autorice la extensión unaccent, los acentos se quitan con
 * translate() en ambos lados de la comparación.
 */
class BuscadorService
{
    private const CON_ACENTO = 'áéíóúäëïöüàèìòùâêîôûñç';

    private const SIN_ACENTO = 'aeiouaeiouaeiouaeiounc';

    private const MAX_TERMINOS = 8;

    /**
     * Expresión SQL: el texto en minúsculas y sin acentos.
     * $expresion debe ser una columna o expresión fija, NUNCA texto del usuario
     * (el texto del usuario va como binding: ver patronLike()).
     */
    public static function sinAcentos(string $expresion): string
    {
        return "translate(lower({$expresion}), '".self::CON_ACENTO."', '".self::SIN_ACENTO."')";
    }

    /** Patrón LIKE ("%texto%") del texto del usuario, en minúsculas y sin acentos. */
    public static function patronLike(string $texto): string
    {
        return '%'.addcslashes(self::normalizar($texto), '%_\\').'%';
    }

    public static function normalizar(string $texto): string
    {
        return strtr(mb_strtolower(trim($texto)), self::mapaAcentos());
    }

    /**
     * Términos de búsqueda: minúsculas, sin acentos y solo letras y números.
     *
     * @return list<string>
     */
    public function terminos(?string $texto): array
    {
        $limpio = preg_replace('/[^a-z0-9]+/', ' ', self::normalizar((string) $texto));

        return array_slice(array_values(array_unique(array_filter(explode(' ', $limpio)))), 0, self::MAX_TERMINOS);
    }

    /**
     * @param  array{q?: ?string, tipo?: ?string, tema?: int|string|null, anio?: int|string|null}  $filtros
     */
    public function buscar(array $filtros, int $pagina = 1, int $porPagina = 10): LengthAwarePaginator
    {
        $terminos = $this->terminos($filtros['q'] ?? null);
        $tipo = $filtros['tipo'] ?? null;
        $tema = $filtros['tema'] ?? null;
        $anio = $filtros['anio'] ?? null;
        $pagina = max(1, $pagina);

        $partes = [];
        $bindings = [];

        if ($tipo !== 'curso') {
            $sql = "select 'articulo' as tipo, a.arti_id_autor as id, a.arti_titulo as titulo,
                           t.tema_nombre::text as tema,
                           extract(year from a.arti_fecha_modificacion)::int as anio,
                           a.arti_fecha_modificacion as fecha,
                           (select string_agg(p.pala_descripcion, ', ' order by p.pala_descripcion)
                              from palabra_articulo pa
                              join palabra p on p.palabra_id_palabra = pa.palabra_id_palabra
                             where pa.arti_id_autor = a.arti_id_autor) as palabras,
                           (select string_agg(concat_ws(' ', pe.pers_nombre, pe.pers_primer_apellido, pe.pers_segundo_apellido), ', '
                                              order by aa.auar_principal desc, aa.auar_id_autor_articulo)
                              from autor_articulo aa
                              join autor au on au.auto_id_autor = aa.auar_id_autor
                              join persona pe on pe.pers_id_persona = au.pers_id_persona
                             where aa.arti_id_autor = a.arti_id_autor) as autores,
                           null::text as institucion,
                           null::text as url,
                           a.arti_resumen::text as resumen
                      from articulo a
                      join tema t on t.tema_id_tema = a.tema_id_tema
                     where a.arti_status = 'APROBADO'";
            if ($tema) {
                $sql .= ' and a.tema_id_tema = ?';
                $bindings[] = (int) $tema;
            }
            if ($anio) {
                $sql .= ' and extract(year from a.arti_fecha_modificacion) = ?';
                $bindings[] = (int) $anio;
            }
            $partes[] = $sql;
        }

        // El tema clasifica solo artículos: con tema elegido no se muestran cursos.
        if ($tipo !== 'articulo' && ! $tema) {
            $sql = "select 'curso' as tipo, c.curs_id_curso as id, c.curs_nombre_curso as titulo,
                           null::text as tema,
                           extract(year from c.curs_fecha)::int as anio,
                           c.curs_fecha as fecha,
                           (select string_agg(p.pala_descripcion, ', ' order by p.pala_descripcion)
                              from palabra_curso pc
                              join palabra p on p.palabra_id_palabra = pc.palabra_id_palabra
                             where pc.curs_id_curso = c.curs_id_curso) as palabras,
                           null::text as autores,
                           c.curs_institucion::text as institucion,
                           c.curs_url::text as url,
                           null::text as resumen
                      from curso c
                     where c.curs_fecha >= current_date";
            if ($anio) {
                $sql .= ' and extract(year from c.curs_fecha) = ?';
                $bindings[] = (int) $anio;
            }
            $partes[] = $sql;
        }

        $union = implode(' union all ', $partes);

        if ($terminos) {
            $documento = "setweight(to_tsvector('spanish', ".self::sinAcentos('titulo')."), 'A')"
                ." || setweight(to_tsvector('spanish', ".self::sinAcentos("coalesce(palabras, '')")."), 'B')"
                ." || setweight(to_tsvector('spanish', ".self::sinAcentos("concat_ws(' ', autores, institucion)")."), 'B')"
                ." || setweight(to_tsvector('spanish', ".self::sinAcentos("coalesce(tema, '')")."), 'C')"
                ." || setweight(to_tsvector('spanish', ".self::sinAcentos("coalesce(resumen, '')")."), 'D')";
            $base = "select r.*, ts_rank_cd(r.documento, q.consulta) as rango
                       from (select u.*, {$documento} as documento from ({$union}) u) r,
                            to_tsquery('spanish', ?) q(consulta)
                      where r.documento @@ q.consulta";
            // Cada término busca también por prefijo ("admin" encuentra "administración").
            $bindings[] = implode(' & ', array_map(fn (string $t) => $t.':*', $terminos));
            $orden = 'rango desc, fecha desc, titulo';
        } else {
            $base = "select u.*, 0 as rango from ({$union}) u";
            $orden = 'fecha desc, titulo';
        }

        $total = (int) DB::scalar("select count(*) from ({$base}) x", $bindings);
        $filas = DB::select(
            "select tipo, id, titulo, tema, anio, fecha, palabras, autores, institucion, url, resumen
               from ({$base}) x order by {$orden} limit ? offset ?",
            [...$bindings, $porPagina, ($pagina - 1) * $porPagina],
        );

        $resultados = array_map(fn (object $f) => [
            'tipo' => $f->tipo,
            'id' => $f->id,
            'tema' => $f->tema,
            'anio' => $f->anio,
            'url' => $f->url,
            'titulo' => $this->resaltar($f->titulo, $terminos),
            'autores' => $this->resaltar($f->autores, $terminos),
            'palabras' => $this->resaltar($f->palabras, $terminos),
            'institucion' => $this->resaltar($f->institucion, $terminos),
            'resumen' => $this->resaltar($f->resumen, $terminos),
        ], $filas);

        return new Paginador($resultados, $total, $porPagina, $pagina);
    }

    /**
     * Años con contenido público (artículos aprobados y cursos vigentes).
     *
     * @return list<int>
     */
    public function aniosDisponibles(): array
    {
        return array_map('intval', DB::table('articulo')
            ->where('arti_status', 'APROBADO')
            ->selectRaw('extract(year from arti_fecha_modificacion)::int as anio')
            ->union(DB::table('curso')->whereDate('curs_fecha', '>=', today())->selectRaw('extract(year from curs_fecha)::int as anio'))
            ->orderByDesc('anio')
            ->pluck('anio')
            ->unique()
            ->values()
            ->all());
    }

    /**
     * Parte un texto en segmentos para resaltar las palabras que empiezan con
     * algún término (sin importar mayúsculas ni acentos). La vista pinta cada
     * segmento con {{ }} o <mark>: nunca se usa v-html.
     *
     * @param  list<string>  $terminos
     * @return list<array{texto: string, marca: bool}>
     */
    public function resaltar(?string $texto, array $terminos): array
    {
        $texto = (string) $texto;
        if ($texto === '') {
            return [];
        }
        if ($terminos === []) {
            return [['texto' => $texto, 'marca' => false]];
        }

        // Versión normalizada con la misma longitud en caracteres que el original.
        $caracteres = mb_str_split($texto);
        $normal = implode('', array_map(
            fn (string $c) => mb_substr(strtr(mb_strtolower($c), self::mapaAcentos()), 0, 1) ?: $c,
            $caracteres,
        ));

        $marcas = array_fill(0, count($caracteres), false);
        $patron = '/(?<![\p{L}\p{N}])(?:'.implode('|', array_map(fn ($t) => preg_quote($t, '/'), $terminos)).')[\p{L}\p{N}]*/u';
        preg_match_all($patron, $normal, $coincidencias, PREG_OFFSET_CAPTURE);
        foreach ($coincidencias[0] as [$palabra, $bytes]) {
            $inicio = mb_strlen(substr($normal, 0, $bytes));
            for ($i = 0, $n = mb_strlen($palabra); $i < $n; $i++) {
                $marcas[$inicio + $i] = true;
            }
        }

        $segmentos = [];
        foreach ($caracteres as $i => $c) {
            $ultimo = array_key_last($segmentos);
            if ($ultimo !== null && $segmentos[$ultimo]['marca'] === $marcas[$i]) {
                $segmentos[$ultimo]['texto'] .= $c;
            } else {
                $segmentos[] = ['texto' => $c, 'marca' => $marcas[$i]];
            }
        }

        return $segmentos;
    }

    /** @return array<string, string> */
    private static function mapaAcentos(): array
    {
        return array_combine(mb_str_split(self::CON_ACENTO), str_split(self::SIN_ACENTO));
    }
}
