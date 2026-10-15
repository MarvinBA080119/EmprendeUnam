<?php

/*
| Acervo histórico de ediciones.
| Convertido del sitio público original (datos-historico.php) sin cambiar su contenido.
*/

/**
 * Histórico de ediciones · Revista Emprendedores UNAM
 * --------------------------------------------------------------
 * Las rutas se arman en la vista con sprintf():
 *   Portada : img/portadas/{numero}.jpg
 *   PDF     : pdf/{numero}_emprendedores.pdf
 *   Ebook   : https://repositorios.fca.unam.mx/pruebaRevistaEmprendedores/emprendedores{numero}/
 *
 * Títulos marcados '' = pendientes; la vista muestra "Emprendedores No. X".
 */

return [
    'historico' => [
        ['numero' => 218, 'anio' => 2026, 'periodo' => 'Marzo - Abril 2026',        'titulo' => 'Igualdad para la innovación y el desarrollo'],
        ['numero' => 217, 'anio' => 2026, 'periodo' => 'Enero - Febrero 2026',       'titulo' => 'Visión global del cambio empresarial'],
        ['numero' => 216, 'anio' => 2025, 'periodo' => 'Noviembre - Diciembre 2025', 'titulo' => 'La Cocina: sabores enriquecidos por más de 50 años'],
        ['numero' => 215, 'anio' => 2025, 'periodo' => 'Septiembre - Octubre 2025',  'titulo' => 'Hecho en México: contenido nacional con valor y futuro'],
        ['numero' => 214, 'anio' => 2025, 'periodo' => 'Julio - Agosto 2025',        'titulo' => ''],
        ['numero' => 213, 'anio' => 2025, 'periodo' => 'Mayo - Junio 2025',          'titulo' => ''],
        ['numero' => 212, 'anio' => 2025, 'periodo' => 'Marzo - Abril 2025',         'titulo' => ''],
        ['numero' => 211, 'anio' => 2025, 'periodo' => 'Enero - Febrero 2025',       'titulo' => ''],
        ['numero' => 210, 'anio' => 2024, 'periodo' => 'Noviembre - Diciembre 2024', 'titulo' => ''],
        ['numero' => 209, 'anio' => 2024, 'periodo' => 'Septiembre - Octubre 2024',  'titulo' => ''],
        ['numero' => 208, 'anio' => 2024, 'periodo' => 'Julio - Agosto 2024',        'titulo' => 'Piso 16: iniciativas culturales de largo alcance'],
        ['numero' => 207, 'anio' => 2024, 'periodo' => 'Mayo - Junio 2024',          'titulo' => ''],
        ['numero' => 206, 'anio' => 2024, 'periodo' => 'Marzo - Abril 2024',         'titulo' => ''],
        ['numero' => 205, 'anio' => 2024, 'periodo' => 'Enero - Febrero 2024',       'titulo' => ''],
        ['numero' => 204, 'anio' => 2023, 'periodo' => 'Noviembre - Diciembre 2023', 'titulo' => ''],
        ['numero' => 203, 'anio' => 2023, 'periodo' => 'Septiembre - Octubre 2023',  'titulo' => ''],
        ['numero' => 202, 'anio' => 2023, 'periodo' => 'Julio - Agosto 2023',        'titulo' => ''],
        ['numero' => 201, 'anio' => 2023, 'periodo' => 'Mayo - Junio 2023',          'titulo' => ''],
        ['numero' => 200, 'anio' => 2023, 'periodo' => 'Marzo - Abril 2023',         'titulo' => 'Cultura en los negocios internacionales'],
        ['numero' => 199, 'anio' => 2023, 'periodo' => 'Enero - Febrero 2023',       'titulo' => ''],
        ['numero' => 198, 'anio' => 2022, 'periodo' => 'Noviembre - Diciembre 2022', 'titulo' => 'Desserto: la piel de México'],
        ['numero' => 197, 'anio' => 2022, 'periodo' => 'Septiembre - Octubre 2022',  'titulo' => ''],
        ['numero' => 196, 'anio' => 2022, 'periodo' => 'Julio - Agosto 2022',        'titulo' => 'Corazón Verde: comercio justo con sororidad'],
        ['numero' => 195, 'anio' => 2022, 'periodo' => 'Mayo - Junio 2022',          'titulo' => ''],
        ['numero' => 194, 'anio' => 2022, 'periodo' => 'Marzo - Abril 2022',         'titulo' => ''],
        ['numero' => 193, 'anio' => 2022, 'periodo' => 'Enero - Febrero 2022',       'titulo' => ''],
        ['numero' => 192, 'anio' => 2021, 'periodo' => 'Noviembre - Diciembre 2021', 'titulo' => ''],
        ['numero' => 191, 'anio' => 2021, 'periodo' => 'Septiembre - Octubre 2021',  'titulo' => ''],
        ['numero' => 190, 'anio' => 2021, 'periodo' => 'Julio - Agosto 2021',        'titulo' => ''],
        ['numero' => 189, 'anio' => 2021, 'periodo' => 'Mayo - Junio 2021',          'titulo' => ''],
        ['numero' => 188, 'anio' => 2021, 'periodo' => 'Marzo - Abril 2021',         'titulo' => ''],
        ['numero' => 187, 'anio' => 2021, 'periodo' => 'Enero - Febrero 2021',       'titulo' => ''],
        ['numero' => 186, 'anio' => 2020, 'periodo' => 'Noviembre - Diciembre 2020', 'titulo' => 'Disruptivo.tv Contenido de Impacto Social'],
    ],

    // El filtro TEMA ya no se alimenta desde aquí.
    // -----------------------------------------------------------------------------
    // Antes existía en este archivo una lista de nueve etiquetas (Manufactura,
    // Cultura, Finanzas...) y un campo 'tema' por edición que no correspondían a
    // ninguna de las áreas del apartado "Temáticas" ni a la clasificación real de
    // los artículos. Ambos se retiraron.
    //
    // La correspondencia edición -> áreas temáticas vive ahora en
    // datos-tematicas-ediciones.php, generado desde la base de metadatos editorial, y
    // los nombres de las áreas en datos-tematicas.php.

    // Años únicos para filtro (de más reciente a más antiguo)
    'anios_disponibles' => ['Todos', 2026, 2025, 2024, 2023, 2022, 2021, 2020],
];
