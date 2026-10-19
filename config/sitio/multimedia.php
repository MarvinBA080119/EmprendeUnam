<?php

/*
| Podcasts y webinars.
| Convertido del sitio público original (datos-multimedia.php) sin cambiar su contenido.
*/

/**
 * datos-multimedia.php
 * Datos de Podcasts y Webinars
 *
 * ESTADO DE LA PUBLICACIÓN (indicaciones del 4 de septiembre de 2026)
 * ---------------------------------------------------------------------------
 * La coordinación editorial estableció una cadencia de UNA pieza al mes, tanto
 * en audio como en video. En consecuencia:
 *
 *   $podcasts        → un solo episodio. Los otros tres que figuraban aquí
 *                      eran material de maqueta y se retiraron.
 *   $mas_webinars    → vacío, por la misma razón.
 *
 * Las vistas (podcasts.php y webinar.php) sólo pintan las rejillas "Episodios
 * de la serie" y "Más videos de la serie" cuando hay más de una pieza: al
 * añadir la segunda entrada a estos arreglos, la presentación anterior vuelve
 * sola, sin tocar el marcado.
 */

/* ══════════════════════════════════════════════════════════════════════════
   PODCASTS

   El episodio está por definirse: la coordinación editorial aún decide cuál
   será el primer audio de la nueva etapa. Los campos quedan vacíos a
   propósito —la vista los omite cuando no traen valor— para que no se
   publique ningún dato que no venga de la propia coordinación.
   ══════════════════════════════════════════════════════════════════════════ */

return [
    'podcasts' => [
        [
            'id' => 1,
            'titulo' => 'Emprende y cuida tu salud mental',
            'autor' => '',
            'descripcion' => 'Una conversación sobre los retos emocionales de emprender y la necesidad de encontrar el equilibrio entre el trabajo y el bienestar personal',
            'fecha' => '',
            'duracion' => '',
            'imagen' => 'img/podcasts/episodio-01.jpg',
            'spotify' => '687LgiJSDUyFsdRpCfndhA',   // ← ESTE ES EL CAMBIO CLAVE
            'audio' => '',                          // ← déjalo vacío, no tienes MP3
            'activo' => true,
        ],
    ],

    /* ══════════════════════════════════════════════════════════════════════════
   WEBINARS

   Contenido del primer webinar, tal como lo entregó la coordinación editorial
   ("Textos para Webinar 1", 4 de septiembre de 2026). La descripción viene en
   dos párrafos y así se conserva: resumirla en uno cambiaría el texto.

   No se registra duración porque el documento no la indica; la vista omite ese
   dato cuando viene vacío en lugar de inventar una cifra.
   ══════════════════════════════════════════════════════════════════════════ */
    'webinar_principal' => [
        'titulo' => 'UN MUNDO DE TRES CEROS Y LA IMPORTANCIA DE LA JUVENTUD',
        'fecha' => '19 DE JUNIO, 2019',
        'duracion' => '',
        'imagen' => 'img/webinars/yunus-un-mundo-de-tres-ceros.jpg',   // Muhammad Yunus en la FCA, foto enviada por la coordinación
        'video_url' => '',
        'descripcion' => [
            'En esta conferencia magistral, Muhammad Yunus, Premio Nobel de la Paz 2006 y fundador del Grameen Bank, comparte su visión sobre el emprendimiento social como una herramienta para enfrentar grandes desafíos de la sociedad.',
            'Yunus invita a las nuevas generaciones a crear negocios cuyo propósito sea resolver problemas reales, más allá de la generación de ganancias, con la meta de avanzar hacia un mundo con cero pobreza, cero desempleo y cero emisiones de carbono.',
        ],
        'puntos_clave' => [
            'Emprendimiento social como motor de transformación',
            'Microcréditos como herramienta para combatir la pobreza',
            'El papel de la juventud en la construcción de soluciones sostenibles',
        ],
        'participantes' => [
            [
                'nombre' => 'Muhammad Yunus',
                'cargo' => 'Premio Nobel de la Paz 2006 y fundador del Grameen Bank',
            ],
        ],
    ],

    /* Sin más videos por ahora: uno al mes. Al añadir entradas aquí reaparece la
   rejilla "Más videos de la serie". */
    'mas_webinars' => [],

    /* Mapas de videos (antes vivían en podcasts.php y webinar.php): ID de YouTube
       de cada episodio que también se publique como video. Vacío si no aplica. */
    'youtube_podcasts' => [
        1 => '',
    ],
    'youtube_webinar' => [
        'principal' => '',    // UN MUNDO DE TRES CEROS Y LA IMPORTANCIA DE LA JUVENTUD
    ],
];
