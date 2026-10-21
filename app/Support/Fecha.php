<?php

namespace App\Support;

use Carbon\CarbonInterface;

/**
 * Fechas relativas cortas en español, en minúsculas, para listas y bandejas:
 * «ahora», «hace 5 min», «hace 3 h», «hoy», «ayer», «hace 4 días», «12 oct.».
 */
class Fecha
{
    /** Saludo según la hora: «Buenos días» hasta las 11:59, «Buenas tardes» hasta las 18:59 y «Buenas noches». */
    public static function saludo(?CarbonInterface $momento = null): string
    {
        $hora = ($momento ?? now())->hour;

        return match (true) {
            $hora < 12 => 'Buenos días',
            $hora < 19 => 'Buenas tardes',
            default => 'Buenas noches',
        };
    }

    /** Fecha larga del día en minúsculas: «martes 20 de octubre». */
    public static function hoyLargo(?CarbonInterface $momento = null): string
    {
        return ($momento ?? now())->locale('es')->translatedFormat('l j \d\e F');
    }

    /** Para columnas de solo fecha (sin hora). */
    public static function dias(CarbonInterface $fecha): string
    {
        $dias = (int) $fecha->copy()->startOfDay()->diffInDays(today(), false);

        return match (true) {
            $dias <= 0 => 'hoy',
            $dias === 1 => 'ayer',
            $dias <= 30 => "hace {$dias} días",
            default => self::fechaCorta($fecha),
        };
    }

    /** Para fechas con hora: minutos y horas el mismo día; después, como dias(). */
    public static function relativa(CarbonInterface $fecha): string
    {
        $minutos = (int) $fecha->diffInMinutes(now(), false);

        if ($fecha->isToday() && $minutos < 60) {
            return $minutos < 1 ? 'ahora' : "hace {$minutos} min";
        }
        if ($fecha->isToday()) {
            return 'hace '.intdiv($minutos, 60).' h';
        }

        return self::dias($fecha);
    }

    private static function fechaCorta(CarbonInterface $fecha): string
    {
        $formato = $fecha->isCurrentYear() ? 'j M' : 'j M Y';

        return $fecha->locale('es')->translatedFormat($formato);
    }
}
