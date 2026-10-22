<?php

/*
| Parámetros propios de la Revista EmprendeUNAM. Los valores marcados como
| "pendiente" están listados en PENDIENTES.md.
*/

return [

    // Tamaño máximo del PDF de un artículo, en KB (7 MB, confirmado).
    'pdf_max_kb' => (int) env('EMPRENDE_PDF_MAX_KB', 7168),

    // Tamaño máximo del PDF de un número completo, en KB (50 MB). Revisa también
    // upload_max_filesize y post_max_size de PHP y client_max_body_size del servidor web.
    'pdf_max_revista_kb' => (int) env('EMPRENDE_PDF_MAX_REVISTA_KB', 51200),

    // Prefijo del folio de constancia: {prefijo}-{AAAA}-{NNNN} (pendiente).
    'folio_prefijo' => env('EMPRENDE_FOLIO_PREFIJO', 'EMP'),

    // Prefijo del folio de la carta de agradecimiento: {prefijo}-{AAAA}-{NNNN}.
    'folio_prefijo_agradecimiento' => env('EMPRENDE_FOLIO_PREFIJO_AGRADECIMIENTO', 'AGR'),

    // Quién firma la carta de agradecimiento.
    'agradecimiento_firma' => [
        'nombre' => env('EMPRENDE_AGRADECIMIENTO_FIRMA', 'Dra. Clotilde Hernández Garnica'),
        'cargo' => env('EMPRENDE_AGRADECIMIENTO_CARGO', 'La directora editorial'),
    ],

    // Dígitos del consecutivo anual del folio.
    'folio_digitos' => 4,

    // Carpetas dentro de storage/app/private (disco "local").
    'carpeta_articulos' => 'articulos',
    'carpeta_constancias' => 'constancias',
    'carpeta_agradecimientos' => 'agradecimientos',
    'carpeta_cesiones' => 'cesiones',
    'carpeta_ediciones' => 'ediciones',

    // Remitente que aparece en las respuestas del consultorio.
    'remitente_consultorio' => 'Consultorio EmprendeUNAM',

    // Horas que vale el enlace de confirmación del newsletter (doble confirmación).
    'newsletter_horas_confirmacion' => (int) env('EMPRENDE_NEWSLETTER_HORAS', 48),

    // Registros por página en listados.
    'por_pagina' => 10,

];
