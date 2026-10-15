<?php

namespace Tests\Feature\Publico;

use App\Services\CursoService;
use Tests\TestCase;

class CursosTest extends TestCase
{
    public function test_solo_muestra_cursos_con_fecha_de_hoy_en_adelante(): void
    {
        $cursos = app(CursoService::class);
        $cursos->crear(['nombre' => 'Curso de hoy', 'institucion' => 'FCA', 'fecha' => today()->toDateString(), 'url' => 'https://fca.unam.mx/hoy', 'palabras' => ['hoy']]);
        $cursos->crear(['nombre' => 'Curso de ayer', 'institucion' => 'FCA', 'fecha' => today()->subDay()->toDateString(), 'url' => 'https://fca.unam.mx/ayer', 'palabras' => ['ayer']]);

        $this->get('/cursos')
            ->assertOk()
            ->assertSee('Curso de hoy')
            ->assertSee('https://fca.unam.mx/hoy', false)
            ->assertDontSee('Curso de ayer');
    }
}
