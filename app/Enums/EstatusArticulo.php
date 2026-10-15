<?php

namespace App\Enums;

/**
 * Valores permitidos por el CHECK ck_arti_status de la tabla articulo.
 */
enum EstatusArticulo: string
{
    case EN_REVISION = 'EN_REVISION';
    case APROBADO = 'APROBADO';
    case RECHAZADO = 'RECHAZADO';

    public function etiqueta(): string
    {
        return match ($this) {
            self::EN_REVISION => 'En revisión',
            self::APROBADO => 'Aprobado',
            self::RECHAZADO => 'Rechazado',
        };
    }

    /** Clase CSS de la insignia (resources/css/emprende.css). */
    public function claseInsignia(): string
    {
        return match ($this) {
            self::EN_REVISION => 'emp-insignia-revision',
            self::APROBADO => 'emp-insignia-aprobado',
            self::RECHAZADO => 'emp-insignia-rechazado',
        };
    }
}
