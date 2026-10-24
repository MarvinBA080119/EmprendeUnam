<?php

namespace Tests\Feature\Admin;

use App\Models\Revista;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

/** Generador de flipbooks: PDF → hojas numeradas → visor publicado en /flipbook/{numero}. */
class FlipbookTest extends TestCase
{
    use CreaCuentas;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        if (! is_executable(trim((string) shell_exec('command -v pdftoppm')))) {
            $this->markTestSkipped('pdftoppm (poppler-utils) no está instalado.');
        }
    }

    /** PDF real de $paginas páginas, hecho con dompdf. */
    private function pdf(int $paginas = 3): UploadedFile
    {
        $html = collect(range(1, $paginas))
            ->map(fn ($n) => "<h1 style=\"font-size:60px\">Página {$n}</h1>")
            ->join('<div style="page-break-after: always"></div>');
        $ruta = tempnam(sys_get_temp_dir(), 'flip');
        file_put_contents($ruta, Pdf::loadHTML($html)->setPaper('letter')->output());

        return new UploadedFile($ruta, 'revista.pdf', null, null, true);
    }

    private function revista(string $numero, array $cambios = []): Revista
    {
        return Revista::create(array_merge([
            'usua_id_usuario' => $this->administrador()->usua_id_usuario,
            'revi_titulo' => 'Número de prueba', 'revi_num_edicion' => $numero, 'revi_anio' => '2026',
            'revi_mes' => 'septiembre-octubre', 'revi_fecha_publicacion' => '2026-09-01',
            'revi_url_portada' => 'ediciones/portada.jpg', 'revi_url_revista_pdf' => 'ediciones/numero.pdf',
            'revi_visible' => true,
        ], $cambios));
    }

    public function test_convierte_cada_pagina_en_una_hoja_numerada_y_arma_el_visor(): void
    {
        $revista = $this->revista('230');

        $this->actingAs($this->administrador())
            ->postJson('/admin/flipbooks', ['numero' => '230', 'pdf' => $this->pdf(3)])
            ->assertStatus(202)
            ->assertJson(['estado' => 'listo', 'total' => 3, 'url_flipbook' => '/flipbook/230']);

        $disco = Storage::disk('local');
        foreach ([1, 2, 3] as $n) {
            $disco->assertExists(["flipbooks/230/img/{$n}.jpg", "flipbooks/230/img/hd/{$n}.jpg", "flipbooks/230/img/thumbs/{$n}.jpg"]);
        }
        $disco->assertMissing('flipbooks/230/img/4.jpg');
        $disco->assertExists(['flipbooks/230/js/page-flip.browser.min.js', 'flipbooks/230/js/reader.js', 'flipbooks/230/NOTICES.txt']);
        $disco->assertMissing('flipbooks/.trabajo/230');

        $html = $disco->get('flipbooks/230/index.html');
        $this->assertStringContainsString('<base href="/flipbook/230/">', $html);
        $this->assertStringContainsString('img/3.jpg', $html);
        $this->assertStringContainsString('total: 3', $html);

        // El número del gestor sin eBook queda ligado solo.
        $this->assertSame('/flipbook/230', $revista->fresh()->revi_url_flipbook);
    }

    public function test_el_visor_es_publico_solo_si_un_numero_visible_lo_usa(): void
    {
        $this->revista('231', ['revi_url_flipbook' => 'https://otro.example/flip']);
        $this->actingAs($this->administrador())->postJson('/admin/flipbooks', ['numero' => '231', 'pdf' => $this->pdf(2)])->assertStatus(202);

        // El administrador lo revisa aunque nadie lo use.
        $this->get('/flipbook/231')->assertOk()->assertHeader('Content-Type', 'text/html; charset=UTF-8');
        $this->get('/flipbook/231/img/1.jpg')->assertOk()->assertHeader('Content-Type', 'image/jpeg');

        auth()->logout();
        $this->get('/flipbook/231')->assertNotFound();

        Revista::where('revi_num_edicion', '231')->update(['revi_url_flipbook' => '/flipbook/231']);
        $this->get('/flipbook/231/')->assertOk();
        $this->get('/flipbook/231/css/prestige.css')->assertOk();
        $this->get('/flipbook/231/../../.env')->assertNotFound();
        $this->get('/flipbook/231/manifest.json')->assertNotFound();
    }

    public function test_el_avance_se_consulta_y_la_lista_lo_muestra(): void
    {
        $this->actingAs($this->administrador())->postJson('/admin/flipbooks', ['numero' => '232', 'pdf' => $this->pdf(2)]);

        $this->getJson('/admin/flipbooks/232/estado')->assertOk()->assertJson(['estado' => 'listo', 'actual' => 2, 'total' => 2]);
        $this->get('/admin/flipbooks/232/miniatura/1')->assertOk();
        $this->get('/admin/flipbooks')->assertOk()->assertSee('No. <span class="emp-num">232</span>', false)->assertSee('Sin usar');
        $this->getJson('/admin/flipbooks/999/estado')->assertNotFound();
    }

    public function test_rechaza_lo_que_no_es_pdf_y_numeros_invalidos(): void
    {
        $falso = tempnam(sys_get_temp_dir(), 'flip');
        file_put_contents($falso, 'no soy pdf');
        $this->actingAs($this->administrador());

        $this->postJson('/admin/flipbooks', ['numero' => '233', 'pdf' => new UploadedFile($falso, 'r.pdf', null, null, true)])
            ->assertStatus(422);
        $this->postJson('/admin/flipbooks', ['numero' => 'abc', 'pdf' => $this->pdf(1)])
            ->assertStatus(422)->assertJsonValidationErrors('numero');
        Storage::disk('local')->assertMissing('flipbooks/233');
    }

    public function test_el_numero_acepta_un_flipbook_generado_y_al_eliminarlo_queda_sin_ebook(): void
    {
        $revista = $this->revista('234', ['revi_url_flipbook' => 'https://otro.example/flip']);
        $this->actingAs($this->administrador())->postJson('/admin/flipbooks', ['numero' => '234', 'pdf' => $this->pdf(1)]);

        $this->post('/admin/flipbooks/234/usar', ['revista' => $revista->revi_id_revista])->assertRedirect();
        $this->assertSame('/flipbook/234', $revista->fresh()->revi_url_flipbook);

        // El formulario del número lo ofrece como opción.
        $this->get("/admin/ediciones/{$revista->revi_id_revista}/editar")->assertSee('Flipbook generado · No. 234');

        $this->delete('/admin/flipbooks/234')->assertRedirect('/admin/flipbooks');
        $this->assertNull($revista->fresh()->revi_url_flipbook);
        Storage::disk('local')->assertMissing('flipbooks/234');
    }

    public function test_un_autor_no_entra_al_generador(): void
    {
        $this->actingAs($this->crearAutor())->get('/admin/flipbooks')->assertRedirect('/autor');
        $this->actingAs($this->crearAutor())->post('/admin/flipbooks', [])->assertForbidden();
    }
}
