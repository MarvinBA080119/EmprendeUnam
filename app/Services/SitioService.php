<?php

namespace App\Services;

/**
 * Contenido del sitio público (config/sitio/*.php, convertido del sitio
 * original). Prepara los datos de cada página: la lógica que antes vivía al
 * inicio de cada archivo .php del sitio.
 */
class SitioService
{
    /** Imágenes de apoyo de «10 de 10», asignadas por posición si el artículo no trae una. */
    private const BANCO_IMAGENES = [
        'img/Gresmex_2026.png',
        'img/supervision.jpeg',
        'img/aires_campo.jpeg',
        'img/artesania_mazahua.jpeg',
        'img/empoderamiento_paciente.jpeg',
    ];

    /** Orden de las pestañas de «10 de 10» (las áreas no listadas van al final). */
    private const ORDEN_DIEZ_DE_DIEZ = ['contaduria', 'administracion', 'informatica', 'negocios-internacionales'];

    /** @return array<string, mixed> config, revista_actual, convocatoria, incluida_en */
    public function general(): array
    {
        return config('sitio.general');
    }

    /** @return array<string, mixed> */
    public function inicio(): array
    {
        $general = $this->general();
        $edicion = config('sitio.edicion');

        // La portada horizontal del inicio sustituye a la del número (como en index.php).
        $general['revista_actual']['portada'] = 'img/portadas/222_horizontal-1_page-0001.jpg';

        // Cada artículo abre su PDF; si no lo tiene, el del número completo.
        $articulos = array_map(fn (array $art) => $art + [
            'pdf_art' => ! empty($art['pdf']) ? $art['pdf'] : $general['revista_actual']['pdf_completo'],
        ], $edicion['tabla_contenidos']);

        return $general + [
            'articulo_principal' => $edicion['articulo_principal'],
            'tabla_contenidos' => $articulos,
            // La portada se revela poco a poco solo en el número cero.
            'revelar_portada' => (int) $general['revista_actual']['edicion'] === 0,
        ];
    }

    /** @return array<string, mixed> */
    public function historico(): array
    {
        $historico = config('sitio.historico.historico');
        $categorias = config('sitio.tematicas.categorias');
        $porEdicion = config('sitio.tematicas_ediciones.tematicas_por_edicion');

        $anios = array_values(array_unique(array_column($historico, 'anio')));
        rsort($anios);

        // Solo las áreas con alguna edición en el acervo (una opción sin resultados es ruido).
        $conEdiciones = [];
        foreach ($historico as $ed) {
            foreach ($porEdicion[(int) $ed['numero']] ?? [] as $slug) {
                $conEdiciones[$slug] = true;
            }
        }
        $temas = [];
        foreach ($categorias as $slug => $cat) {
            if (isset($conEdiciones[$slug])) {
                $temas[$slug] = $cat['nombre'];
            }
        }

        $ediciones = array_map(function (array $ed) use ($porEdicion, $categorias) {
            $num = (int) $ed['numero'];
            $tieneTitulo = isset($ed['titulo']) && trim($ed['titulo']) !== '';
            $slugs = $porEdicion[$num] ?? [];
            $nombres = array_values(array_filter(array_map(fn ($s) => $categorias[$s]['nombre'] ?? null, $slugs)));

            return [
                'numero' => $num,
                'anio' => $ed['anio'],
                'periodo' => $ed['periodo'],
                'titulo' => $tieneTitulo ? $ed['titulo'] : 'Emprendedores No. '.$num,
                'tiene_titulo' => $tieneTitulo,
                'portada' => "img/portadas/{$num}.jpg",
                'remoto' => "https://emprendedores.unam.mx/img/portadas/{$num}.jpg",
                'pdf' => "pdf/{$num}_emprendedores.pdf",
                'ebook' => "https://repositorios.fca.unam.mx/pruebaRevistaEmprendedores/emprendedores{$num}/",
                // El filtro comprueba pertenencia dentro de la lista: un número toca varias áreas.
                'attr_temas' => $slugs ? '|'.implode('|', $slugs).'|' : '',
                'nombres_temas' => $nombres,
            ];
        }, $historico);

        return compact('anios', 'temas', 'ediciones');
    }

