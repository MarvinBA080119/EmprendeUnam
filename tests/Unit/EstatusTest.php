<?php

namespace Tests\Unit;

use App\Enums\EstatusArticulo;
use App\Enums\EstatusMensaje;
use PHPUnit\Framework\TestCase;

class EstatusTest extends TestCase
{
    public function test_los_valores_coinciden_con_los_check_de_la_base(): void
    {
        $this->assertSame(['EN_REVISION', 'APROBADO', 'RECHAZADO'], array_column(EstatusArticulo::cases(), 'value'));
        $this->assertSame(['PENDIENTE', 'CONTESTADO', 'RECHAZADO'], array_column(EstatusMensaje::cases(), 'value'));
    }

    public function test_las_etiquetas_estan_en_espanol(): void
    {
        $this->assertSame('En revisión', EstatusArticulo::EN_REVISION->etiqueta());
        $this->assertSame('Contestado', EstatusMensaje::CONTESTADO->etiqueta());
    }
}
