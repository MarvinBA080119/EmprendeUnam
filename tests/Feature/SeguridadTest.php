<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

/** Correcciones de la revisión de seguridad (fase F). */
class SeguridadTest extends TestCase
{
    use CreaCuentas;

    public function test_el_filtro_de_autores_no_permite_inyectar_html(): void
    {
        $respuesta = $this->actingAs($this->administrador())
            ->get('/admin/autores?q='.urlencode("a'><img src=x onerror=alert(1)>"))
            ->assertOk();

        $respuesta->assertDontSee("a'><img", false);
        $respuesta->assertSee('a\u0027\u003E\u003Cimg', false);
    }

    public function test_las_respuestas_llevan_encabezados_de_seguridad(): void
    {
        $this->get('/iniciar-sesion')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Content-Security-Policy', "frame-ancestors 'self'")
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_hsts_solo_con_https(): void
    {
        $this->get('/iniciar-sesion')->assertHeaderMissing('Strict-Transport-Security');
        $this->get('https://localhost/iniciar-sesion')->assertHeader('Strict-Transport-Security', 'max-age=31536000');
    }

    public function test_un_autor_no_entra_a_ninguna_ruta_del_administrador(): void
    {
        $this->actingAs($this->crearAutor());

        foreach (['/admin', '/admin/articulos', '/admin/autores', '/admin/consultorio', '/admin/cursos', '/admin/newsletter', '/admin/autores/datos'] as $ruta) {
            $this->get($ruta)->assertRedirect('/autor');
            $this->getJson($ruta)->assertForbidden();
        }
        $this->post('/admin/newsletter/avisar', ['titulo' => 'x', 'enlace' => 'https://example.com'])->assertForbidden();
    }

    public function test_bloquea_una_cuenta_atacada_desde_muchas_ip(): void
    {
        $autor = $this->crearAutor();

        for ($i = 1; $i <= 20; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => "10.0.0.{$i}"])
                ->post('/iniciar-sesion', ['acceso' => $autor->correo(), 'clave' => 'incorrecta']);
        }

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.1.1'])
            ->post('/iniciar-sesion', ['acceso' => $autor->correo(), 'clave' => self::CLAVE_PRUEBA])
            ->assertSessionHasErrors('acceso');

        $this->assertGuest();
        $this->assertStringContainsString('Demasiados intentos', session('errors')->first('acceso'));
    }

    public function test_un_usuario_inexistente_recibe_el_mismo_mensaje(): void
    {
        $this->post('/iniciar-sesion', ['acceso' => 'nadie-registrado@example.com', 'clave' => 'lo-que-sea'])
            ->assertSessionHasErrors(['acceso' => __('auth.failed')]);
    }

    public function test_restablecer_la_clave_cierra_las_demas_sesiones(): void
    {
        $autor = $this->crearAutor(datos: ['correo' => 'sesiones@example.com']);
        DB::table('sessions')->insert([
            'id' => 'sesion-anterior', 'user_id' => $autor->usua_id_usuario, 'ip_address' => '10.0.0.1',
            'user_agent' => 'prueba', 'payload' => '', 'last_activity' => time(),
        ]);

        $this->post('/restablecer-clave', [
            'token' => Password::broker()->createToken($autor),
            'correo' => 'sesiones@example.com',
            'clave' => 'clave-totalmente-nueva',
            'clave_confirmation' => 'clave-totalmente-nueva',
        ])->assertRedirect('/iniciar-sesion');

        $this->assertFalse(DB::table('sessions')->where('id', 'sesion-anterior')->exists());
    }
}
