<?php

namespace Tests\Feature\Admin;

use App\Mail\AvisoNuevaEdicion;
use App\Models\Persona;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\CreaContenido;
use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

class AvisoNuevaEdicionTest extends TestCase
{
    use CreaContenido, CreaCuentas;

    public function test_encola_un_aviso_por_suscriptor_activo_con_su_enlace_de_baja(): void
    {
        $activo = $this->crearSuscriptor('activo', ['correo' => 'activo.aviso@example.com']);
        $this->crearSuscriptor('sin_confirmar', ['correo' => 'pendiente.aviso@example.com']);
        $this->crearSuscriptor('baja', ['correo' => 'baja.aviso@example.com']);
        $totalActivos = Persona::suscriptoresActivos()->count();
        Mail::fake();

        $this->actingAs($this->administrador())
            ->post('/admin/newsletter/avisar', [
                'titulo' => 'Edición 02 · Noviembre 2026',
                'url' => 'https://emprendedores.fca.unam.mx/newsletter/02',
                'confirmo' => '1',
            ])
            ->assertRedirect('/admin/newsletter')
            ->assertSessionHas('exito');

        Mail::assertQueued(AvisoNuevaEdicion::class, $totalActivos);
        Mail::assertQueued(AvisoNuevaEdicion::class, fn ($m) => $m->hasTo('activo.aviso@example.com')
            && $m->enlace === 'https://emprendedores.fca.unam.mx/newsletter/02'
            && str_ends_with($m->enlaceBaja, '/newsletter/baja/'.$activo->pers_token_confirmacion));
        Mail::assertNotQueued(AvisoNuevaEdicion::class, fn ($m) => $m->hasTo('pendiente.aviso@example.com') || $m->hasTo('baja.aviso@example.com'));
    }

    public function test_valida_titulo_liga_https_y_confirmacion(): void
    {
        $this->actingAs($this->administrador())
            ->post('/admin/newsletter/avisar', ['titulo' => '', 'url' => 'http://inseguro.example.com'])
            ->assertSessionHasErrors(['titulo', 'url', 'confirmo']);
    }

    public function test_un_autor_no_puede_avisar(): void
    {
        $this->actingAs($this->crearAutor())
            ->post('/admin/newsletter/avisar', ['titulo' => 'X', 'url' => 'https://x.example.com', 'confirmo' => '1'])
            ->assertForbidden();
    }
}