    /** @return array<string, mixed> */
    public function tematicas(?string $categoria): array
    {
        $categorias = config('sitio.tematicas.categorias');
        $activa = isset($categorias[$categoria]) ? $categoria : array_key_first($categorias);
        $datos = $categorias[$activa];

        // Se agrupa por número: el rótulo de edición se pinta al cambiar de número.
        $previo = null;
        $articulos = array_map(function (array $art) use (&$previo) {
            $nuevo = $art['numero'] !== $previo;
            $previo = $art['numero'];

            return $art + ['nuevo_numero' => $nuevo];
        }, $datos['articulos']);

        return [
            'categorias' => $categorias,
            'cat_activa' => $activa,
            'datos_cat' => $datos,
            'articulos' => $articulos,
            'n_art' => count($articulos),
        ];
    }

    /** @return array<string, mixed> */
    public function podcasts(): array
    {
        $multimedia = config('sitio.multimedia');
        $youtube = $multimedia['youtube_podcasts'];
        $podcasts = $multimedia['podcasts'];

        // Episodio destacado: el marcado como activo, o el primero.
        $destacado = $podcasts[0];
        foreach ($podcasts as $p) {
            if (! empty($p['activo'])) {
                $destacado = $p;
                break;
            }
        }

        $episodios = array_map(function (array $ep) use ($youtube) {
            $idYoutube = $youtube[$ep['id']] ?? '';

            return $ep + [
                'id_youtube' => $idYoutube,
                // Miniatura oficial de YouTube; si no hay video, la imagen del episodio.
                'portada' => $idYoutube !== '' ? "https://i.ytimg.com/vi/{$idYoutube}/maxresdefault.jpg" : $ep['imagen'],
            ];
        }, $podcasts);

        return [
            'podcasts' => $episodios,
            'destacado' => $destacado,
            'id_destacado' => $youtube[$destacado['id']] ?? '',
            'spotify_id' => trim($destacado['spotify'] ?? ''),
            'meta_ep' => array_values(array_filter([$destacado['fecha'], $destacado['duracion']], fn ($v) => $v !== '')),
        ];
    }

    /** @return array<string, mixed> */
    public function webinar(): array
    {
        $multimedia = config('sitio.multimedia');
        $youtube = $multimedia['youtube_webinar'];

        return [
            'webinar_principal' => $multimedia['webinar_principal'],
            'id_principal' => $youtube['principal'] ?? '',
            'mas_webinars' => array_map(
                fn (array $w, $i) => $w + ['id_youtube' => $youtube[$i] ?? ''],
                $multimedia['mas_webinars'],
                array_keys($multimedia['mas_webinars']),
            ),
        ];
    }

    /** @return list<array{id: string, label: string, resumen: string, items: list<array<string, mixed>>}> */
    public function diezDeDiez(): array
    {
        $bloques = [];
        foreach (config('sitio.diezdediez') as $slug => $bloque) {
            $items = $bloque['articulos'] ?? [];
            foreach ($items as $pos => $art) {
                // La imagen propia si viene informada; si no, una del banco según su posición.
                $items[$pos]['imagen'] = ! empty($art['imagen'])
                    ? $art['imagen']
                    : self::BANCO_IMAGENES[$pos % count(self::BANCO_IMAGENES)];
            }
            $bloques[$slug] = [
                'id' => $slug,
                'label' => $bloque['titulo_tematica'],
                'resumen' => $bloque['descripcion_tematica'] ?? '',
                'items' => $items,
            ];
        }

        $areas = [];
        foreach (self::ORDEN_DIEZ_DE_DIEZ as $slug) {
            if (isset($bloques[$slug])) {
                $areas[] = $bloques[$slug];
                unset($bloques[$slug]);
            }
        }

        return array_merge($areas, array_values($bloques));
    }

    /** @return array<string, mixed>|null el área de «10 de 10» o null si no existe */
    public function diezDeDiezArea(string $slug): ?array
    {
        return config("sitio.diezdediez.{$slug}");
    }

    /** @return list<array<string, mixed>> */
    public function pymes(): array
    {
        return config('sitio.pymes.casos_pyme');
    }

    /** @return array<string, mixed> */
    public function acerca(): array
    {
        return config('sitio.acerca');
    }

    /** @return array<string, mixed> */
    public function lineamientos(): array
    {
        return config('sitio.lineamientos');
    }

    /** Consultas resueltas antes del sistema (archivo estático del consultorio). */
    public function consultasAnteriores(): array
    {
        return config('sitio.consultorio.consultas_consultorio');
    }
}
