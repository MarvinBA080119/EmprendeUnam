<?php

namespace Tests\Feature\Publico;

use Tests\TestCase;

class InicioTest extends TestCase
{
    public function test_el_inicio_muestra_la_edicion_actual_del_sitio(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Diferenciación para generar ventajas competitivas')
            ->assertSee('Gresmex: una empresa que cambiará el mundo')
            ->assertSee('Alydia: humanización de los servicios de salud')
            // Recursos con ruta absoluta (no relativa a la página).
            ->assertSee(asset('img/portadas/222_horizontal-1_page-0001.jpg'), false)
            ->assertSee(asset('pdf/gresmex-una-empresa-que-cambiara-el-mundo.pdf'), false)
            // Búsqueda, newsletter y convocatoria conectados al sistema.
            ->assertSee('action="'.route('buscador').'"', false)
            ->assertSee(route('newsletter'), false)
            ->assertSee(route('envio-articulos'), false);
    }
}
