<?php

namespace Tests\Feature\Publico;

use App\Services\RegistroAutorService;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/** Páginas de contenido convertidas del sitio original. */
class SitioTest extends TestCase
{
    public static function paginas(): array
    {
        return [
            'acerca' => ['/acerca-de', 'Dra. Clotilde Hernández Garnica'],
            'historia' => ['/historia', 'Nuestra historia'],
            'histórico' => ['/historico', 'Histórico'],
            'temáticas' => ['/tematicas', 'Habilidades directivas'],
            'podcasts' => ['/podcasts', 'Emprende y cuida tu salud mental'],
            'webinar' => ['/webinar', 'Webinars'],
            '10 de 10' => ['/10-de-10', '10 de 10'],
            '10 de 10 · área' => ['/10-de-10/negocios-internacionales', 'Metamorfosis de la Pyme internacional'],
            'pymes' => ['/las-pyme-hablan', 'Las Pyme Hablan'],
            'lineamientos' => ['/lineamientos', 'Citas y referencias (Chicago 18ª ed.)'],
            'envío de artículos' => ['/envio-de-articulos', 'Registrarme como autor'],
            'cursos' => ['/cursos', 'Cursos'],
            'buscador' => ['/buscar', 'id="buscador"'],
            'suscripción' => ['/suscripcion', 'Únete a nuestra comunidad'],
            'newsletter' => ['/newsletter', 'Recibe cada edición'],
            'consultorio' => ['/consultorio', 'Financiamiento para el crecimiento empresarial de manera segura'],
        ];
    }

    #[DataProvider('paginas')]
    public function test_la_pagina_responde(string $ruta, string $texto): void
    {
        $this->get($ruta)->assertOk()->assertSee($texto, false);
    }

    public function test_tematicas_cambia_de_categoria_por_querystring(): void
    {
        $this->get('/tematicas?cat=comunicacion')
            ->assertOk()
            ->assertSee('<span class="tem-indice__resumen-val">Comunicación</span>', false);

        // Una categoría inexistente cae en la primera.
        $this->get('/tematicas?cat=no-existe')
            ->assertSee('<span class="tem-indice__resumen-val">Habilidades directivas</span>', false);
    }

    public function test_un_area_inexistente_de_10_de_10_vuelve_al_indice(): void
    {
        $this->get('/10-de-10/no-existe')->assertRedirect('/10-de-10');
    }

    public function test_las_direcciones_del_sitio_anterior_redirigen_permanentemente(): void
    {
        $this->get('/consultorio.php')->assertRedirect('/consultorio')->assertStatus(301);
        $this->get('/tematicas.php?cat=comunicacion')->assertRedirect('/tematicas?cat=comunicacion');
        $this->get('/diezdediez-detalle.php?tema=informatica')->assertRedirect('/10-de-10/informatica');
        $this->get('/newsletter/index.html')->assertRedirect('/newsletter');
    }

    public function test_el_envio_de_articulos_lleva_al_panel_si_hay_sesion(): void
    {
        $autor = app(RegistroAutorService::class)->registrar([
            'nombre' => 'Ana', 'primer_apellido' => 'Paz', 'correo' => 'ana.sitio@example.com',
            'clave' => 'clave-de-prueba', 'institucion' => 'FCA',
        ]);
        $autor->forceFill(['usua_activo' => true])->save();

        $this->actingAs($autor)->get('/envio-de-articulos')
            ->assertSee(route('autor.articulos.create'), false)
            ->assertDontSee('Registrarme como autor');
    }
}
