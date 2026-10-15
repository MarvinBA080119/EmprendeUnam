<?php

namespace Tests\Feature;

use App\Mail\CuentaActivada;
use App\Mail\CuentaRecibida;
use App\Mail\RecuperarClave;
use Tests\TestCase;

class CorreosTest extends TestCase
{
    public function test_cuenta_recibida(): void
    {
        $correo = new CuentaRecibida('Ana');

        $correo->assertHasSubject('Recibimos tu registro como autor');
        $correo->assertSeeInHtml('Hola, Ana:');
        $correo->assertSeeInHtml('#001328');
    }

    public function test_cuenta_activada_lleva_el_enlace_de_inicio_de_sesion(): void
    {
        $correo = new CuentaActivada('Ana', 'ana@example.com');

        $correo->assertHasSubject('Tu cuenta fue activada');
        $correo->assertSeeInHtml(route('login'));
        $correo->assertSeeInHtml('#664300');
    }

    public function test_recuperar_clave_lleva_el_enlace_y_la_vigencia(): void
    {
        $correo = new RecuperarClave('Ana', 'https://revista.test/restablecer-clave/abc', 60);

        $correo->assertHasSubject('Restablece tu contraseña');
        $correo->assertSeeInHtml('https://revista.test/restablecer-clave/abc');
        $correo->assertSeeInHtml('60 minutos');
    }

    public function test_los_datos_del_usuario_se_escapan(): void
    {
        (new CuentaRecibida('<script>alert(1)</script>'))
            ->assertDontSeeInHtml('<script>alert(1)</script>', false);
    }
}
