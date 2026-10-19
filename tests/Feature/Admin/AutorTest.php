<?php

namespace Tests\Feature\Admin;

use App\Mail\CuentaActivada;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

class AutorTest extends TestCase
{
    use CreaCuentas;

    public function test_la_pagina_monta_la_tabla_vue(): void
    {
        $this->actingAs($this->administrador())
            ->get('/admin/autores?estatus=pendiente')
            ->assertOk()
            ->assertSee('id="tabla-autores"', false)
            ->assertSee('admin\/autores\/datos', false);
    }

    public function test_lista_autores_con_su_estatus_en_json(): void
    {
        $this->crearAutor(activo: false, datos: ['nombre' => 'Pendencio']);
        $this->crearAutor(datos: ['nombre' => 'Activia']);

        $respuesta = $this->actingAs($this->administrador())->getJson('/admin/autores/datos?q=')->assertOk();

        $nombres = collect($respuesta->json('datos'))->pluck('activo', 'nombre');
        $this->assertFalse($nombres['Pendencio López Ruiz']);
        $this->assertTrue($nombres['Activia López Ruiz']);
        $respuesta->assertJsonStructure(['datos' => [['id', 'nombre', 'usuario', 'correo', 'institucion', 'registro', 'activo', 'urlActivar']], 'meta' => ['pagina', 'ultima', 'total']]);
    }

    public function test_filtra_por_estatus_pendiente(): void
    {
        $this->crearAutor(activo: false, datos: ['nombre' => 'Pendencio']);
        $this->crearAutor(datos: ['nombre' => 'Activia']);

        $nombres = collect($this->actingAs($this->administrador())
            ->getJson('/admin/autores/datos?estatus=pendiente')
            ->json('datos'))->pluck('nombre');

        $this->assertContains('Pendencio López Ruiz', $nombres);
        $this->assertNotContains('Activia López Ruiz', $nombres);
    }

    public function test_busca_sin_importar_mayusculas_ni_acentos(): void
    {
        $this->crearAutor(datos: ['nombre' => 'Begoña', 'primer_apellido' => 'Martínez']);

        $nombres = collect($this->actingAs($this->administrador())
            ->getJson('/admin/autores/datos?q=MARTINEZ')
            ->json('datos'))->pluck('nombre');

        $this->assertContains('Begoña Martínez Ruiz', $nombres);
    }

    public function test_el_administrador_no_aparece_en_la_lista(): void
    {
        $correos = collect($this->actingAs($this->administrador())
            ->getJson('/admin/autores/datos')
            ->json('datos'))->pluck('correo');

        $this->assertNotContains('admin@emprendeunam.invalid', $correos);
    }

    public function test_activar_desde_la_tabla_vue_responde_json(): void
    {
        $autor = $this->crearAutor(activo: false);
        Mail::fake();

        $this->actingAs($this->administrador())
            ->postJson("/admin/autores/{$autor->usua_id_usuario}/activar")
            ->assertOk()
            ->assertJson(['activo' => true]);

        $this->assertTrue($autor->fresh()->usua_activo);
    }

    public function test_activar_una_cuenta_avisa_por_correo_y_permite_entrar(): void
    {
        $autor = $this->crearAutor(activo: false, datos: ['correo' => 'por.activar@example.com']);
        Mail::fake();

        $this->actingAs($this->administrador())
            ->post("/admin/autores/{$autor->usua_id_usuario}/activar")
            ->assertSessionHas('exito');

        $this->assertTrue($autor->fresh()->usua_activo);
        Mail::assertQueued(CuentaActivada::class, fn ($m) => $m->hasTo('por.activar@example.com'));

        auth()->logout();
        $this->post('/iniciar-sesion', ['acceso' => 'por.activar@example.com', 'clave' => self::CLAVE_PRUEBA])
            ->assertRedirect('/autor');
    }

    public function test_activar_una_cuenta_ya_activa_no_reenvia_el_correo(): void
    {
        $autor = $this->crearAutor();
        Mail::fake();

        $this->actingAs($this->administrador())
            ->post("/admin/autores/{$autor->usua_id_usuario}/activar")
            ->assertSessionHas('aviso');

        Mail::assertNothingQueued();
    }

    public function test_no_se_puede_activar_al_administrador_por_esta_ruta(): void
    {
        $admin = $this->administrador();

        $this->actingAs($admin)
            ->post("/admin/autores/{$admin->usua_id_usuario}/activar")
            ->assertNotFound();
    }

    public function test_un_autor_no_puede_activar_cuentas(): void
    {
        $pendiente = $this->crearAutor(activo: false);

        $this->actingAs($this->crearAutor())
            ->post("/admin/autores/{$pendiente->usua_id_usuario}/activar")
            ->assertForbidden();

        $this->assertFalse($pendiente->fresh()->usua_activo);
    }
}
