<?php

namespace Tests\Feature\Auth;

use App\Mail\RecuperarClave;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

class RecuperarClaveTest extends TestCase
{
    use CreaCuentas;

    public function test_envia_el_enlace_al_correo_de_comunicacion(): void
    {
        $autor = $this->crearAutor(datos: ['correo' => 'recupera@example.com']);
        Mail::fake();

        $this->post('/recuperar-clave', ['correo' => 'Recupera@Example.com'])
            ->assertSessionHas('exito');

        Mail::assertQueued(RecuperarClave::class, fn (RecuperarClave $m) => $m->hasTo('recupera@example.com')
            && str_contains($m->enlace, '/restablecer-clave/'));
        $this->assertDatabaseHas('password_reset_tokens', ['email' => 'recupera@example.com']);
    }

    public function test_responde_igual_si_el_correo_no_existe(): void
    {
        Mail::fake();

        $this->post('/recuperar-clave', ['correo' => 'nadie@example.com'])
            ->assertSessionHas('exito', 'Si el correo está registrado, te enviamos un enlace para restablecer tu contraseña.');

        Mail::assertNothingQueued();
    }

    public function test_restablece_la_clave_con_un_token_valido(): void
    {
        $autor = $this->crearAutor(datos: ['correo' => 'nueva.clave@example.com']);
        $token = Password::broker()->createToken($autor);

        $this->post('/restablecer-clave', [
            'token' => $token,
            'correo' => 'nueva.clave@example.com',
            'clave' => 'clave-totalmente-nueva',
            'clave_confirmation' => 'clave-totalmente-nueva',
        ])->assertRedirect('/iniciar-sesion');

        $this->assertTrue(Hash::check('clave-totalmente-nueva', $autor->fresh()->usua_clave_acceso));
        $this->assertSame(0, DB::table('password_reset_tokens')->where('email', 'nueva.clave@example.com')->count());
    }

    public function test_rechaza_un_token_invalido(): void
    {
        $autor = $this->crearAutor(datos: ['correo' => 'token.malo@example.com']);

        $this->post('/restablecer-clave', [
            'token' => 'token-falso',
            'correo' => 'token.malo@example.com',
            'clave' => 'clave-totalmente-nueva',
            'clave_confirmation' => 'clave-totalmente-nueva',
        ])->assertSessionHasErrors('correo');

        $this->assertTrue(Hash::check(self::CLAVE_PRUEBA, $autor->fresh()->usua_clave_acceso));
    }
}
