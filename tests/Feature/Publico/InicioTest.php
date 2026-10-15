<?php

namespace Tests\Feature\Publico;

use Tests\TestCase;

class InicioTest extends TestCase
{
    public function test_la_pagina_de_inicio_lista_los_13_temas(): void
    {
        $respuesta = $this->get('/');

        $respuesta->assertOk()
            ->assertSee('Temas de la revista')
            ->assertSee('Habilidades directivas')
            ->assertSee('Toma de decisiones y resolución de problemas')
            ->assertSee('Emprendimiento social');

        $this->assertCount(13, $respuesta->viewData('temas'));
    }
}
