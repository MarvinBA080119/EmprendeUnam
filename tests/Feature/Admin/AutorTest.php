<?php

namespace Tests\Feature\Admin;

use App\Mail\CuentaActivada;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

class AutorTest extends TestCase
{
    use CreaCuentas;

    public function test_lista_autores_con_su_estatus(): void
    {
        $pendiente = $this->crearAutor(activo: false, datos: ['nombre' => 'Pendencio']);
        $activo = $this->crearAutor(datos: ['nombre' => 'Activia']);

        $this->actingAs($this->administrador())
            ->get('/admin/autores')
            ->assertOk()
            ->assertSee('Pendencio')
            ->assertSee('Activia')
            ->assertSee('Pendiente')
            ->assertSee('Activa');
    }

    public function test_filtra_por_estatus_pendiente(): void
    {
        $this->crearAutor(activo: false, datos: ['nombre' => 'Pendencio']);
        $this->crearAutor(datos: ['nombre' => 'Activia']);

        $this->actingAs($this->administrador())
            ->get('/admin/autores?estatus=pendiente')
            ->assertOk()
            ->assertSee('Pendencio')
            ->assertDontSee('Activia');
    }

    public function test_el_administrador_no_aparece_en_la_lista(): void
    {
        $this->actingAs($this->administrador())
            ->get('/admin/autores')
            ->assertDontSee('admin@emprendeunam.invalid');
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
