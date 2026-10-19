<?php

namespace App\Enums;

/**
 * Valores permitidos por el CHECK ck_mens_status de la tabla mensaje.
 */
enum EstatusMensaje: string
{
    case PENDIENTE = 'PENDIENTE';
    case CONTESTADO = 'CONTESTADO';
    case RECHAZADO = 'RECHAZADO';

    public function etiqueta(): string
    {
        return match ($this) {
            self::PENDIENTE => 'Pendiente',
            self::CONTESTADO => 'Contestado',
            self::RECHAZADO => 'Rechazado',
        };
    }

    /** Clase CSS de la insignia (resources/css/emprende.css). */
    public function claseInsignia(): string
    {
        return match ($this) {
            self::PENDIENTE => 'emp-insignia-revision',
            self::CONTESTADO => 'emp-insignia-aprobado',
            self::RECHAZADO => 'emp-insignia-rechazado',
        };
    }
}
