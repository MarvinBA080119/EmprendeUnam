<?php

namespace Tests\Feature\Admin;

use Illuminate\Support\Facades\DB;
use Tests\Concerns\CreaContenido;
use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use CreaContenido, CreaCuentas;

    public function test_lista_suscriptores_con_su_estado(): void
    {
        $this->crearSuscriptor('activo', ['nombre' => 'Activa', 'correo' => 'activa@example.com', 'tema' => 'Comunicación']);
        $this->crearSuscriptor('sin_confirmar', ['nombre' => 'Pendiente', 'correo' => 'pendiente@example.com']);
        $this->crearSuscriptor('baja', ['nombre' => 'Exsuscrita', 'correo' => 'baja@example.com']);
        DB::statement('SET CONSTRAINTS ALL IMMEDIATE');

        $filas = collect($this->actingAs($this->administrador())
            ->getJson('/admin/newsletter/datos?q=example.com')
            ->assertOk()
            ->json('datos'))->keyBy('correo');

        $this->assertSame('Activo', $filas['activa@example.com']['estadoEtiqueta']);
        $this->assertSame('Comunicación', $filas['activa@example.com']['temas']);
        $this->assertSame('Sin confirmar', $filas['pendiente@example.com']['estadoEtiqueta']);
        $this->assertSame('Dado de baja', $filas['baja@example.com']['estadoEtiqueta']);

        $activos = collect($this->actingAs($this->administrador())
            ->getJson('/admin/newsletter/datos?estado=activo&q=example.com')
            ->json('datos'))->pluck('correo')->all();
        $this->assertSame(['activa@example.com'], $activos);
    }

    public function test_exporta_csv_utf8_con_bom(): void
    {
        $this->crearSuscriptor('activo', ['nombre' => 'José', 'apellido' => 'Núñez', 'correo' => 'jose@example.com', 'tema' => 'Análisis de mercados']);

        $respuesta = $this->actingAs($this->administrador())->get('/admin/newsletter/exportar?q=jose@example.com');

        $respuesta->assertOk()->assertDownload('suscriptores-newsletter-'.now()->format('Y-m-d').'.csv');
        $csv = $respuesta->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $csv);
        $this->assertStringContainsString('Nombre,Correo,"Tema de interés"', $csv);
        $this->assertStringContainsString('"José Núñez",jose@example.com,"Análisis de mercados"', $csv);
        $this->assertStringContainsString(',Activo', $csv);
    }

    public function test_el_csv_neutraliza_formulas(): void
    {
        $this->crearSuscriptor('activo', ['nombre' => '=HYPERLINK("x")', 'correo' => 'formula@example.com']);

        $csv = $this->actingAs($this->administrador())->get('/admin/newsletter/exportar?q=formula@example.com')->streamedContent();

        $this->assertStringContainsString("'=HYPERLINK", $csv);
    }
}
