<?php

namespace Tests\Feature\Auth;

use App\Mail\CuentaRecibida;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

class RegistroTest extends TestCase
{
    use CreaCuentas;

    /** @return array<string, string> */
    private function datos(array $cambios = []): array
    {
        return array_merge([
            'nombre' => 'Luis',
            'primer_apellido' => 'Pérez',
            'segundo_apellido' => '',
            'correo' => 'Luis.Perez@Example.com',
            'clave' => 'una-clave-segura',
            'clave_confirmation' => 'una-clave-segura',
            'institucion' => 'Facultad de Contaduría y Administración',
            'grado_academico' => '',
            'nombre_usuario' => '',
        ], $cambios);
    }

    public function test_registro_completo_crea_persona_correo_usuario_autor_y_rol_en_una_transaccion(): void
    {
        Mail::fake();

        $this->post('/registro', $this->datos())
            ->assertRedirect('/iniciar-sesion')
            ->assertSessionHas('exito', 'Tu registro se recibió; el administrador activará tu cuenta.');

        // El trigger diferido tr_usuario_con_correo se revisa aquí.
        DB::statement('SET CONSTRAINTS ALL IMMEDIATE');

        $usuario = Usuario::with(['persona.correo', 'persona.autor', 'roles'])
            ->where('usua_nombre_usuario', 'luis.perez')
            ->firstOrFail();

        $this->assertFalse($usuario->usua_activo);
        $this->assertSame(today()->toDateString(), $usuario->usua_fecha_registro->toDateString());
        $this->assertTrue(Hash::check('una-clave-segura', $usuario->usua_clave_acceso));
        $this->assertSame('Luis', $usuario->persona->pers_nombre);
        $this->assertNull($usuario->persona->pers_segundo_apellido);
        $this->assertSame('luis.perez@example.com', $usuario->persona->correo->comu_valor_comunicacion);
        $this->assertSame('Principal', $usuario->persona->correo->comu_etiqueta);
        $this->assertSame('Facultad de Contaduría y Administración', $usuario->persona->autor->auto_institucion);
        $this->assertNull($usuario->persona->autor->auto_id_coautor);
        $this->assertSame(['Autor'], $usuario->roles->pluck('rol_nombre')->all());

        Mail::assertQueued(CuentaRecibida::class, fn ($m) => $m->hasTo('luis.perez@example.com'));
    }

    public function test_respeta_el_nombre_de_usuario_elegido(): void
    {
        Mail::fake();

        $this->post('/registro', $this->datos(['nombre_usuario' => 'LuisP']))->assertRedirect('/iniciar-sesion');

        $this->assertDatabaseHas('usuario', ['usua_nombre_usuario' => 'LuisP']);
    }

    public function test_genera_un_nombre_de_usuario_unico_si_el_del_correo_ya_existe(): void
    {
        $this->crearAutor(datos: ['correo' => 'luis.perez@otro.mx']);   // queda como luis.perez
        Mail::fake();

        $this->post('/registro', $this->datos())->assertRedirect('/iniciar-sesion');

        $this->assertDatabaseHas('usuario', ['usua_nombre_usuario' => 'luis.perez2']);
    }

    public function test_un_correo_repetido_da_error_claro_y_no_crea_nada(): void
    {
        $this->crearAutor(datos: ['correo' => 'luis.perez@example.com']);
        $personas = DB::table('persona')->count();

        $this->from('/registro')
            ->post('/registro', $this->datos(['correo' => 'LUIS.PEREZ@example.com']))
            ->assertRedirect('/registro')
            ->assertSessionHasErrors(['correo' => 'Este correo ya está registrado.']);

        $this->assertSame($personas, DB::table('persona')->count());
    }

    public function test_el_correo_del_administrador_tampoco_se_puede_usar(): void
    {
        $this->post('/registro', $this->datos(['correo' => $this->administrador()->correo()]))
            ->assertSessionHasErrors('correo');
    }

    public function test_un_nombre_de_usuario_repetido_da_error(): void
    {
        $this->post('/registro', $this->datos(['nombre_usuario' => 'emprendeunam']))
            ->assertSessionHasErrors(['nombre_usuario' => 'Este nombre de usuario ya está en uso.']);
    }

    public function test_valida_campos_obligatorios_y_longitudes(): void
    {
        $this->post('/registro', $this->datos([
            'nombre' => '   ',
            'primer_apellido' => '',
            'correo' => 'no-es-correo',
            'clave' => 'corta',
            'clave_confirmation' => 'otra',
            'institucion' => str_repeat('a', 101),
        ]))->assertSessionHasErrors(['nombre', 'primer_apellido', 'correo', 'clave', 'institucion']);

        $this->assertDatabaseMissing('comunicacion', ['comu_valor_comunicacion' => 'no-es-correo']);
    }

    public function test_los_mensajes_de_validacion_estan_en_espanol(): void
    {
        $this->post('/registro', $this->datos(['nombre' => '']))
            ->assertSessionHasErrors(['nombre' => 'Escribe tu nombre.']);
    }

    public function test_un_autor_recien_registrado_no_puede_entrar_hasta_ser_activado(): void
    {
        Mail::fake();
        $this->post('/registro', $this->datos());

        $this->post('/iniciar-sesion', ['acceso' => 'luis.perez@example.com', 'clave' => 'una-clave-segura'])
            ->assertSessionHasErrors(['acceso' => 'Tu cuenta está pendiente de activación por el administrador.']);
        $this->assertGuest();
    }
}
