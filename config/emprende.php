<?php

/*
| Parámetros propios de la Revista EmprendeUNAM. Los valores marcados como
| "pendiente" están listados en PENDIENTES.md.
*/

return [

    // Tamaño máximo del PDF de un artículo, en KB (7 MB, confirmado).
    'pdf_max_kb' => (int) env('EMPRENDE_PDF_MAX_KB', 7168),

    // Prefijo del folio de constancia: {prefijo}-{AAAA}-{NNNN} (pendiente).
    'folio_prefijo' => env('EMPRENDE_FOLIO_PREFIJO', 'EMP'),

    // Dígitos del consecutivo anual del folio.
    'folio_digitos' => 4,

    // Carpetas dentro de storage/app/private (disco "local").
    'carpeta_articulos' => 'articulos',
    'carpeta_constancias' => 'constancias',

    // Remitente que aparece en las respuestas del consultorio.
    'remitente_consultorio' => 'Consultorio EmprendeUNAM',

    // Registros por página en listados.
    'por_pagina' => 10,

];
