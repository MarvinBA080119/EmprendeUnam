<?php

namespace Tests\Feature\Auth;

use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use CreaCuentas;

    public function test_muestra_el_formulario(): void
    {
        $this->get('/iniciar-sesion')->assertOk()->assertSee('Correo o nombre de usuario');
    }

    public function test_un_autor_activo_entra_con_su_correo_y_va_a_autor(): void
    {
        $autor = $this->crearAutor(datos: ['correo' => 'Autora.Uno@Example.com']);

        $this->post('/iniciar-sesion', ['acceso' => 'AUTORA.UNO@example.com', 'clave' => self::CLAVE_PRUEBA])
            ->assertRedirect('/autor');

        $this->assertAuthenticatedAs($autor);
        $this->assertSame(today()->toDateString(), $autor->fresh()->usua_ultimo_login->toDateString());
    }

    public function test_un_autor_entra_con_su_nombre_de_usuario_sin_importar_mayusculas(): void
    {
        $autor = $this->crearAutor(datos: ['nombre_usuario' => 'AnaLopez']);

        $this->post('/iniciar-sesion', ['acceso' => 'analopez', 'clave' => self::CLAVE_PRUEBA])
            ->assertRedirect('/autor');

        $this->assertAuthenticatedAs($autor);
    }

    public function test_el_administrador_entra_como_emprendeunam_y_va_a_admin(): void
    {
        $admin = $this->administrador(conClaveDePrueba: true);

        $this->post('/iniciar-sesion', ['acceso' => 'EmprendeUnam', 'clave' => self::CLAVE_PRUEBA])
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($admin);
    }

    public function test_el_administrador_tambien_entra_con_su_correo(): void
    {
        $admin = $this->administrador(conClaveDePrueba: true);

        $this->post('/iniciar-sesion', ['acceso' => $admin->correo(), 'clave' => self::CLAVE_PRUEBA])
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($admin);
    }

    public function test_una_cuenta_inactiva_no_entra_aunque_la_clave_sea_correcta(): void
    {
        $autor = $this->crearAutor(activo: false);

        $this->from('/iniciar-sesion')
            ->post('/iniciar-sesion', ['acceso' => $autor->correo(), 'clave' => self::CLAVE_PRUEBA])
            ->assertRedirect('/iniciar-sesion')
            ->assertSessionHasErrors(['acceso' => 'Tu cuenta está pendiente de activación por el administrador.']);

        $this->assertGuest();
    }

    public function test_una_clave_incorrecta_no_entra(): void
    {
        $autor = $this->crearAutor();

        $this->post('/iniciar-sesion', ['acceso' => $autor->correo(), 'clave' => 'otra-clave-cualquiera'])
            ->assertSessionHasErrors('acceso');

        $this->assertGuest();
    }

    public function test_bloquea_despues_de_cinco_intentos_fallidos(): void
    {
        $autor = $this->crearAutor();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/iniciar-sesion', ['acceso' => $autor->correo(), 'clave' => 'incorrecta']);
        }

        $this->post('/iniciar-sesion', ['acceso' => $autor->correo(), 'clave' => self::CLAVE_PRUEBA])
            ->assertSessionHasErrors('acceso');

        $this->assertGuest();
        $this->assertStringContainsString('Demasiados intentos', session('errors')->first('acceso'));
    }

    public function test_cerrar_sesion(): void
    {
        $this->actingAs($this->crearAutor())
            ->post('/cerrar-sesion')
            ->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_quien_ya_inicio_sesion_va_a_su_panel_desde_el_login(): void
    {
        $this->actingAs($this->administrador())->get('/iniciar-sesion')->assertRedirect('/admin');
    }
}
