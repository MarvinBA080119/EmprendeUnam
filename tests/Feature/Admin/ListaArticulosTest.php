<?php

namespace Tests\Feature\Admin;

use App\Models\Tema;
use App\Services\ArticuloService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreaArticulos;
use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

class ListaArticulosTest extends TestCase
{
    use CreaArticulos, CreaCuentas;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    private function titulos(string $consulta): array
    {
        return collect($this->actingAs($this->administrador())
            ->getJson('/admin/articulos/datos?'.$consulta)
            ->assertOk()
            ->json('datos'))->pluck('titulo')->all();
    }

    public function test_filtra_por_estatus_y_tema(): void
    {
        $autor = $this->crearAutor();
        $comunicacion = Tema::where('tema_nombre', 'Comunicación')->value('tema_id_tema');
        $a = $this->enviarArticulo($autor, ['titulo' => 'Alfa zzz', 'tema_id_tema' => $comunicacion]);
        $b = $this->enviarArticulo($autor, ['titulo' => 'Beta zzz']);
        app(ArticuloService::class)->rechazar($b, 'Motivo de prueba suficientemente largo.', $this->administrador());

        $this->assertSame(['Alfa zzz'], $this->titulos('q=zzz&estatus=EN_REVISION'));
        $this->assertSame(['Beta zzz'], $this->titulos('q=zzz&estatus=RECHAZADO'));
        $this->assertSame(['Alfa zzz'], $this->titulos("q=zzz&tema={$comunicacion}"));
    }

    public function test_filtra_por_rango_de_fechas(): void
    {
        $autor = $this->crearAutor();
        $viejo = $this->enviarArticulo($autor, ['titulo' => 'Viejo zzz']);
        $this->enviarArticulo($autor, ['titulo' => 'Nuevo zzz']);
        DB::table('articulo')->where('arti_id_autor', $viejo->arti_id_autor)->update(['arti_fecha_registro' => '2026-01-10']);

        $this->assertSame(['Viejo zzz'], $this->titulos('q=zzz&desde=2026-01-01&hasta=2026-01-31'));
        $this->assertSame(['Nuevo zzz'], $this->titulos('q=zzz&desde='.today()->toDateString()));
    }

    public function test_busca_por_titulo_sin_acentos_ni_mayusculas(): void
    {
        $this->enviarArticulo($this->crearAutor(), ['titulo' => 'Administración estratégica xyz']);

        $this->assertSame(['Administración estratégica xyz'], $this->titulos('q=ADMINISTRACION%20ESTRATEGICA'));
    }

    public function test_busca_por_autor_coautor_y_palabra_clave(): void
    {
        $this->enviarArticulo($this->crearAutor(datos: ['nombre' => 'Eulalia', 'primer_apellido' => 'Quiñones']), [
            'titulo' => 'Por autor',
        ]);
        $this->enviarArticulo($this->crearAutor(), [
            'titulo' => 'Por coautor',
            'tiene_coautores' => true,
            'coautores' => [['nombre' => 'Teodoro', 'primer_apellido' => 'Zubiría', 'correo' => 'teo@example.com', 'institucion' => 'X']],
        ]);
        $this->enviarArticulo($this->crearAutor(), [
            'titulo' => 'Por palabra',
            'palabras' => ['neuromarketing', 'b', 'c', 'd', 'e'],
        ]);

        $this->assertSame(['Por autor'], $this->titulos('q=quinones'));
        $this->assertSame(['Por coautor'], $this->titulos('q=zubiria'));
        $this->assertSame(['Por palabra'], $this->titulos('q=NEUROMARKETING'));
    }

    public function test_valida_los_filtros(): void
    {
        $this->actingAs($this->administrador())
            ->getJson('/admin/articulos/datos?estatus=PUBLICADO&desde=2026-05-01&hasta=2026-01-01')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['estatus', 'hasta']);
    }

    public function test_pagina_de_10_en_10(): void
    {
        $autor = $this->crearAutor();
        for ($i = 1; $i <= 11; $i++) {
            $this->enviarArticulo($autor, ['titulo' => "Paginado qqq {$i}"]);
        }

        $respuesta = $this->actingAs($this->administrador())->getJson('/admin/articulos/datos?q=qqq&page=2');

        $respuesta->assertJsonPath('meta.total', 11)
            ->assertJsonPath('meta.ultima', 2)
            ->assertJsonCount(1, 'datos');
    }
}
