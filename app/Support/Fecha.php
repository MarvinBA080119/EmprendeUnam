<?php

namespace App\Support;

use Carbon\CarbonInterface;

/** Fechas relativas en español para listas y bandejas. */
class Fecha
{
    /**
     * «Hoy», «Ayer», «Hace 5 días» o, después de 30 días, «12 sep. 2026».
     * Para columnas de solo fecha (sin hora).
     */
    public static function dias(CarbonInterface $fecha): string
    {
        $dias = (int) $fecha->copy()->startOfDay()->diffInDays(today(), false);

        return match (true) {
            $dias <= 0 => 'Hoy',
            $dias === 1 => 'Ayer',
            $dias <= 30 => "Hace {$dias} días",
            default => $fecha->locale('es')->translatedFormat('j M Y'),
        };
    }

    /** «Hace 3 horas», «Hace 2 días»… para fechas con hora. */
    public static function relativa(CarbonInterface $fecha): string
    {
        return ucfirst($fecha->locale('es')->diffForHumans());
    }
}
