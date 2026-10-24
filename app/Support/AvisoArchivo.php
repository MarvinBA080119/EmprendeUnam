<?php

namespace App\Support;

/**
 * Aviso para un envío más pesado de lo que acepta PHP (post_max_size). Esa
 * petición se rechaza antes de abrir la sesión, así que el aviso no puede
 * viajar como flash: se regresa a la página anterior con ?aviso=archivo-pesado
 * y <x-alertas> lo muestra como notificación de error.
 */
class AvisoArchivo
{
    public const PARAMETRO = 'archivo-pesado';

    public static function mensaje(): string
    {
        return 'El archivo es demasiado pesado: el servidor acepta envíos de hasta '.self::limiteMb()
            .' MB en total. Revisa el límite indicado junto al campo y elige un archivo más ligero.';
    }

    /** post_max_size de PHP en MB (por ejemplo «60M» → 60). */
    public static function limiteMb(): int
    {
        $valor = trim((string) ini_get('post_max_size'));
        $numero = (float) $valor;

        return (int) round(match (strtoupper(substr($valor, -1))) {
            'G' => $numero * 1024,
            'K' => $numero / 1024,
            'M' => $numero,
            default => $numero / 1048576,
        });
    }

    /** Página anterior del mismo sitio con el aviso en la URL, o el inicio. */
    public static function volver(?string $previa, string $host): string
    {
        $destino = $previa && parse_url($previa, PHP_URL_HOST) === $host ? $previa : route('inicio');
        $destino = preg_replace('/([?&])aviso=[^&#]*&?/', '$1', $destino);
        $destino = rtrim((string) $destino, '?&');

        return $destino.(str_contains($destino, '?') ? '&' : '?').'aviso='.self::PARAMETRO;
    }
}
