<?php

namespace Tests\Feature\Auth;

use App\Http\Middleware\ExpiraPorInactividad;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

/** Cambio de cuenta, cierre de sesión, caché del navegador e inactividad. */
class SesionTest extends TestCase
{
    use CreaCuentas;

    public function test_con_sesion_de_autor_el_login_con_credenciales_de_admin_abre_la_del_admin(): void
    {
        $autor = $this->crearAutor();
        $admin = $this->administrador(conClaveDePrueba: true);

        $this->actingAs($autor)->withSession(['dato_de_la_sesion_anterior' => 'autor']);
        $idAnterior = session()->getId();

        $this->post('/iniciar-sesion', ['acceso' => 'EmprendeUnam', 'clave' => self::CLAVE_PRUEBA])
            ->assertRedirect('/admin')
            ->assertSessionMissing('dato_de_la_sesion_anterior');

        $this->assertAuthenticatedAs($admin);
        $this->assertNotSame($idAnterior, session()->getId());
    }

    public function test_con_sesion_abierta_unas_credenciales_incorrectas_cierran_la_anterior(): void
    {
        $autor = $this->crearAutor();

        $this->actingAs($autor)
            ->post('/iniciar-sesion', ['acceso' => 'EmprendeUnam', 'clave' => 'no-es-la-clave'])
            ->assertSessionHasErrors('acceso');

        $this->assertGuest();
    }

    public function test_el_login_avisa_con_que_cuenta_esta_abierta_la_sesion(): void
    {
        $autor = $this->crearAutor(datos: ['nombre_usuario' => 'mariana']);

        $this->actingAs($autor)->get('/iniciar-sesion')
            ->assertOk()
            ->assertSee('Tienes una sesión abierta como')
            ->assertSee('mariana');
    }

    public function test_no_respeta_una_pagina_pedida_que_es_del_panel_de_otro_rol(): void
    {
        $autor = $this->crearAutor();

        $this->withSession(['url.intended' => url('/admin/articulos')])
            ->post('/iniciar-sesion', ['acceso' => $autor->correo(), 'clave' => self::CLAVE_PRUEBA])
            ->assertRedirect('/autor');
    }

    public function test_respeta_la_pagina_pedida_dentro_del_mismo_panel(): void
    {
        $autor = $this->crearAutor();

        $this->withSession(['url.intended' => url('/autor/articulos/nuevo')])
            ->post('/iniciar-sesion', ['acceso' => $autor->correo(), 'clave' => self::CLAVE_PRUEBA])
            ->assertRedirect('/autor/articulos/nuevo');
    }

    public function test_cerrar_sesion_lleva_al_login(): void
    {
        $this->actingAs($this->crearAutor())
            ->post('/cerrar-sesion')
            ->assertRedirect('/iniciar-sesion')
            ->assertSessionHas('exito', 'Cerraste tu sesión.');

        $this->assertGuest();
    }

    public function test_el_cierre_por_inactividad_lo_dice_en_el_login(): void
    {
        $this->actingAs($this->crearAutor())
            ->post('/cerrar-sesion', ['motivo' => 'inactividad'])
            ->assertRedirect('/iniciar-sesion')
            ->assertSessionHas('aviso', ExpiraPorInactividad::MENSAJE);

        $this->get('/iniciar-sesion')->assertSee('Tu sesión expiró por inactividad');
    }

    public function test_cerrar_sesion_sin_sesion_tambien_lleva_al_login(): void
    {
        $this->post('/cerrar-sesion')->assertRedirect('/iniciar-sesion');
    }

    public function test_las_paginas_con_sesion_y_las_de_acceso_no_se_guardan_en_cache(): void
    {
        $this->get('/iniciar-sesion')->assertHeader('Pragma', 'no-cache');
        $this->assertStringContainsString('no-store', $this->get('/registro')->headers->get('Cache-Control'));

        $respuesta = $this->actingAs($this->crearAutor())->get('/autor');
        $this->assertStringContainsString('no-store', $respuesta->headers->get('Cache-Control'));

        $respuesta = $this->actingAs($this->administrador())->get('/admin');
        $this->assertStringContainsString('no-store', $respuesta->headers->get('Cache-Control'));
    }

    public function test_el_sitio_publico_sin_sesion_si_puede_guardarse(): void
    {
        $this->assertStringNotContainsString('no-store', (string) $this->get('/')->headers->get('Cache-Control'));
    }

