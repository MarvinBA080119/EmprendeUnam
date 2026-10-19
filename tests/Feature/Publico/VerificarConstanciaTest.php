<?php

namespace Tests\Feature\Publico;

use App\Services\ConstanciaService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreaArticulos;
use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

class VerificarConstanciaTest extends TestCase
{
    use CreaArticulos, CreaCuentas;

    public function test_un_folio_existente_muestra_titulo_y_fecha_sin_descargar_el_pdf(): void
    {
        Storage::fake('local');
        $articulo = $this->enviarArticulo($this->crearAutor(), ['titulo' => 'Liderazgo y cultura organizacional']);
        Mail::fake();
        $constancia = app(ConstanciaService::class)->aprobar($articulo, $this->administrador());

        $respuesta = $this->get("/constancias/verificar/{$constancia->cons_folio}");

        $respuesta->assertOk()
            ->assertSee('Constancia válida')
            ->assertSee('Liderazgo y cultura organizacional')
            ->assertSee($constancia->cons_fecha_emision->locale('es')->translatedFormat('j \d\e F \d\e Y'));
        $this->assertStringStartsWith('text/html', $respuesta->headers->get('content-type'));
    }

    public function test_un_folio_inexistente_lo_indica(): void
    {
        $this->get('/constancias/verificar/EMP-1999-9999')
            ->assertOk()
            ->assertSee('Folio no encontrado');
    }

    public function test_el_qr_de_la_constancia_apunta_a_la_verificacion(): void
    {
        $this->assertSame(
            url('/constancias/verificar/EMP-2026-0001'),
            app(ConstanciaService::class)->urlVerificacion('EMP-2026-0001'),
        );
    }
}
