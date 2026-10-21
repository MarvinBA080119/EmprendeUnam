<?php

namespace App\Support;

/** Pequeñas ayudas de redacción para las frases de los paneles. */
class Texto
{
    /** «1 artículo», «4 artículos». */
    public static function cantidad(int $numero, string $singular, string $plural): string
    {
        return $numero.' '.($numero === 1 ? $singular : $plural);
    }

    /** Une frases con comas y una «y» final: «a», «a y b», «a, b y c». */
    public static function lista(array $partes): string
    {
        $partes = array_values(array_filter($partes, fn ($p) => $p !== null && $p !== ''));
        $ultima = array_pop($partes);

        return $partes === [] ? (string) $ultima : implode(', ', $partes).' y '.$ultima;
    }
}
