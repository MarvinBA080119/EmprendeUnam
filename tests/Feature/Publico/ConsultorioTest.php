<?php

namespace Tests\Feature\Publico;

use App\Enums\EstatusMensaje;
use App\Models\Mensaje;
use App\Services\ConsultorioService;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\CreaContenido;
use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

class ConsultorioTest extends TestCase
{
    use CreaContenido, CreaCuentas;

    /** @return array<string, string> */
    private function datos(array $cambios = []): array
    {
        return array_merge([
            'nombre' => 'Rosa Elena Díaz',
            'correo' => 'Rosa.Diaz@Example.com',
            'lugar' => 'Oaxaca',
            'asunto' => 'Exportar mezcal a Canadá',
            'mensaje' => 'Tengo una pequeña destilería y quiero exportar. ¿Por dónde empiezo?',
        ], $cambios);
    }

    public function test_un_lector_envia_un_mensaje_que_queda_pendiente(): void
    {
        $this->post('/consultorio', $this->datos())
            ->assertRedirect(route('consultorio').'#enviar')
            ->assertSessionHas('exito');

        $mensaje = Mensaje::where('mens_asunto', 'Exportar mezcal a Canadá')->firstOrFail();
        $this->assertNull($mensaje->usua_id_usuario);
        $this->assertNull($mensaje->men_mens_id_mensaje);
        $this->assertSame(EstatusMensaje::PENDIENTE, $mensaje->mens_status);
        $this->assertSame('rosa.diaz@example.com', $mensaje->mens_correo);
        $this->assertSame('Oaxaca', $mensaje->mens_lugar);
    }

    public function test_valida_campos_y_longitudes(): void
    {
        $this->post('/consultorio', $this->datos([
            'nombre' => '',
            'correo' => 'no-es-correo',
            'asunto' => str_repeat('a', 51),
            'mensaje' => str_repeat('a', 701),
        ]))->assertSessionHasErrors(['nombre', 'correo', 'asunto', 'mensaje']);
    }

    public function test_el_campo_trampa_no_guarda_nada_pero_responde_igual(): void
    {
        $antes = Mensaje::count();

        $this->post('/consultorio', $this->datos(['sitio_web' => 'http://spam.example']))
            ->assertSessionHas('exito');

        $this->assertSame($antes, Mensaje::count());
    }

    public function test_limita_a_3_envios_por_hora_por_ip(): void
    {
        for ($i = 1; $i <= ConsultorioService::ENVIOS_POR_HORA; $i++) {
            $this->post('/consultorio', $this->datos(['asunto' => "Consulta {$i}"]))->assertSessionHas('exito');
        }

        $this->post('/consultorio', $this->datos(['asunto' => 'Consulta 4']))->assertSessionHasErrors('mensaje');
        $this->assertDatabaseMissing('mensaje', ['mens_asunto' => 'Consulta 4']);
    }

    public function test_la_seccion_publica_muestra_solo_contestados_con_su_respuesta_y_sin_correos(): void
    {
        $contestado = $this->crearMensaje(['mens_asunto' => 'Pregunta contestada', 'mens_correo' => 'visible.no@example.com']);
        $this->crearMensaje(['mens_asunto' => 'Pregunta pendiente']);
        $this->crearMensaje(['mens_asunto' => 'Pregunta rechazada', 'mens_status' => EstatusMensaje::RECHAZADO]);
        Mail::fake();
        app(ConsultorioService::class)->responder($contestado, "Primer párrafo de la respuesta.\n\nSegundo párrafo.", $this->administrador());

        $this->get('/consultorio')
            ->assertOk()
            ->assertSee('Pregunta contestada')
            ->assertSee('Primer párrafo de la respuesta.')
            ->assertSee('<p>Segundo párrafo.</p>', false)
            ->assertSee('Lectora Curiosa')
            ->assertDontSee('Pregunta pendiente')
            ->assertDontSee('Pregunta rechazada')
            ->assertDontSee('visible.no@example.com')
            // El archivo del sitio anterior se conserva al final.
            ->assertSee('Valuación de la empresa para vender una parte');
    }
}
