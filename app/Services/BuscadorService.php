<?php

namespace App\Services;

/**
 * Buscador público (sección 6.9, fase E). Por ahora expone la expresión SQL
 * que quita acentos, compartida con los filtros del administrador.
 *
 * Mientras no se autorice la extensión unaccent, los acentos se quitan con
 * translate() en ambos lados de la comparación.
 */
class BuscadorService
{
    private const CON_ACENTO = 'áéíóúäëïöüàèìòùâêîôûñç';

    private const SIN_ACENTO = 'aeiouaeiouaeiouaeiounc';

    /**
     * Expresión SQL: el texto en minúsculas y sin acentos.
     * $expresion debe ser una columna o expresión fija, NUNCA texto del usuario
     * (el texto del usuario va como binding: ver patronLike()).
     */
    public static function sinAcentos(string $expresion): string
    {
        return "translate(lower({$expresion}), '".self::CON_ACENTO."', '".self::SIN_ACENTO."')";
    }

    /** Patrón LIKE ("%texto%") del texto del usuario, en minúsculas y sin acentos. */
    public static function patronLike(string $texto): string
    {
        $limpio = strtr(mb_strtolower(trim($texto)), self::mapaAcentos());

        return '%'.addcslashes($limpio, '%_\\').'%';
    }

    /** @return array<string, string> */
    private static function mapaAcentos(): array
    {
        return array_combine(
            mb_str_split(self::CON_ACENTO),
            str_split(self::SIN_ACENTO),
        );
    }
}
