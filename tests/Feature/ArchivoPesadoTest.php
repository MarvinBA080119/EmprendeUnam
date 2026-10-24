<?php

namespace Tests\Feature;

use Illuminate\Testing\TestResponse;
use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

/** Envío más pesado que post_max_size: regresa a la misma vista con un aviso, no a una página de error. */
class ArchivoPesadoTest extends TestCase
{
    use CreaCuentas;

    private function enviarPesado(string $url, array $encabezados = []): TestResponse
    {
        return $this->call('POST', $url, [], [], [], array_merge([
            'CONTENT_LENGTH' => (string) (1024 * 1024 * 1024),
        ], $encabezados));
    }

    public function test_regresa_a_la_pagina_anterior_con_el_aviso(): void
    {
        $this->actingAs($this->administrador());

        $this->enviarPesado('/admin/ediciones', ['HTTP_REFERER' => 'http://localhost/admin/ediciones/nuevo'])
            ->assertRedirect('http://localhost/admin/ediciones/nuevo?aviso=archivo-pesado');

        $this->get('/admin/ediciones/nuevo?aviso=archivo-pesado')
            ->assertOk()
            ->assertSee('El archivo es demasiado pesado');
    }

    public function test_no_regresa_a_otro_sitio(): void
    {
        $this->enviarPesado('/admin/ediciones', ['HTTP_REFERER' => 'https://otro-sitio.example/robar'])
            ->assertRedirect(route('inicio').'?aviso=archivo-pesado');
    }

    public function test_la_subida_por_javascript_recibe_json(): void
    {
        $this->enviarPesado('/diseno/articulos/1/diseno', ['HTTP_X_SUBIDA_MESA' => '1'])
            ->assertStatus(413)
            ->assertJsonStructure(['mensaje']);
    }
}
