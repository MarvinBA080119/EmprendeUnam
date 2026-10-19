<?php

namespace Tests\Feature\Publico;

use App\Models\Tema;
use App\Services\ArticuloService;
use App\Services\BuscadorService;
use App\Services\ConstanciaService;
use App\Services\CursoService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreaArticulos;
use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

class BuscadorTest extends TestCase
{
    use CreaArticulos, CreaCuentas;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Mail::fake();
    }

    private function aprobado(array $datos = [])
    {
        $articulo = $this->enviarArticulo($this->crearAutor($datos['autor'] ?? true, $datos['datosAutor'] ?? []), $datos['articulo'] ?? []);
        Mail::fake();
        app(ConstanciaService::class)->aprobar($articulo, $this->administrador());

        return $articulo;
    }

    /** Títulos devueltos (texto plano, uniendo los segmentos). */
    private function titulos(string $consulta): array
    {
        return collect($this->getJson('/buscar/resultados?'.$consulta)->assertOk()->json('datos'))
            ->map(fn ($r) => collect($r['titulo'])->pluck('texto')->join(''))
            ->all();
    }

    public function test_solo_muestra_articulos_aprobados(): void
    {
        $this->aprobado(['articulo' => ['titulo' => 'Zafiro aprobado']]);
        $this->enviarArticulo($this->crearAutor(), ['titulo' => 'Zafiro en revisión']);
        $rechazado = $this->enviarArticulo($this->crearAutor(), ['titulo' => 'Zafiro rechazado']);
        app(ArticuloService::class)->rechazar($rechazado, 'Motivo de rechazo suficientemente largo.', $this->administrador());

        $this->assertSame(['Zafiro aprobado'], $this->titulos('q=zafiro'));
    }

    public function test_es_insensible_a_mayusculas_y_acentos(): void
    {
        $this->aprobado(['articulo' => ['titulo' => 'Administración estratégica para pymes']]);

        $this->assertSame(['Administración estratégica para pymes'], $this->titulos('q=ADMINISTRACION%20estrategica'));
        $this->assertSame(['Administración estratégica para pymes'], $this->titulos('q=administ'));   // prefijo
    }

    public function test_busca_en_palabras_clave_autores_coautores_y_tema(): void
    {
        $this->aprobado(['articulo' => ['titulo' => 'Con palabra', 'palabras' => ['blockchain', 'b', 'c', 'd', 'e']]]);
        $this->aprobado(['datosAutor' => ['nombre' => 'Eulalia', 'primer_apellido' => 'Quiñones'], 'articulo' => ['titulo' => 'Con autora']]);
        $this->aprobado(['articulo' => [
            'titulo' => 'Con coautor',
            'tiene_coautores' => true,
            'coautores' => [['nombre' => 'Teodoro', 'primer_apellido' => 'Zubiría', 'correo' => 'teo.busca@example.com', 'institucion' => 'X']],
        ]]);
        $this->aprobado(['articulo' => ['titulo' => 'Con tema', 'tema_id_tema' => Tema::where('tema_nombre', 'Negocios internacionales')->value('tema_id_tema')]]);

        $this->assertSame(['Con palabra'], $this->titulos('q=blockchain'));
        $this->assertSame(['Con autora'], $this->titulos('q=quinones'));
        $this->assertSame(['Con coautor'], $this->titulos('q=zubiria'));
        $this->assertContains('Con tema', $this->titulos('q=internacionales'));
    }

    public function test_resalta_el_termino_encontrado(): void
    {
        $this->aprobado(['articulo' => ['titulo' => 'Innovación abierta']]);

        $titulo = $this->getJson('/buscar/resultados?q=innovacion')->json('datos.0.titulo');

        $this->assertSame([['texto' => 'Innovación', 'marca' => true], ['texto' => ' abierta', 'marca' => false]], $titulo);
    }

    public function test_cursos_vigentes_y_filtros_de_tipo_tema_y_anio(): void
    {
        $articulo = $this->aprobado(['articulo' => ['titulo' => 'Finanzas aprobadas', 'palabras' => ['finanzas', 'b', 'c', 'd', 'e']]]);
        $cursos = app(CursoService::class);
        $cursos->crear(['nombre' => 'Finanzas para emprendedores', 'institucion' => 'FCA', 'fecha' => today()->addMonth()->toDateString(), 'url' => 'https://fca.unam.mx/f', 'palabras' => ['finanzas']]);
        $cursos->crear(['nombre' => 'Finanzas del año pasado', 'institucion' => 'FCA', 'fecha' => today()->subYear()->toDateString(), 'url' => 'https://fca.unam.mx/p', 'palabras' => ['finanzas']]);

        $this->assertEqualsCanonicalizing(['Finanzas aprobadas', 'Finanzas para emprendedores'], $this->titulos('q=finanzas'));
        $this->assertSame(['Finanzas para emprendedores'], $this->titulos('q=finanzas&tipo=curso'));
        $this->assertSame(['Finanzas aprobadas'], $this->titulos('q=finanzas&tipo=articulo'));
        $this->assertSame(['Finanzas aprobadas'], $this->titulos("q=finanzas&tema={$articulo->tema_id_tema}"));   // el tema excluye cursos
        $this->assertSame([], $this->titulos('q=finanzas&anio=1999'));
    }

    public function test_pagina_de_10_en_10(): void
    {
        for ($i = 1; $i <= 11; $i++) {
            $this->aprobado(['articulo' => ['titulo' => "Quetzal {$i}"]]);
        }

        $this->getJson('/buscar/resultados?q=quetzal&page=2')
            ->assertJsonPath('meta.total', 11)
            ->assertJsonCount(1, 'datos');
    }

    public function test_una_busqueda_solo_con_palabras_vacias_no_falla(): void
    {
        $this->getJson('/buscar/resultados?q=de%20la')->assertOk()->assertJsonPath('meta.total', 0);
        $this->getJson('/buscar/resultados?q=%27%3B%20drop%20table%20articulo%3B--')->assertOk();
        $this->assertTrue(DB::getSchemaBuilder()->hasTable('articulo'));
    }

    public function test_valida_los_filtros(): void
    {
        $this->getJson('/buscar/resultados?tipo=revista&anio=abc&tema=999999')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['tipo', 'anio', 'tema']);
    }

    public function test_terminos_normalizados(): void
    {
        $this->assertSame(['gestion', 'pymes'], app(BuscadorService::class)->terminos('  Gestión, PYMES!! '));
    }
}
