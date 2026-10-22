<?php

namespace Tests\Feature\Publico;

use App\Mail\ConfirmarSuscripcion;
use App\Models\Comunicacion;
use App\Models\Persona;
use App\Models\TemaNews;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\CreaContenido;
use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use CreaContenido, CreaCuentas;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    /** @return array<string, string> */
    private function datos(array $cambios = []): array
    {
        return array_merge([
            'nombre' => 'Valeria',
            'apellido' => 'Torres',
            'correo' => 'Valeria.Torres@Example.com',
            'tema' => 'Análisis de mercados',
            'acepta' => '1',
        ], $cambios);
    }

    private function personaDe(string $correo): Persona
    {
        return Persona::findOrFail(Comunicacion::correos()->conValor($correo)->value('pers_id_persona'));
    }

    public function test_suscripcion_completa_en_una_transaccion(): void
    {
        $this->post('/newsletter/suscribir', $this->datos())
            ->assertRedirect('/suscripcion')
            ->assertSessionHas('exito');

        // El trigger diferido tr_newsletter_completa se revisa aquí.
        DB::statement('SET CONSTRAINTS ALL IMMEDIATE');

        $persona = $this->personaDe('valeria.torres@example.com');
        $this->assertTrue($persona->pers_subs_newsletter);
        $this->assertTrue($persona->pers_acepta_aviso);
        $this->assertNotNull($persona->pers_fecha_suscripcion);
        $this->assertNull($persona->pers_fecha_confirmacion);
        $this->assertNull($persona->pers_fecha_baja);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $persona->pers_token_confirmacion);
        $this->assertSame('Newsletter', $persona->correo->comu_etiqueta);
        $this->assertSame(['Análisis de mercados'], $persona->temasNews->pluck('tene_tema_interes')->all());
        $this->assertFalse(Persona::suscriptoresActivos()->whereKey($persona->getKey())->exists());

        Mail::assertQueued(ConfirmarSuscripcion::class, fn ($m) => $m->hasTo('valeria.torres@example.com')
            && str_contains($m->enlaceConfirmar, $persona->pers_token_confirmacion));
    }

    public function test_la_ventana_del_newsletter_recibe_json(): void
    {
        $this->postJson('/newsletter/suscribir', $this->datos())
            ->assertOk()
            ->assertJson(['ok' => true, 'nombre' => 'Valeria']);

        $this->postJson('/newsletter/suscribir', $this->datos(['correo' => 'mal', 'acepta' => '0']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['correo', 'acepta']);
    }

    public function test_exige_aviso_de_privacidad_y_tema(): void
    {
        $this->post('/newsletter/suscribir', $this->datos(['acepta' => '', 'tema' => '   ']))
            ->assertSessionHasErrors([
                'acepta' => 'Para suscribirte es necesario aceptar el aviso de privacidad.',
                'tema' => 'Cuéntanos qué tema te interesa.',
            ]);

        $this->post('/newsletter/suscribir', $this->datos(['tema' => str_repeat('a', 121)]))->assertSessionHasErrors('tema');

        $this->assertFalse(Comunicacion::correos()->conValor('valeria.torres@example.com')->exists());
    }

    public function test_el_tema_es_texto_libre_y_se_limpia(): void
    {
        $this->post('/newsletter/suscribir', $this->datos(['tema' => "  <b>Exportación</b>   de\tartesanías  "]))
            ->assertSessionHas('exito');

        $this->assertSame(
            ['Exportación de artesanías'],
            $this->personaDe('valeria.torres@example.com')->temasNews->pluck('tene_tema_interes')->all(),
        );
    }

    public function test_un_correo_ya_suscrito_y_confirmado_no_se_duplica(): void
    {
        $persona = $this->crearSuscriptor('activo', ['correo' => 'ya.suscrita@example.com']);
        $comunicaciones = Comunicacion::count();
        $temas = TemaNews::count();

        $this->post('/newsletter/suscribir', $this->datos(['correo' => 'YA.SUSCRITA@example.com', 'tema' => 'Comunicación']))
            ->assertSessionHas('aviso', 'Este correo ya está suscrito. Te seguiremos avisando de cada nueva edición.');

        $this->assertSame($comunicaciones, Comunicacion::count());
        $this->assertSame($temas, TemaNews::count());
        Mail::assertNothingQueued();
    }

    public function test_una_suscripcion_sin_confirmar_reenvia_la_confirmacion(): void
    {
        $persona = $this->crearSuscriptor('sin_confirmar', ['correo' => 'pendiente@example.com']);
        $tokenAnterior = $persona->pers_token_confirmacion;

        $this->post('/newsletter/suscribir', $this->datos(['correo' => 'pendiente@example.com']))
            ->assertSessionHas('exito', 'Te reenviamos el correo de confirmación a pendiente@example.com.');

        $this->assertNotSame($tokenAnterior, $persona->fresh()->pers_token_confirmacion);
        Mail::assertQueued(ConfirmarSuscripcion::class, 1);
    }

    public function test_quien_se_dio_de_baja_se_reactiva_sin_duplicar_tema(): void
    {
        $persona = $this->crearSuscriptor('baja', ['correo' => 'regresa@example.com', 'tema' => 'Comunicación']);

        $this->post('/newsletter/suscribir', $this->datos(['correo' => 'regresa@example.com', 'tema' => 'Comunicación']));
        DB::statement('SET CONSTRAINTS ALL IMMEDIATE');

        $persona->refresh();
        $this->assertTrue($persona->pers_subs_newsletter);
        $this->assertNull($persona->pers_fecha_baja);
        $this->assertNull($persona->pers_fecha_confirmacion); // debe confirmar de nuevo
        $this->assertSame(1, $persona->temasNews()->count());
    }

    public function test_un_autor_registrado_se_suscribe_con_su_mismo_correo(): void
    {
        $autor = $this->crearAutor(datos: ['correo' => 'autora.lectora@example.com']);
        Mail::fake();

        $this->post('/newsletter/suscribir', $this->datos(['correo' => 'autora.lectora@example.com']))->assertSessionHas('exito');
        DB::statement('SET CONSTRAINTS ALL IMMEDIATE');

        $this->assertTrue($autor->persona->fresh()->pers_subs_newsletter);
        $this->assertSame(1, Comunicacion::correos()->conValor('autora.lectora@example.com')->count());
    }

    public function test_el_campo_trampa_no_guarda_nada(): void
    {
        $this->post('/newsletter/suscribir', $this->datos(['sitio_web' => 'spam']))->assertSessionHas('exito');

        $this->assertFalse(Comunicacion::correos()->conValor('valeria.torres@example.com')->exists());
        Mail::assertNothingQueued();
    }

    public function test_la_confirmacion_activa_al_suscriptor(): void
    {
        $this->post('/newsletter/suscribir', $this->datos());
        $persona = $this->personaDe('valeria.torres@example.com');

        $this->get("/newsletter/confirmar/{$persona->pers_token_confirmacion}")
            ->assertOk()
            ->assertSee('¡Suscripción confirmada!');

        $this->assertNotNull($persona->fresh()->pers_fecha_confirmacion);
        $this->assertTrue(Persona::suscriptoresActivos()->whereKey($persona->getKey())->exists());

        $this->get("/newsletter/confirmar/{$persona->pers_token_confirmacion}")->assertSee('ya estaba confirmada');
    }

    public function test_el_enlace_de_confirmacion_vence_y_la_suscripcion_sigue_pendiente(): void
    {
        $this->post('/newsletter/suscribir', $this->datos());
        $persona = $this->personaDe('valeria.torres@example.com');

        $this->travel(49)->hours();

        $this->get("/newsletter/confirmar/{$persona->pers_token_confirmacion}")
            ->assertOk()
            ->assertSee('El enlace de confirmación venció');

        $this->assertNull($persona->fresh()->pers_fecha_confirmacion);

        // Al suscribirse otra vez llega un enlace nuevo que sí funciona.
        $this->post('/newsletter/suscribir', $this->datos());
        $nuevo = $persona->fresh()->pers_token_confirmacion;
        $this->assertNotSame($persona->pers_token_confirmacion, $nuevo);
        $this->get("/newsletter/confirmar/{$nuevo}")->assertSee('¡Suscripción confirmada!');
    }

    public function test_el_enlace_de_baja_no_vence(): void
    {
        $this->post('/newsletter/suscribir', $this->datos());
        $persona = $this->personaDe('valeria.torres@example.com');

        $this->travel(30)->days();

        $this->get("/newsletter/baja/{$persona->pers_token_confirmacion}")
            ->assertOk()
            ->assertSee('¿Dejar de recibir el newsletter?');
    }

    public function test_un_token_desconocido_no_es_valido(): void
    {
        $this->get('/newsletter/confirmar/'.str_repeat('a', 64))->assertOk()->assertSee('Este enlace ya no es válido');
        $this->get('/newsletter/baja/'.str_repeat('b', 64))->assertOk()->assertSee('Este enlace ya no es válido');
    }

    public function test_la_baja_pide_confirmacion_y_funciona_sin_sesion(): void
    {
        $persona = $this->crearSuscriptor('activo', ['correo' => 'se.va@example.com']);
        $token = $persona->pers_token_confirmacion;

        // Abrir el enlace no da de baja (los filtros antispam visitan los enlaces).
        $this->get("/newsletter/baja/{$token}")->assertOk()->assertSee('Sí, darme de baja');
        $this->assertTrue($persona->fresh()->pers_subs_newsletter);

        $this->post("/newsletter/baja/{$token}")->assertOk()->assertSee('Ya no recibirás avisos');

        $persona->refresh();
        $this->assertFalse($persona->pers_subs_newsletter);
        $this->assertNotNull($persona->pers_fecha_baja);
        $this->assertFalse(Persona::suscriptoresActivos()->whereKey($persona->getKey())->exists());

        $this->get("/newsletter/baja/{$token}")->assertSee('Ya estabas dado de baja');
    }
}
