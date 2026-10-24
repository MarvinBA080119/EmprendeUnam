<?php

namespace Tests\Feature\Admin;

use App\Services\PanelAdminService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreaArticulos;
use Tests\Concerns\CreaContenido;
use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

class PanelTest extends TestCase
{
    use CreaArticulos, CreaContenido, CreaCuentas;

    public function test_los_contadores_del_panel(): void
    {
        Storage::fake('local');
        $antes = app(PanelAdminService::class)->contadores();

        $this->crearAutor(activo: false);
        $this->crearAutor(activo: false);
        $this->enviarArticulo($this->crearAutor());
        $this->crearMensaje();
        $this->crearMensaje(['mens_status' => 'RECHAZADO']);
        $this->crearSuscriptor('activo');
        $this->crearSuscriptor('sin_confirmar');
        $this->crearSuscriptor('baja');
        DB::statement('SET CONSTRAINTS ALL IMMEDIATE');

        $despues = app(PanelAdminService::class)->contadores();

        $this->assertSame($antes['autores_pendientes'] + 2, $despues['autores_pendientes']);
        $this->assertSame($antes['articulos_en_revision'] + 1, $despues['articulos_en_revision']);
        $this->assertSame($antes['mensajes_pendientes'] + 1, $despues['mensajes_pendientes']);
        $this->assertSame($antes['suscriptores_activos'] + 1, $despues['suscriptores_activos']);

        $this->actingAs($this->administrador())->get('/admin')
            ->assertOk()
            ->assertSee('Autores pendientes de activar')
            ->assertSee('Artículos en revisión')
            ->assertSee('Mensajes del consultorio pendientes')
            ->assertSee('Suscriptores activos')
            ->assertSee('Cursos visibles');
    }

    public function test_la_bandeja_muestra_lo_pendiente_y_escapa_los_nombres(): void
    {
        Storage::fake('local');
        $this->crearAutor(activo: false, datos: ['nombre' => "Ana'); alert(1); ('", 'primer_apellido' => 'O<b>Neil']);
        $this->enviarArticulo($this->crearAutor(), ['titulo' => 'Redes de mentoría en incubadoras']);
        $this->crearMensaje(['mens_asunto' => '¿Cómo valúo mi empresa?']);

        $respuesta = $this->actingAs($this->administrador())->get('/admin')->assertOk();

        $respuesta->assertSee('Redes de mentoría en incubadoras')
            ->assertSee('¿Cómo valúo mi empresa?')
            ->assertSee('data-confirmar=', false)
            ->assertDontSee("Ana'); alert(1)", false)
            ->assertDontSee('O<b>Neil', false)
            ->assertSee('Ana&#039;); alert(1); (&#039; O&lt;b&gt;Neil', false);
    }

    public function test_la_barra_lateral_muestra_los_pendientes(): void
    {
        $this->crearAutor(activo: false);
        $pendientes = app(PanelAdminService::class)->contadores()['autores_pendientes'];

        $this->actingAs($this->administrador())->get('/admin/cursos')
            ->assertOk()
            ->assertSee('<span class="emp-cuenta">'.$pendientes.'<span class="visually-hidden"> por activar</span></span>', false);
    }

    public function test_todas_las_secciones_del_panel_responden(): void
    {
        $admin = $this->administrador();

        foreach (['/admin', '/admin/autores', '/admin/articulos', '/admin/consultorio', '/admin/cursos', '/admin/cursos/nuevo', '/admin/newsletter'] as $ruta) {
            $this->actingAs($admin)->get($ruta)->assertOk();
        }
        foreach (['/admin/autores/datos', '/admin/articulos/datos', '/admin/consultorio/datos', '/admin/newsletter/datos'] as $ruta) {
            $this->actingAs($admin)->getJson($ruta)->assertOk()->assertJsonStructure(['datos', 'meta']);
        }
    }

    public function test_un_autor_no_entra_a_ninguna_seccion(): void
    {
        $autor = $this->crearAutor();

        foreach (['/admin/consultorio', '/admin/cursos', '/admin/newsletter', '/admin/newsletter/exportar', '/admin/articulos/datos'] as $ruta) {
            $this->actingAs($autor)->get($ruta)->assertRedirect('/autor');
        }
    }
}
