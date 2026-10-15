<?php

/*
| Datos generales del sitio público: contacto, revista actual, convocatoria y directorios.
| Convertido del sitio público original (config.php) sin cambiar su contenido.
*/

/**
 * Configuración global del sitio
 * Emprendedores UNAM - FCA
 * PHP 5.3 compatible
 */

// Configuración general

return [
    'config' => [
        'site_name' => 'Emprendedores UNAM',
        'site_url' => 'https://emprendedores.fca.unam.mx',
        'ebook_url' => 'https://repositorios.fca.unam.mx/pruebaRevistaEmprendedores/emprendedores0/',
        'contact_email' => 'revistaemprendedores@fca.unam.mx',
        'contact_phone' => '+52 (55) 5622 8332',
        'address' => 'Circuito Exterior s/n, Ciudad Universitaria<br>Coyoacán, CP 04510, CDMX, México',
        'year' => 2026,
    ],

    // Revista actual (la más reciente)
    'revista_actual' => [
        'edicion' => 0,   // número consecutivo de la revista
        'volumen' => 0,
        'numero' => 1,
        'anio' => 2026,
        'periodo' => 'Octubre - Noviembre 2026',
        'publicado' => '2026-08-17',
        'titulo' => 'Diferenciación para generar ventajas competitivas',
        // Imagen ilustrativa del artículo principal (ya no se usa la portada de la revista)
        'imagen_principal' => 'img/Gresmex_2026.png',
        'portada' => 'img/portadas/0.jpg',   // número cero de la nueva etapa
        'pdf_completo' => 'pdf/emprendedores_num0-2026.pdf',
    ],

    // Convocatoria
    'convocatoria' => [
        'titulo' => 'CONVOCATORIA ABIERTA',
        'descripcion' => 'Recibimos artículos durante todo el año, envie su contribución.',
        'cta' => 'ENVIAR ARTÍCULO',
    ],

    // Directorios en los que la revista está incluida
    // (la indexación aún se encuentra en proceso)
    'incluida_en' => [
        [
            'nombre' => 'Directorio Latindex',
            'descripcion' => 'Registro vigente; la indexación se encuentra pendiente.',
            'url' => 'https://www.latindex.org/latindex/ficha/6603',
        ],
        [
            'nombre' => 'Directorio de publicaciones UNAM',
            'descripcion' => 'Catálogo de publicaciones de la Universidad.',
            'url' => 'https://www.unam.mx/cultura/publicaciones',
        ],
    ],

    // Idiomas disponibles
    'idiomas' => [
        'es' => 'Español (España)',
        'en' => 'English',
        'pt' => 'Português',
    ],
];
