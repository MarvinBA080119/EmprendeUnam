<?php

namespace Tests\Feature\Autor;

use App\Services\ArticuloService;
use App\Services\ConstanciaService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreaArticulos;
use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

class PanelTest extends TestCase
{
    use CreaArticulos, CreaCuentas;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function test_el_historial_muestra_solo_sus_articulos(): void
    {
        $yo = $this->crearAutor();
        $otro = $this->crearAutor();
        $this->enviarArticulo($yo, ['titulo' => 'Mi artículo propio']);
        $this->enviarArticulo($otro, ['titulo' => 'Artículo de otra persona']);

        $this->actingAs($yo)->get('/autor')
            ->assertOk()
            ->assertSee('Mi artículo propio')
            ->assertSee('En revisión')
            ->assertDontSee('Artículo de otra persona')
            ->assertSee('Enviar nuevo artículo');
    }

    public function test_un_autor_no_descarga_el_pdf_de_otro(): void
    {
        $yo = $this->crearAutor();
        $ajeno = $this->enviarArticulo($this->crearAutor());
        $propio = $this->enviarArticulo($yo);

        $this->actingAs($yo)->get("/autor/articulos/{$propio->arti_id_autor}/pdf")
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($yo)->get("/autor/articulos/{$ajeno->arti_id_autor}/pdf")->assertForbidden();
    }

    public function test_un_coautor_registrado_no_ve_el_articulo_en_su_historial(): void
    {
        $coautor = $this->crearAutor(datos: ['correo' => 'coautora@example.com']);
        $articulo = $this->enviarArticulo($this->crearAutor(), [
            'titulo' => 'Escrito a cuatro manos',
            'tiene_coautores' => true,
            'coautores' => [['nombre' => 'X', 'primer_apellido' => 'Y', 'correo' => 'coautora@example.com', 'institucion' => 'Z']],
        ]);

        $this->actingAs($coautor)->get('/autor')->assertDontSee('Escrito a cuatro manos');
        $this->actingAs($coautor)->get("/autor/articulos/{$articulo->arti_id_autor}/pdf")->assertForbidden();
    }

    public function test_muestra_el_motivo_de_un_articulo_rechazado(): void
    {
        $yo = $this->crearAutor();
        $articulo = $this->enviarArticulo($yo);
        app(ArticuloService::class)->rechazar($articulo, 'El artículo no sigue las normas editoriales.', $this->administrador());

        $this->actingAs($yo)->get('/autor')
            ->assertSee('Rechazado')
            ->assertSee('El artículo no sigue las normas editoriales.');
    }

    public function test_aprobado_muestra_agradecimiento_y_descarga_su_constancia(): void
    {
        $yo = $this->crearAutor();
        $articulo = $this->enviarArticulo($yo, ['titulo' => 'Artículo aprobado de prueba']);
        Mail::fake();
        $constancia = app(ConstanciaService::class)->aprobar($articulo, $this->administrador());

        $this->actingAs($yo)->get('/autor')->assertSee('Descargar constancia');

        $this->actingAs($yo)->get("/autor/articulos/{$articulo->arti_id_autor}/aprobado")
            ->assertOk()
            ->assertSee('¡Gracias por tu contribución!')
            ->assertSee($constancia->cons_folio);

        $this->actingAs($yo)->get("/autor/articulos/{$articulo->arti_id_autor}/constancia")
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertDownload("constancia-{$constancia->cons_folio}.pdf");
    }

    public function test_un_autor_no_descarga_la_constancia_de_otro(): void
    {
        $articulo = $this->enviarArticulo($this->crearAutor());
        Mail::fake();
        app(ConstanciaService::class)->aprobar($articulo, $this->administrador());

        $this->actingAs($this->crearAutor())
            ->get("/autor/articulos/{$articulo->arti_id_autor}/constancia")
            ->assertForbidden();
    }

    public function test_la_pantalla_de_agradecimiento_solo_existe_para_aprobados(): void
    {
        $yo = $this->crearAutor();
        $articulo = $this->enviarArticulo($yo);

        $this->actingAs($yo)->get("/autor/articulos/{$articulo->arti_id_autor}/aprobado")->assertNotFound();
    }
}
