<?php

namespace Tests\Unit;

use App\Support\Fecha;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class FechaTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_fechas_de_solo_dia_en_palabras(): void
    {
        Carbon::setTestNow('2026-10-16 10:00:00');

        $this->assertSame('hoy', Fecha::dias(Carbon::parse('2026-10-16')));
        $this->assertSame('ayer', Fecha::dias(Carbon::parse('2026-10-15')));
        $this->assertSame('hace 5 días', Fecha::dias(Carbon::parse('2026-10-11')));
        $this->assertSame('12 sep.', Fecha::dias(Carbon::parse('2026-09-12')));
        $this->assertSame('3 dic. 2025', Fecha::dias(Carbon::parse('2025-12-03')));
    }

    public function test_fechas_con_hora_en_minutos_y_horas_el_mismo_dia(): void
    {
        Carbon::setTestNow('2026-10-16 10:00:00');

        $this->assertSame('ahora', Fecha::relativa(Carbon::parse('2026-10-16 09:59:40')));
        $this->assertSame('hace 5 min', Fecha::relativa(Carbon::parse('2026-10-16 09:55:00')));
        $this->assertSame('hace 3 h', Fecha::relativa(Carbon::parse('2026-10-16 07:00:00')));
        $this->assertSame('ayer', Fecha::relativa(Carbon::parse('2026-10-15 23:00:00')));
    }

    public function test_saludo_segun_la_hora_y_fecha_larga(): void
    {
        $this->assertSame('Buenos días', Fecha::saludo(Carbon::parse('2026-10-16 08:30')));
        $this->assertSame('Buenas tardes', Fecha::saludo(Carbon::parse('2026-10-16 12:00')));
        $this->assertSame('Buenas noches', Fecha::saludo(Carbon::parse('2026-10-16 19:00')));
        $this->assertSame('viernes 16 de octubre', Fecha::hoyLargo(Carbon::parse('2026-10-16 08:30')));
    }
}
