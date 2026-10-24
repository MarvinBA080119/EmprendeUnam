<?php

namespace Tests\Feature\Admin;

use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

class AccesoTest extends TestCase
{
    use CreaCuentas;

    public function test_un_visitante_sin_sesion_va_al_login(): void
    {
        $this->get('/admin')->assertRedirect('/iniciar-sesion');
        $this->get('/autor')->assertRedirect('/iniciar-sesion');
    }

    public function test_un_autor_no_puede_entrar_a_admin(): void
    {
        $autor = $this->crearAutor();

        // Un panel ajeno lleva al propio con un aviso (no a un 403 sin salida).
        $this->actingAs($autor)->get('/admin')->assertRedirect('/autor')->assertSessionHas('aviso');
        $this->actingAs($autor)->get('/admin/autores')->assertRedirect('/autor');
    }

    public function test_un_autor_entra_a_su_panel(): void
    {
        $this->actingAs($this->crearAutor())->get('/autor')->assertOk()->assertSee('Mis artículos');
    }

    public function test_el_administrador_entra_a_admin(): void
    {
        $this->actingAs($this->administrador())->get('/admin')->assertOk()->assertSee('Panel del administrador');
    }

    public function test_los_privilegios_guardados_en_sesion_son_del_usuario_que_los_cargo(): void
    {
        $autor = $this->crearAutor();
        $admin = $this->administrador();

        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($autor)->get('/admin')->assertRedirect('/autor');
    }
}