    public function test_tras_30_minutos_sin_actividad_la_sesion_se_cierra(): void
    {
        $this->actingAs($this->crearAutor())->get('/autor')->assertOk();

        $this->travel(31)->minutes();

        $this->get('/autor')
            ->assertRedirect('/iniciar-sesion')
            ->assertSessionHas('aviso', ExpiraPorInactividad::MENSAJE);

        $this->assertGuest();
    }

    public function test_con_actividad_antes_del_limite_la_sesion_sigue(): void
    {
        $this->actingAs($this->crearAutor())->get('/autor')->assertOk();

        $this->travel(29)->minutes();
        $this->post('/sesion/mantener')->assertNoContent();

        $this->travel(29)->minutes();
        $this->get('/autor')->assertOk();
    }

    public function test_tras_la_inactividad_una_pagina_publica_se_ve_como_visitante(): void
    {
        $this->actingAs($this->crearAutor())->get('/autor')->assertOk();

        $this->travel(31)->minutes();

        $this->get('/consultorio')->assertOk();
        $this->assertGuest();
        $this->getJson('/buscar/resultados?q=pymes')->assertOk();
    }

    public function test_una_peticion_json_vencida_recibe_401(): void
    {
        $this->actingAs($this->administrador())->get('/admin')->assertOk();

        $this->travel(31)->minutes();

        $this->getJson('/admin/autores/datos')
            ->assertUnauthorized()
            ->assertJson(['message' => ExpiraPorInactividad::MENSAJE]);
    }

    public function test_mantener_la_sesion_requiere_sesion(): void
    {
        $this->postJson('/sesion/mantener')->assertUnauthorized();
    }

    public function test_un_token_vencido_al_cerrar_sesion_lleva_al_login_con_aviso(): void
    {
        $this->exigirTokenCsrf();

        $this->post('/cerrar-sesion', ['motivo' => 'inactividad'])
            ->assertRedirect('/iniciar-sesion')
            ->assertSessionHas('aviso', ExpiraPorInactividad::MENSAJE);
    }

    public function test_un_token_vencido_en_el_login_pide_intentarlo_de_nuevo(): void
    {
        $this->exigirTokenCsrf();

        $this->post('/iniciar-sesion', ['acceso' => 'alguien', 'clave' => 'algo'])
            ->assertRedirect('/iniciar-sesion')
            ->assertSessionHas('aviso');
    }

    /** En las pruebas Laravel no revisa el token CSRF; aquí sí. */
    private function exigirTokenCsrf(): void
    {
        $this->app->singleton(PreventRequestForgery::class, fn ($app) => new class($app, $app['encrypter']) extends PreventRequestForgery
        {
            protected function runningUnitTests()
            {
                return false;
            }
        });
    }

    public function test_el_administrador_no_termina_en_el_panel_de_autor_desde_el_sitio(): void
    {
        // Recorrido reportado: el administrador vuelve al sitio, abre «Envío de
        // artículos» y de ahí intentaba entrar al panel de autor.
        $this->actingAs($this->administrador());

        $this->get('/envio-de-articulos')
            ->assertOk()
            ->assertSee('Sesión iniciada como')
            ->assertSee('Cerrar sesión y entrar como autor')
            ->assertDontSee(route('autor.articulos.create'));

        $this->get('/autor')->assertRedirect('/admin')->assertSessionHas('aviso');
        $this->get('/autor/articulos/nuevo')->assertRedirect('/admin');
    }

    public function test_el_autor_en_el_sitio_ve_su_panel_y_el_envio(): void
    {
        $this->actingAs($this->crearAutor());

        $this->get('/envio-de-articulos')
            ->assertOk()
            ->assertSee(route('autor.articulos.create'))
            ->assertDontSee('Cerrar sesión y entrar como autor');

        $this->get('/admin')->assertRedirect('/autor');
    }

    public function test_el_login_con_sesion_abierta_ofrece_continuar_o_cerrar(): void
    {
        $this->actingAs($this->administrador())
            ->get('/iniciar-sesion')
            ->assertOk()
            ->assertSee('Continuar como EmprendeUnam')
            ->assertSee('(administración)');
    }

    public function test_sin_sesion_el_sitio_no_muestra_la_franja(): void
    {
        $this->get('/envio-de-articulos')->assertOk()->assertDontSee('Sesión iniciada como');
    }
}
