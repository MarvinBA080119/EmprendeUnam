<?php

namespace App\Enums;

/**
 * Valores permitidos por el CHECK ck_cons_tipo de la tabla constancia: los
 * documentos de un artículo. La carta de cesión no lleva folio
 * (ck_cons_folio_segun_tipo); la constancia y la de agradecimiento sí.
 */
enum TipoDocumento: string
{
    case CONSTANCIA = 'CONSTANCIA';
    case AGRADECIMIENTO = 'AGRADECIMIENTO';
    case CESION = 'CESION';

    public function etiqueta(): string
    {
        return match ($this) {
            self::CONSTANCIA => 'Constancia',
            self::AGRADECIMIENTO => 'Carta de agradecimiento',
            self::CESION => 'Carta de cesión de derechos',
        };
    }

    public function llevaFolio(): bool
    {
        return $this !== self::CESION;
    }
}
