<?php

namespace Tests\Feature;

use App\Mail\ArticuloAprobado;
use App\Mail\ArticuloRechazado;
use App\Mail\ArticuloRecibido;
use App\Mail\AvisoNuevaEdicion;
use App\Mail\ConfirmarSuscripcion;
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

    public function test_articulo_recibido(): void
    {
        $correo = new ArticuloRecibido('Ana', 'Mi artículo');

        $correo->assertHasSubject('Recibimos tu artículo');
        $correo->assertSeeInHtml('Mi artículo');
        $correo->assertSeeInHtml('En revisión');
    }

    public function test_articulo_aprobado_lleva_folio_y_enlace(): void
    {
        $correo = new ArticuloAprobado('Ana', 'Mi artículo', 'EMP-2026-0007', 'https://revista.test/autor/articulos/7/aprobado');

        $correo->assertHasSubject('Tu artículo fue aprobado');
        $correo->assertSeeInHtml('EMP-2026-0007');
        $correo->assertSeeInHtml('https://revista.test/autor/articulos/7/aprobado');
    }

    public function test_articulo_rechazado_lleva_el_motivo(): void
    {
        $correo = new ArticuloRechazado('Ana', 'Mi artículo', 'No cumple con las normas editoriales.');

        $correo->assertHasSubject('Dictamen de tu artículo');
        $correo->assertSeeInHtml('No cumple con las normas editoriales.');
    }

    public function test_confirmar_suscripcion_lleva_enlaces_de_confirmacion_y_baja(): void
    {
        $correo = new ConfirmarSuscripcion('Sofía', 'https://revista.test/newsletter/confirmar/abc', 'https://revista.test/newsletter/baja/abc');

        $correo->assertHasSubject('Confirma tu suscripción al newsletter EmprendeUNAM');
        $correo->assertSeeInHtml('https://revista.test/newsletter/confirmar/abc');
        $correo->assertSeeInHtml('https://revista.test/newsletter/baja/abc');
    }

    public function test_aviso_de_nueva_edicion_lleva_liga_y_baja(): void
    {
        $correo = new AvisoNuevaEdicion('Sofía', 'Edición 02', 'https://revista.test/newsletter/02', 'https://revista.test/newsletter/baja/abc');

        $correo->assertHasSubject('Ya está disponible: Edición 02');
        $correo->assertSeeInHtml('https://revista.test/newsletter/02');
        $correo->assertSeeInHtml('darte de baja aquí');
    }
}
