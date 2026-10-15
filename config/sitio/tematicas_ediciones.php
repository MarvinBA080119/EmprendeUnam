<?php

/*
| Relación edición → áreas temáticas.
| Convertido del sitio público original (datos-tematicas-ediciones.php) sin cambiar su contenido.
*/

/**
 * datos-tematicas-ediciones.php
 * Áreas temáticas presentes en cada número de la revista.
 *
 * FUENTE
 * ---------------------------------------------------------------------------
 * Se deriva de la base de metadatos (Metadatos de artículos, 04/09/2026):
 * para cada número se reúnen las áreas de todos sus artículos, según las
 * columnas "Temática 1" y "Temática 2". Es lo que alimenta el filtro TEMA
 * del Histórico.
 *
 * La clave es el número de edición; el valor, los slugs de datos-tematicas.php.
 *
 * NO EDITAR A MANO: se regenera desde la base de metadatos.
 */

return [
    'tematicas_por_edicion' => [
        220 => ['escenarios-estrategia', 'negocios-internacionales', 'emprendimiento-social'],
        219 => ['escenarios-estrategia', 'cultura-organizacional', 'economia-sociedad', 'relacion-consumidor', 'negocios-internacionales', 'emprendimiento-social'],
        218 => ['diagnostico-problemas', 'escenarios-estrategia', 'relacion-consumidor', 'toma-decisiones', 'emprendimiento-social'],
        217 => ['direccion-talento', 'escenarios-estrategia', 'cultura-organizacional', 'negocios-internacionales', 'toma-decisiones', 'emprendimiento-social'],
        216 => ['comunicacion', 'negocios-internacionales', 'toma-decisiones', 'emprendimiento-social'],
        215 => ['escenarios-estrategia', 'negocios-internacionales'],
        214 => ['negocios-internacionales', 'toma-decisiones', 'emprendimiento-social'],
        213 => ['habilidades-directivas', 'direccion-talento', 'escenarios-estrategia', 'economia-sociedad', 'negocios-internacionales', 'toma-decisiones', 'emprendimiento-social'],
        212 => ['procesos-creativos', 'diagnostico-problemas', 'escenarios-estrategia', 'economia-sociedad', 'relacion-consumidor', 'emprendimiento-social'],
        211 => ['direccion-talento', 'escenarios-estrategia', 'cultura-organizacional', 'relacion-consumidor', 'negocios-internacionales', 'toma-decisiones'],
        210 => ['habilidades-directivas', 'procesos-creativos', 'diagnostico-problemas', 'direccion-talento', 'escenarios-estrategia', 'cultura-organizacional', 'economia-sociedad', 'relacion-consumidor', 'toma-decisiones'],
        209 => ['habilidades-directivas', 'direccion-talento', 'escenarios-estrategia', 'analisis-mercados', 'economia-sociedad', 'emprendimiento-social'],
        208 => ['procesos-creativos', 'direccion-talento', 'cultura-organizacional', 'emprendimiento-social'],
        207 => ['escenarios-estrategia', 'economia-sociedad', 'emprendimiento-social'],
        206 => ['habilidades-directivas', 'direccion-talento', 'economia-sociedad', 'negocios-internacionales', 'toma-decisiones', 'emprendimiento-social'],
        205 => ['escenarios-estrategia', 'analisis-mercados', 'economia-sociedad', 'negocios-internacionales', 'toma-decisiones'],
        204 => ['comunicacion', 'economia-sociedad', 'toma-decisiones'],
        203 => ['direccion-talento', 'analisis-mercados', 'economia-sociedad', 'negocios-internacionales', 'toma-decisiones'],
        202 => ['direccion-talento', 'escenarios-estrategia', 'comunicacion', 'economia-sociedad', 'relacion-consumidor', 'negocios-internacionales', 'toma-decisiones', 'emprendimiento-social'],
        201 => ['habilidades-directivas', 'direccion-talento', 'cultura-organizacional', 'analisis-mercados', 'economia-sociedad', 'relacion-consumidor'],
        200 => ['habilidades-directivas', 'procesos-creativos', 'escenarios-estrategia', 'analisis-mercados', 'economia-sociedad', 'negocios-internacionales'],
        199 => ['habilidades-directivas', 'direccion-talento', 'comunicacion', 'economia-sociedad', 'negocios-internacionales', 'toma-decisiones', 'emprendimiento-social'],
        198 => ['habilidades-directivas', 'procesos-creativos', 'diagnostico-problemas', 'direccion-talento', 'escenarios-estrategia', 'cultura-organizacional', 'comunicacion', 'economia-sociedad', 'negocios-internacionales', 'toma-decisiones', 'emprendimiento-social'],
        197 => ['habilidades-directivas', 'diagnostico-problemas', 'direccion-talento', 'escenarios-estrategia', 'cultura-organizacional', 'analisis-mercados', 'comunicacion', 'economia-sociedad', 'relacion-consumidor', 'toma-decisiones'],
        196 => ['habilidades-directivas', 'procesos-creativos', 'diagnostico-problemas', 'direccion-talento', 'escenarios-estrategia', 'cultura-organizacional', 'analisis-mercados', 'comunicacion', 'economia-sociedad', 'relacion-consumidor', 'toma-decisiones', 'emprendimiento-social'],
        195 => ['habilidades-directivas', 'procesos-creativos', 'direccion-talento', 'escenarios-estrategia', 'cultura-organizacional', 'analisis-mercados', 'comunicacion', 'economia-sociedad', 'relacion-consumidor', 'negocios-internacionales', 'toma-decisiones', 'emprendimiento-social'],
        194 => ['habilidades-directivas', 'procesos-creativos', 'diagnostico-problemas', 'escenarios-estrategia', 'analisis-mercados', 'economia-sociedad', 'relacion-consumidor', 'negocios-internacionales', 'toma-decisiones'],
        193 => ['habilidades-directivas', 'diagnostico-problemas', 'direccion-talento', 'escenarios-estrategia', 'cultura-organizacional', 'comunicacion', 'economia-sociedad', 'negocios-internacionales', 'toma-decisiones', 'emprendimiento-social'],
        192 => ['habilidades-directivas', 'procesos-creativos', 'diagnostico-problemas', 'direccion-talento', 'escenarios-estrategia', 'analisis-mercados', 'economia-sociedad', 'negocios-internacionales', 'toma-decisiones', 'emprendimiento-social'],
        191 => ['habilidades-directivas', 'procesos-creativos', 'diagnostico-problemas', 'direccion-talento', 'escenarios-estrategia', 'cultura-organizacional', 'analisis-mercados', 'comunicacion', 'economia-sociedad', 'toma-decisiones', 'emprendimiento-social'],
        190 => ['habilidades-directivas', 'procesos-creativos', 'diagnostico-problemas', 'escenarios-estrategia', 'cultura-organizacional', 'analisis-mercados', 'comunicacion', 'economia-sociedad', 'relacion-consumidor', 'negocios-internacionales', 'toma-decisiones', 'emprendimiento-social'],
        189 => ['procesos-creativos', 'diagnostico-problemas', 'direccion-talento', 'escenarios-estrategia', 'cultura-organizacional', 'analisis-mercados', 'comunicacion', 'economia-sociedad', 'relacion-consumidor', 'toma-decisiones', 'emprendimiento-social'],
        188 => ['habilidades-directivas', 'procesos-creativos', 'diagnostico-problemas', 'direccion-talento', 'escenarios-estrategia', 'comunicacion', 'economia-sociedad', 'relacion-consumidor', 'negocios-internacionales', 'toma-decisiones', 'emprendimiento-social'],
        187 => ['habilidades-directivas', 'procesos-creativos', 'diagnostico-problemas', 'direccion-talento', 'escenarios-estrategia', 'analisis-mercados', 'comunicacion', 'economia-sociedad', 'relacion-consumidor', 'toma-decisiones', 'emprendimiento-social'],
        186 => ['diagnostico-problemas', 'escenarios-estrategia', 'comunicacion', 'economia-sociedad', 'relacion-consumidor', 'negocios-internacionales', 'toma-decisiones', 'emprendimiento-social'],
        185 => ['habilidades-directivas', 'procesos-creativos', 'diagnostico-problemas', 'direccion-talento', 'escenarios-estrategia', 'cultura-organizacional', 'analisis-mercados', 'economia-sociedad', 'relacion-consumidor', 'toma-decisiones', 'emprendimiento-social'],
        184 => ['habilidades-directivas', 'diagnostico-problemas', 'direccion-talento', 'escenarios-estrategia', 'cultura-organizacional', 'analisis-mercados', 'comunicacion', 'economia-sociedad', 'relacion-consumidor', 'negocios-internacionales', 'toma-decisiones'],
        183 => ['habilidades-directivas', 'procesos-creativos', 'diagnostico-problemas', 'direccion-talento', 'escenarios-estrategia', 'cultura-organizacional', 'analisis-mercados', 'economia-sociedad', 'relacion-consumidor', 'toma-decisiones', 'emprendimiento-social'],
        182 => ['habilidades-directivas', 'procesos-creativos', 'diagnostico-problemas', 'direccion-talento', 'escenarios-estrategia', 'cultura-organizacional', 'analisis-mercados', 'comunicacion', 'economia-sociedad', 'relacion-consumidor', 'toma-decisiones', 'emprendimiento-social'],
        181 => ['habilidades-directivas', 'procesos-creativos', 'diagnostico-problemas', 'direccion-talento', 'escenarios-estrategia', 'cultura-organizacional', 'comunicacion', 'economia-sociedad', 'relacion-consumidor', 'toma-decisiones', 'emprendimiento-social'],
    ],
];
