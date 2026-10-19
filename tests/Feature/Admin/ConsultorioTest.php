<?php

namespace Tests\Feature\Admin;

use App\Enums\EstatusMensaje;
use App\Mail\RespuestaConsultorio;
use App\Models\Mensaje;
use App\Services\ConsultorioService;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\CreaContenido;
use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

class ConsultorioTest extends TestCase
{
    use CreaContenido, CreaCuentas;

    public function test_la_bandeja_muestra_solo_mensajes_de_lectores_y_filtra_por_estatus(): void
    {
        $pendiente = $this->crearMensaje(['mens_asunto' => 'Pendiente zz']);
        $rechazado = $this->crearMensaje(['mens_asunto' => 'Rechazado zz', 'mens_status' => EstatusMensaje::RECHAZADO]);
        Mail::fake();
        app(ConsultorioService::class)->responder($this->crearMensaje(['mens_asunto' => 'Contestado zz']), 'Respuesta.', $this->administrador());

        $asuntos = fn (string $q) => collect($this->actingAs($this->administrador())
            ->getJson('/admin/consultorio/datos?'.$q)->assertOk()->json('datos'))->pluck('asunto')->sort()->values()->all();

        $this->assertSame(['Contestado zz', 'Pendiente zz', 'Rechazado zz'], $asuntos('q=zz'));
        $this->assertSame(['Pendiente zz'], $asuntos('q=zz&estatus=PENDIENTE'));
        $this->assertNotContains('Re: Contestado zz', $asuntos('q=Re:'));
    }

    public function test_responder_guarda_la_respuesta_ligada_y_avisa_al_lector(): void
    {
        $admin = $this->administrador();
        $original = $this->crearMensaje([
            'mens_correo' => 'lector@example.com',
            'mens_asunto' => str_repeat('a', 49),
        ]);
        Mail::fake();

        $this->actingAs($admin)
            ->post("/admin/consultorio/{$original->mens_id_mensaje}/responder", ['respuesta' => 'Debes acudir al IMPI con tu solicitud.'])
            ->assertRedirect("/admin/consultorio/{$original->mens_id_mensaje}")
            ->assertSessionHas('exito');

        $respuesta = Mensaje::where('men_mens_id_mensaje', $original->mens_id_mensaje)->firstOrFail();
        $this->assertSame($admin->usua_id_usuario, $respuesta->usua_id_usuario);
        $this->assertSame('Consultorio EmprendeUNAM', $respuesta->mens_remitente);
        $this->assertSame($admin->correo(), $respuesta->mens_correo);
        $this->assertSame(50, mb_strlen($respuesta->mens_asunto));
        $this->assertStringStartsWith('Re: ', $respuesta->mens_asunto);
        $this->assertSame(EstatusMensaje::CONTESTADO, $respuesta->mens_status);
        $this->assertSame(EstatusMensaje::CONTESTADO, $original->fresh()->mens_status);

        Mail::assertQueued(RespuestaConsultorio::class, fn ($m) => $m->hasTo('lector@example.com')
            && $m->respuesta === 'Debes acudir al IMPI con tu solicitud.');
    }

    public function test_la_respuesta_es_obligatoria_y_de_maximo_700_caracteres(): void
    {
        $original = $this->crearMensaje();

        $this->actingAs($this->administrador())
            ->post("/admin/consultorio/{$original->mens_id_mensaje}/responder", ['respuesta' => str_repeat('a', 701)])
            ->assertSessionHasErrors('respuesta');
        $this->actingAs($this->administrador())
            ->post("/admin/consultorio/{$original->mens_id_mensaje}/responder", ['respuesta' => ''])
            ->assertSessionHasErrors('respuesta');

        $this->assertSame(EstatusMensaje::PENDIENTE, $original->fresh()->mens_status);
    }

    public function test_rechazar_un_mensaje(): void
    {
        $original = $this->crearMensaje();
        Mail::fake();

        $this->actingAs($this->administrador())
            ->post("/admin/consultorio/{$original->mens_id_mensaje}/rechazar")
            ->assertRedirect('/admin/consultorio');

        $this->assertSame(EstatusMensaje::RECHAZADO, $original->fresh()->mens_status);
        $this->assertFalse(Mensaje::where('men_mens_id_mensaje', $original->mens_id_mensaje)->exists());
        Mail::assertNothingQueued();
    }

    public function test_no_se_responde_un_mensaje_ya_atendido(): void
    {
        $original = $this->crearMensaje(['mens_status' => EstatusMensaje::RECHAZADO]);

        $this->actingAs($this->administrador())
            ->post("/admin/consultorio/{$original->mens_id_mensaje}/responder", ['respuesta' => 'Hola'])
            ->assertSessionHasErrors('mensaje');

        $this->assertFalse(Mensaje::where('men_mens_id_mensaje', $original->mens_id_mensaje)->exists());
    }

    public function test_el_detalle_de_una_respuesta_no_existe_como_mensaje_de_bandeja(): void
    {
        Mail::fake();
        $respuesta = app(ConsultorioService::class)->responder($this->crearMensaje(), 'Respuesta.', $this->administrador());

        $this->actingAs($this->administrador())->get("/admin/consultorio/{$respuesta->mens_id_mensaje}")->assertNotFound();
    }
}
