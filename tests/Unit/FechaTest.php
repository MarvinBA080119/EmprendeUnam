<?php

namespace Tests\Unit;

use App\Support\Fecha;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class FechaTest extends TestCase
{
    public function test_fechas_de_solo_dia_en_palabras(): void
    {
        Carbon::setTestNow('2026-10-16 10:00:00');

        $this->assertSame('Hoy', Fecha::dias(Carbon::parse('2026-10-16')));
        $this->assertSame('Ayer', Fecha::dias(Carbon::parse('2026-10-15')));
        $this->assertSame('Hace 5 días', Fecha::dias(Carbon::parse('2026-10-11')));
        $this->assertSame('12 sep. 2026', Fecha::dias(Carbon::parse('2026-09-12')));

        Carbon::setTestNow();
    }
}
