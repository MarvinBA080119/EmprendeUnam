<?php

/*
| Contenido de la edición más reciente (inicio del sitio).
| Convertido del sitio público original (datos-articulos.php) sin cambiar su contenido.
*/

/**
 * Contenidos de la edición más reciente
 * --------------------------------------------------------------
 * $articulo_principal : artículo destacado de la página de inicio
 * $tabla_contenidos   : artículos de la sección "En este número"
 *
 * Cada artículo incluye una descripción breve (aprox. 20 palabras)
 * y el nombre de la persona autora, que se muestra en línea aparte.
 *
 * PHP 5.3 compatible
 */

// ══════════════════════════════════════════════
// ARTÍCULO PRINCIPAL
// ══════════════════════════════════════════════

return [
    'articulo_principal' => [
        'categoria' => 'TECNOLOGÍA, INNOVACIÓN Y COMPETITIVIDAD',
        'titulo' => 'Gresmex: una empresa que cambiará el mundo',
        'descripcion' => 'Soluciones biotecnológicas para enfrentar amenazas invisibles, transformar industrias tradicionales y generar productos con impacto positivo en salud y medio ambiente.',
        'autores' => 'Julio César Olvera Hernández',
        'imagen' => 'img/Gresmex_2026.png',
        'pdf' => 'pdf/gresmex-una-empresa-que-cambiara-el-mundo.pdf',
    ],

    // ══════════════════════════════════════════════
    // EN ESTE NÚMERO
    // ══════════════════════════════════════════════
    'tabla_contenidos' => [

        [
            'categoria' => 'TALENTO HUMANO',
            'titulo' => 'Alydia: humanización de los servicios de salud',
            'descripcion' => 'Transformación de la atención en salud para personas adultas mayores, priorizando bienestar, autonomía, prevención y acompañamiento ante desafíos del envejecimiento.',
            'autores' => 'Sair Alejandra Martínez Hernández',
            'paginas' => '7-12',
            'pdf' => 'pdf/alydia-humanizacion-de-los-servicios-de-salud.pdf',   // sólo este artículo, separado del número 219
            'imagen' => 'img/empoderamiento_paciente.jpeg',
            'cursos_fca' => [
                ['texto' => 'Licenciatura en Administración - FCA UNAM',             'url' => 'https://www.fca.unam.mx/lic-administracion'],
                ['texto' => 'Diplomado en Gestión de Organizaciones de Salud',       'url' => 'https://www.fca.unam.mx/diplomados'],
            ],
        ],

        [
            'categoria' => 'SUSTENTABILIDAD Y RESPONSABILIDAD SOCIAL',
            'titulo' => 'Aires de campo, por una conciencia ecológica',
            'descripcion' => 'Negocios que conectan campo y ciudad reconfiguran la producción y consumo de alimentos mediante sustentabilidad, innovación y logística oportuna.',
            'autores' => 'Hugo Ernesto López Morales',
            'paginas' => '13-17',
            'pdf' => 'pdf/aires-de-campo-por-una-conciencia-ecologica.pdf',   // sólo este artículo, separado del número 219
            'imagen' => 'img/aires_campo.jpeg',
            'cursos_fca' => [
                ['texto' => 'Seminario de Responsabilidad Social Empresarial - FCA', 'url' => 'https://www.fca.unam.mx/responsabilidad-social'],
                ['texto' => 'Diplomado en Sustentabilidad y Negocios',               'url' => 'https://www.fca.unam.mx/diplomados'],
            ],
        ],

        [
            'categoria' => 'TECNOLOGÍA, INNOVACIÓN Y COMPETITIVIDAD',
            'titulo' => 'ACOSDEC: supervisión para cimentar proyectos duraderos',
            'descripcion' => 'Iniciativa que redefine la gestión de proyectos en la construcción, fortaleciendo calidad y eficiencia mediante propuestas especializadas.',
            'autores' => 'Norma Tadeo Márquez<br>Julio César Olvera Hernández',
            'paginas' => '18-22',
            'pdf' => 'pdf/acosdec-supervision-para-cimentar-proyectos-duraderos.pdf',   // sólo este artículo, separado del número 219
            'imagen' => 'img/supervision.jpeg',
            'cursos_fca' => [
                ['texto' => 'Licenciatura en Informática - FCA UNAM',                'url' => 'https://www.fca.unam.mx/lic-informatica'],
                ['texto' => 'Diplomado en Gestión de Proyectos e Innovación',        'url' => 'https://www.fca.unam.mx/diplomados'],
            ],
        ],

        [
            'categoria' => 'EMPRENDIMIENTO SOCIAL',
            'titulo' => 'Blanki Oñatl: éxito mazahua en el emprendimiento social',
            'descripcion' => 'En contextos rurales marcados por desigualdad, la organización transforma condiciones de vida mediante trabajo comunitario, capacitación y acceso a mercados.',
            'autores' => 'Claudia Marcela Jerez Morales',
            'paginas' => '23-28',
            'pdf' => 'pdf/blanki-onatl-exito-mazahua-en-el-emprendimiento-social.pdf',   // sólo este artículo, separado del número 219
            'imagen' => 'img/artesania_mazahua.jpeg',
            'cursos_fca' => [
                ['texto' => 'Seminario de Emprendimiento Social - FCA UNAM',         'url' => 'https://www.fca.unam.mx/emprendimiento'],
                ['texto' => 'Diplomado en Economía Social y Solidaria',              'url' => 'https://www.fca.unam.mx/diplomados'],
            ],
        ],

    ],
];
