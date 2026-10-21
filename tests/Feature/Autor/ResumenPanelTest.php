<?php

namespace Tests\Feature\Autor;

use App\Services\ArticuloService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreaArticulos;
use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

/** Resumen por estatus en el panel del autor. */
class ResumenPanelTest extends TestCase
{
    use CreaArticulos, CreaCuentas;

    public function test_cuenta_solo_los_articulos_del_autor_por_estatus(): void
    {
        Storage::fake('local');
        $autor = $this->crearAutor();
        $this->enviarArticulo($autor, ['titulo' => 'Primero en revisión']);
        $rechazado = $this->enviarArticulo($autor, ['titulo' => 'Segundo dictaminado']);
        app(ArticuloService::class)->rechazar($rechazado, 'El artículo no sigue las normas editoriales.', $this->administrador());
        $this->enviarArticulo($this->crearAutor(), ['titulo' => 'De otra persona']);
        DB::statement('SET CONSTRAINTS ALL IMMEDIATE');

        $resumen = app(ArticuloService::class)->resumenDe($autor);

        $this->assertSame(['total' => 2, 'en_revision' => 1, 'aprobados' => 0, 'rechazados' => 1], $resumen);

        $this->actingAs($autor)->get('/autor')
            ->assertOk()
            ->assertSee('Historial de envíos')
            ->assertSee('No aprobados');
    }

    public function test_sin_envios_no_muestra_el_resumen(): void
    {
        $autor = $this->crearAutor();

        $this->actingAs($autor)->get('/autor')
            ->assertOk()
            ->assertDontSee('Historial de envíos')
            ->assertSee('Aún no has enviado artículos');
    }
}
