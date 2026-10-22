<?php

namespace Tests\Feature\Autor;

use App\Enums\TipoDocumento;
use App\Models\Articulo;
use App\Models\ArticuloRevista;
use App\Models\Constancia;
use App\Models\Revista;
use App\Services\ConstanciaService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreaArticulos;
use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

/** Carta de cesión firmada (al enviar) y carta de agradecimiento (al salir en un número). */
class DocumentosTest extends TestCase
{
    use CreaArticulos, CreaCuentas;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Mail::fake();
    }

    private function aprobado(): array
    {
        $autor = $this->crearAutor(datos: ['nombre' => 'Mariana', 'primer_apellido' => 'Fuentes']);
        $articulo = $this->enviarArticulo($autor);
        app(ConstanciaService::class)->aprobar($articulo, $this->administrador());

        return [$autor, $articulo->fresh()];
    }

    private function enNumero(Articulo $articulo, bool $visible = true): Revista
    {
        $revista = new Revista;
        $revista->forceFill([
            'usua_id_usuario' => $this->administrador()->usua_id_usuario,
            'revi_titulo' => 'Emprendedores 220',
            'revi_anio' => '2026',
            'revi_mes' => 'julio-agosto',
            'revi_num_edicion' => '220',
            'revi_url_portada' => 'ediciones/220/portada.jpg',
            'revi_url_revista_pdf' => 'ediciones/220/revista.pdf',
            'revi_fecha_publicacion' => today(),
            'revi_visible' => $visible,
        ])->save();

        ArticuloRevista::create([
            'revi_id_revista' => $revista->revi_id_revista,
            'arti_id_autor' => $articulo->arti_id_autor,
            'arre_titulo' => $articulo->arti_titulo,
            'arre_autores' => 'Mariana Fuentes',
        ]);

        return $revista;
    }

    public function test_el_envio_exige_la_carta_de_cesion_firmada(): void
    {
        $this->actingAs($this->crearAutor())
            ->post('/autor/articulos', $this->datosArticulo(['pdf' => $this->pdfDePrueba()]))
            ->assertSessionHasErrors(['carta_cesion' => 'Sube la carta de cesión de derechos firmada, en PDF.']);

        $this->post('/autor/articulos', $this->datosArticulo([
            'pdf' => $this->pdfDePrueba(),
            'carta_cesion' => UploadedFile::fake()->createWithContent('carta.pdf', 'no soy un pdf'),
        ]))->assertSessionHasErrors('carta_cesion');
    }

    public function test_la_carta_de_cesion_se_guarda_sin_folio_en_la_misma_transaccion(): void
    {
        $articulo = $this->enviarArticulo($this->crearAutor());
        DB::statement('SET CONSTRAINTS ALL IMMEDIATE');

        $carta = $articulo->cartaCesion;
        $this->assertSame(TipoDocumento::CESION, $carta->cons_tipo);
        $this->assertNull($carta->cons_folio);
        Storage::disk('local')->assertExists($carta->cons_url_archivo);
        $this->assertNull($articulo->constancia, 'La carta de cesión no cuenta como constancia de aprobación.');
    }

    public function test_el_administrador_descarga_la_carta_de_cesion(): void
    {
        $articulo = $this->enviarArticulo($this->crearAutor());

        $this->actingAs($this->administrador())
            ->get("/admin/articulos/{$articulo->arti_id_autor}")
            ->assertSee('Carta de cesión');
        $this->get("/admin/articulos/{$articulo->arti_id_autor}/carta-cesion")->assertOk()->assertDownload();
    }

    public function test_la_carta_de_agradecimiento_espera_a_que_el_articulo_salga_en_un_numero(): void
    {
        [$autor, $articulo] = $this->aprobado();

        $this->actingAs($autor)->get('/autor')->assertDontSee('Carta de agradecimiento');
        $this->get("/autor/articulos/{$articulo->arti_id_autor}/carta-agradecimiento")->assertSessionHasErrors('articulo');

        $this->enNumero($articulo, visible: false);
        $this->get("/autor/articulos/{$articulo->arti_id_autor}/carta-agradecimiento")->assertSessionHasErrors('articulo');
        $this->assertNull($articulo->fresh()->cartaAgradecimiento);
    }

    public function test_con_el_articulo_en_un_numero_visible_el_autor_descarga_su_carta(): void
    {
        [$autor, $articulo] = $this->aprobado();
        $this->enNumero($articulo);

        $this->actingAs($autor)->get('/autor')->assertSee('Carta de agradecimiento');
        $this->get("/autor/articulos/{$articulo->arti_id_autor}/carta-agradecimiento")->assertOk()->assertDownload();

        $carta = $articulo->fresh()->cartaAgradecimiento;
        $this->assertSame(TipoDocumento::AGRADECIMIENTO, $carta->cons_tipo);
        $this->assertSame('AGR-'.now()->year.'-0001', $carta->cons_folio);

        // La segunda descarga entrega la misma carta.
        $this->get("/autor/articulos/{$articulo->arti_id_autor}/carta-agradecimiento")->assertOk();
        $this->assertSame(1, Constancia::where('arti_id_autor', $articulo->arti_id_autor)->deTipo(TipoDocumento::AGRADECIMIENTO)->count());

        // Se verifica en línea con su folio.
        $this->get("/constancias/verificar/{$carta->cons_folio}")->assertSee('Carta de agradecimiento válida');
    }

    public function test_otro_autor_no_descarga_la_carta_ajena(): void
    {
        [, $articulo] = $this->aprobado();
        $this->enNumero($articulo);

        $this->actingAs($this->crearAutor())
            ->get("/autor/articulos/{$articulo->arti_id_autor}/carta-agradecimiento")
            ->assertForbidden();
    }

    public function test_el_folio_de_agradecimiento_no_altera_el_de_las_constancias(): void
    {
        [, $articulo] = $this->aprobado();
        $this->enNumero($articulo);
        app(ConstanciaService::class)->agradecimiento($articulo);

        $this->assertSame('EMP-'.now()->year.'-0002', app(ConstanciaService::class)->siguienteFolio(now()->year));
    }
}
