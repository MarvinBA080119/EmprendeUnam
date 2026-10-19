<?php

namespace Tests\Feature\Admin;

use App\Models\Curso;
use App\Models\Palabra;
use App\Models\PalabraCurso;
use App\Services\CursoService;
use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

class CursoTest extends TestCase
{
    use CreaCuentas;

    /** @return array<string, mixed> */
    private function datos(array $cambios = []): array
    {
        return array_merge([
            'nombre' => 'Finanzas para emprendedores',
            'institucion' => 'Facultad de Contaduría y Administración',
            'fecha' => today()->addMonth()->toDateString(),
            'url' => 'https://educacioncontinua.fca.unam.mx/finanzas',
            'palabras' => ['Finanzas', ' flujo de  efectivo ', '', '', ''],
        ], $cambios);
    }

    public function test_crea_un_curso_con_sus_palabras_clave(): void
    {
        $this->actingAs($this->administrador())
            ->post('/admin/cursos', $this->datos())
            ->assertRedirect('/admin/cursos')
            ->assertSessionHas('exito');

        $curso = Curso::with('palabras')->where('curs_nombre_curso', 'Finanzas para emprendedores')->firstOrFail();
        $this->assertSame(today()->addMonth()->toDateString(), $curso->curs_fecha->toDateString());
        $this->assertEqualsCanonicalizing(['finanzas', 'flujo de efectivo'], $curso->palabras->pluck('pala_descripcion')->all());
    }

    public function test_valida_campos_y_enlace_https(): void
    {
        $this->actingAs($this->administrador())
            ->post('/admin/cursos', $this->datos([
                'nombre' => str_repeat('a', 101),
                'institucion' => '',
                'fecha' => '',
                'url' => 'http://inseguro.example.com',
                'palabras' => ['', '', '', '', ''],
            ]))
            ->assertSessionHasErrors(['nombre', 'institucion', 'fecha', 'url', 'palabras']);
    }

    public function test_rechaza_mas_de_5_palabras_o_repetidas(): void
    {
        $this->actingAs($this->administrador())
            ->post('/admin/cursos', $this->datos(['palabras' => ['a', 'b', 'c', 'd', 'e', 'f']]))
            ->assertSessionHasErrors(['palabras' => 'Escribe como máximo 5 palabras clave.']);

        $this->actingAs($this->administrador())
            ->post('/admin/cursos', $this->datos(['palabras' => ['Ventas', 'ventas ']]))
            ->assertSessionHasErrors(['palabras' => 'Las palabras clave no deben repetirse.']);
    }

    public function test_edita_un_curso_y_reemplaza_sus_palabras(): void
    {
        $this->actingAs($this->administrador())->post('/admin/cursos', $this->datos());
        $curso = Curso::where('curs_nombre_curso', 'Finanzas para emprendedores')->firstOrFail();

        $this->actingAs($this->administrador())
            ->put("/admin/cursos/{$curso->curs_id_curso}", $this->datos([
                'nombre' => 'Finanzas avanzadas',
                'palabras' => ['inversión', 'riesgo', '', '', ''],
            ]))
            ->assertRedirect('/admin/cursos');

        $curso->refresh()->load('palabras');
        $this->assertSame('Finanzas avanzadas', $curso->curs_nombre_curso);
        $this->assertEqualsCanonicalizing(['inversión', 'riesgo'], $curso->palabras->pluck('pala_descripcion')->all());
    }

    public function test_elimina_el_curso_y_sus_palabra_curso(): void
    {
        $this->actingAs($this->administrador())->post('/admin/cursos', $this->datos());
        $curso = Curso::where('curs_nombre_curso', 'Finanzas para emprendedores')->firstOrFail();

        $this->actingAs($this->administrador())
            ->delete("/admin/cursos/{$curso->curs_id_curso}")
            ->assertRedirect('/admin/cursos');

        $this->assertDatabaseMissing('curso', ['curs_id_curso' => $curso->curs_id_curso]);
        $this->assertSame(0, PalabraCurso::where('curs_id_curso', $curso->curs_id_curso)->count());
        $this->assertTrue(Palabra::whereRaw('lower(pala_descripcion) = ?', ['finanzas'])->exists());
    }

    public function test_la_lista_distingue_cursos_vigentes_y_pasados(): void
    {
        $servicio = app(CursoService::class);
        $servicio->crear($this->datos(['nombre' => 'Curso futuro', 'palabras' => ['futuro']]));
        $servicio->crear($this->datos(['nombre' => 'Curso pasado', 'palabras' => ['pasado'], 'fecha' => today()->subDay()->toDateString()]));

        $this->actingAs($this->administrador())->get('/admin/cursos?vigencia=vigentes')
            ->assertSee('Curso futuro')->assertDontSee('Curso pasado');
        $this->actingAs($this->administrador())->get('/admin/cursos?vigencia=pasados')
            ->assertSee('Curso pasado')->assertDontSee('Curso futuro');
    }

    public function test_un_autor_no_gestiona_cursos(): void
    {
        $this->actingAs($this->crearAutor())->post('/admin/cursos', $this->datos())->assertForbidden();
        $this->assertDatabaseMissing('curso', ['curs_nombre_curso' => 'Finanzas para emprendedores']);
    }
}
