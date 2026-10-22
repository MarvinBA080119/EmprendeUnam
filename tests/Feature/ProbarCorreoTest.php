<?php

namespace Tests\Feature;

use App\Mail\CorreoPrueba;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ProbarCorreoTest extends TestCase
{
    public function test_rechaza_un_destino_invalido(): void
    {
        $this->artisan('emprende:probar-correo', ['destino' => 'no-es-correo'])->assertFailed();
    }

    public function test_avisa_que_con_log_o_array_no_se_envia_nada(): void
    {
        config(['mail.default' => 'log']);

        $this->artisan('emprende:probar-correo', ['destino' => 'prueba@example.com'])
            ->expectsOutputToContain('los correos NO se envían')
            ->assertFailed();
    }

    public function test_con_smtp_envia_la_prueba_en_el_momento(): void
    {
        config(['mail.default' => 'smtp', 'mail.mailers.smtp.password' => 'secreta']);
        Mail::fake();

        $this->artisan('emprende:probar-correo', ['destino' => 'prueba@example.com'])
            ->doesntExpectOutputToContain('secreta')
            ->assertSuccessful();

        Mail::assertSent(CorreoPrueba::class, fn ($correo) => $correo->hasTo('prueba@example.com'));
    }

    public function test_con_la_opcion_cola_la_prueba_va_por_la_cola(): void
    {
        config(['mail.default' => 'smtp']);
        Mail::fake();

        $this->artisan('emprende:probar-correo', ['destino' => 'prueba@example.com', '--cola' => true])->assertSuccessful();

        Mail::assertQueued(CorreoPrueba::class);
    }
}
