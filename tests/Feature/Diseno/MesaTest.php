<?php

namespace Tests\Feature\Diseno;

use App\Mail\DisenoListo;
use App\Mail\DisenoPendiente;
use App\Models\Articulo;
use App\Models\ArticuloRevista;
use App\Models\Comunicacion;
use App\Models\Revista;
use App\Models\Usuario;
use App\Services\ConstanciaService;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreaArticulos;
use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

/** Mesa de diseño: cuenta Diseño, textos originales, PDF diseñado y su uso en el número. */
class MesaTest extends TestCase
{
    use CreaArticulos, CreaCuentas;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    private function aprobado(string $titulo = 'Finanzas sanas en la microempresa'): Articulo
    {
        $articulo = $this->enviarArticulo($this->crearAutor(), ['titulo' => $titulo]);
        app(ConstanciaService::class)->aprobar($articulo, $this->administrador());

        return $articulo->fresh();
    }

    /** Archivo real: su tipo se detecta por el contenido, como en producción. */
    private function archivo(string $nombre, string $contenido): UploadedFile
    {
        $ruta = tempnam(sys_get_temp_dir(), 'dis');
        file_put_contents($ruta, $contenido);

        return new UploadedFile($ruta, $nombre, null, null, true);
    }

    private function pdfDisenado(string $nombre = 'final.pdf'): UploadedFile
    {
        return $this->archivo($nombre, "%PDF-1.7\n1 0 obj << /Type /Catalog >> endobj\n%%EOF\n");
    }

    /** El correo del administrador en datos_iniciales es de relleno (.invalid): se cambia solo en la prueba. */
    private function correoRealDelAdministrador(): void
    {
        Comunicacion::where('pers_id_persona', $this->administrador()->pers_id_persona)
            ->update(['comu_valor_comunicacion' => 'direccion.editorial@example.com']);
    }

    public function test_el_comando_crea_la_cuenta_y_entra_a_su_panel(): void
    {
        $this->artisan('emprende:crear-diseno')
            ->expectsQuestion('Correo de la cuenta Diseño', 'diseno@fca.example.mx')
            ->expectsQuestion('Contraseña (mínimo 12 caracteres)', self::CLAVE_PRUEBA)
            ->expectsQuestion('Escríbela de nuevo', self::CLAVE_PRUEBA)
            ->assertSuccessful();

        DB::statement('SET CONSTRAINTS ALL IMMEDIATE');
        $cuenta = Usuario::where('usua_nombre_usuario', 'Diseño')->firstOrFail();
        $this->assertSame('diseno@fca.example.mx', $cuenta->correo());
        $this->assertTrue($cuenta->usua_activo);

        // Sin distinguir mayúsculas, como el administrador.
        $this->post('/iniciar-sesion', ['acceso' => 'diseño', 'clave' => self::CLAVE_PRUEBA])->assertRedirect('/diseno');
        $this->get('/diseno')->assertOk()->assertSee('Mesa de diseño');
    }

    public function test_el_comando_cambia_correo_o_contrasena_de_la_cuenta_existente(): void
    {
        $cuenta = $this->cuentaDiseno();
        $nueva = 'otra-clave-de-prueba-2026';

        $this->artisan('emprende:crear-diseno')
            ->expectsQuestion('Correo de la cuenta Diseño (Enter para conservar diseno.prueba@example.com)', '')
            ->expectsQuestion('Nueva contraseña (mínimo 12 caracteres; Enter para conservar la actual)', $nueva)
            ->expectsQuestion('Escríbela de nuevo', $nueva)
            ->assertSuccessful();

        $this->assertSame('diseno.prueba@example.com', $cuenta->fresh()->correo());
        $this->assertTrue(password_verify($nueva, $cuenta->fresh()->usua_clave_acceso));
        $this->assertSame(1, Usuario::where('usua_nombre_usuario', 'Diseño')->count());
    }

    public function test_el_comando_rechaza_contrasena_corta_y_correo_ajeno(): void
    {
        $autor = $this->crearAutor();

        $this->artisan('emprende:crear-diseno')
            ->expectsQuestion('Correo de la cuenta Diseño', $autor->correo())
            ->assertFailed();

        $this->artisan('emprende:crear-diseno')
            ->expectsQuestion('Correo de la cuenta Diseño', 'diseno@fca.example.mx')
            ->expectsQuestion('Contraseña (mínimo 12 caracteres)', 'corta')
            ->assertFailed();

        $this->assertFalse(Usuario::where('usua_nombre_usuario', 'Diseño')->exists());
    }

    public function test_al_aprobar_un_articulo_se_avisa_a_la_cuenta_de_diseno(): void
    {
        $this->cuentaDiseno('diseno.aviso@example.com');
        $articulo = $this->enviarArticulo($this->crearAutor(), ['titulo' => 'Logística inversa']);
        Mail::fake();

        app(ConstanciaService::class)->aprobar($articulo, $this->administrador());

        Mail::assertQueued(DisenoPendiente::class, fn ($m) => $m->hasTo('diseno.aviso@example.com') && $m->titulo === 'Logística inversa');
    }

    public function test_la_mesa_solo_muestra_aprobados_y_empieza_por_los_pendientes(): void
    {
        $aprobado = $this->aprobado('Artículo aprobado de prueba');
        $this->enviarArticulo($this->crearAutor(), ['titulo' => 'Artículo todavía en revisión']);

        $this->actingAs($this->cuentaDiseno())
            ->get('/diseno')
            ->assertOk()
            ->assertSee('Artículo aprobado de prueba')
            ->assertDontSee('Artículo todavía en revisión')
            ->assertSee('Tienes 1 artículo por diseñar')
            ->assertSee('0 de 1')
            ->assertSee(route('diseno.articulos.original', $aprobado), false);
    }

    public function test_descarga_el_original_solo_de_un_aprobado(): void
    {
        $aprobado = $this->aprobado();
        $enRevision = $this->enviarArticulo($this->crearAutor());
        $this->actingAs($this->cuentaDiseno());

        $this->get("/diseno/articulos/{$aprobado->arti_id_autor}/original")
            ->assertOk()
            ->assertDownload("{$aprobado->arti_id_autor}-finanzas-sanas-en-la-microempresa.pdf");
        $this->get("/diseno/articulos/{$enRevision->arti_id_autor}/original")->assertNotFound();
    }

    public function test_sube_el_pdf_disenado_y_avisa_al_administrador(): void
    {
        $articulo = $this->aprobado();
        $diseno = $this->cuentaDiseno();
        $this->correoRealDelAdministrador();
        Mail::fake();

        $this->actingAs($diseno)
            ->withHeader('X-Subida-Mesa', '1')
            ->post("/diseno/articulos/{$articulo->arti_id_autor}/diseno", ['pdf' => $this->pdfDisenado(), 'ver' => 'pendientes'])
            ->assertOk()
            ->assertJson(['url' => route('diseno.panel', ['ver' => 'pendientes'])])
            ->assertSessionHas('recien', $articulo->arti_id_autor);

        $articulo->refresh();
        Storage::disk('local')->assertExists($articulo->arti_url_pdf_diseno);
        $this->assertSame($diseno->usua_id_usuario, $articulo->arti_id_disenador);
        $this->assertNotNull($articulo->arti_fecha_diseno);
        Mail::assertQueued(DisenoListo::class, fn ($m) => $m->hasTo('direccion.editorial@example.com') && ! $m->reemplazo);

        $this->get('/diseno?ver=disenados')->assertSee('Diseñado el');
    }

    public function test_reemplazar_borra_el_pdf_anterior(): void
    {
        $articulo = $this->aprobado();
        $this->actingAs($this->cuentaDiseno());

        $this->post("/diseno/articulos/{$articulo->arti_id_autor}/diseno", ['pdf' => $this->pdfDisenado()])->assertRedirect();
        $anterior = $articulo->fresh()->arti_url_pdf_diseno;
        $this->post("/diseno/articulos/{$articulo->arti_id_autor}/diseno", ['pdf' => $this->pdfDisenado('v2.pdf')])
            ->assertRedirect()
            ->assertSessionHas('exito', fn ($m) => str_starts_with($m, 'Se reemplazó'));

        Storage::disk('local')->assertMissing($anterior);
        Storage::disk('local')->assertExists($articulo->fresh()->arti_url_pdf_diseno);
    }

    public function test_rechaza_lo_que_no_es_pdf(): void
    {
        $articulo = $this->aprobado();
        $this->actingAs($this->cuentaDiseno());

        // Por la subida con avance: JSON con el motivo.
        $this->withHeader('X-Subida-Mesa', '1')
            ->post("/diseno/articulos/{$articulo->arti_id_autor}/diseno", ['pdf' => $this->archivo('final.pdf', 'no soy un pdf')])
            ->assertStatus(422)
            ->assertJsonStructure(['mensaje']);

        // Sin JavaScript: regresa a la lista con el aviso.
        $this->withoutHeader('X-Subida-Mesa')
            ->post("/diseno/articulos/{$articulo->arti_id_autor}/diseno", ['pdf' => $this->archivo('final.pdf', 'no soy un pdf')])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertNull($articulo->fresh()->arti_url_pdf_diseno);
    }

    public function test_la_base_no_acepta_diseno_en_un_articulo_no_aprobado(): void
    {
        $articulo = $this->enviarArticulo($this->crearAutor());
        $diseno = $this->cuentaDiseno();

        $this->expectException(QueryException::class);
        $articulo->forceFill([
            'arti_url_pdf_diseno' => 'disenos/x.pdf',
            'arti_fecha_diseno' => now(),
            'arti_id_disenador' => $diseno->usua_id_usuario,
        ])->save();
    }

    public function test_cada_cuenta_queda_en_su_panel(): void
    {
        $diseno = $this->cuentaDiseno();
        $articulo = $this->aprobado();

        $this->actingAs($diseno)->get('/admin')->assertRedirect('/diseno')->assertSessionHas('aviso');
        $this->actingAs($diseno)->get('/autor')->assertRedirect('/diseno');
        $this->actingAs($this->administrador())->get('/diseno')->assertRedirect('/admin');
        $this->actingAs($this->crearAutor())->get('/diseno')->assertRedirect('/autor');
        $this->actingAs($this->crearAutor())
            ->post("/diseno/articulos/{$articulo->arti_id_autor}/diseno", ['pdf' => $this->pdfDisenado()])
            ->assertForbidden();
    }

    public function test_el_numero_publica_el_pdf_disenado_al_agregar_el_articulo(): void
    {
        $articulo = $this->aprobado();
        $this->actingAs($this->cuentaDiseno())
            ->post("/diseno/articulos/{$articulo->arti_id_autor}/diseno", ['pdf' => $this->pdfDisenado()]);
        $rutaDiseno = $articulo->fresh()->arti_url_pdf_diseno;

        $revista = Revista::create([
            'usua_id_usuario' => $this->administrador()->usua_id_usuario,
            'revi_titulo' => 'Número de prueba', 'revi_num_edicion' => '221', 'revi_anio' => '2026',
            'revi_mes' => 'septiembre-octubre', 'revi_fecha_publicacion' => '2026-09-01',
            'revi_url_portada' => 'ediciones/portada.jpg', 'revi_url_revista_pdf' => 'ediciones/numero.pdf',
            'revi_visible' => true,
        ]);
        $entrada = ArticuloRevista::create([
            'revi_id_revista' => $revista->revi_id_revista,
            'arti_id_autor' => $articulo->arti_id_autor,
            'arre_titulo' => $articulo->arti_titulo,
            'arre_autores' => 'Ana López Ruiz',
        ]);

        $this->assertSame($rutaDiseno, $entrada->rutaPdf());
        $this->assertSame('diseno', $entrada->origenPdf());
        $this->get(route('edicion.articulo.pdf', $entrada))->assertOk();

        $this->actingAs($this->administrador())
            ->get("/admin/ediciones/{$revista->revi_id_revista}")
            ->assertSee('PDF diseñado');
    }

    public function test_el_administrador_ve_el_avance_y_descarga_los_disenados_en_zip(): void
    {
        $listo = $this->aprobado('Artículo ya diseñado');
        $this->aprobado('Artículo sin diseño');
        $this->actingAs($this->cuentaDiseno())
            ->post("/diseno/articulos/{$listo->arti_id_autor}/diseno", ['pdf' => $this->pdfDisenado()]);

        $this->actingAs($this->administrador())
            ->get('/admin/ediciones/manuscritos')
            ->assertOk()
            ->assertSee('1 de 2')
            ->assertSee('Artículo sin diseño')
            ->assertSee(route('admin.ediciones.manuscritos.diseno', $listo), false);

        $respuesta = $this->get('/admin/ediciones/manuscritos/disenos')->assertOk();
        $zip = new \ZipArchive;
        $zip->open($respuesta->baseResponse->getFile()->getPathname());
        $this->assertSame(1, $zip->numFiles);
        $this->assertSame("{$listo->arti_id_autor}-articulo-ya-disenado-diseno.pdf", $zip->getNameIndex(0));
        $zip->close();

        $respuesta = $this->get('/admin/ediciones/manuscritos/originales')->assertOk();
        $zip->open($respuesta->baseResponse->getFile()->getPathname());
        $this->assertSame(2, $zip->numFiles);
        $zip->close();

        $this->get("/admin/ediciones/manuscritos/{$listo->arti_id_autor}/diseno")->assertOk()->assertDownload();
    }

    public function test_no_se_avisa_a_correos_de_relleno(): void
    {
        $articulo = $this->aprobado();
        Mail::fake();

        // El administrador de datos_iniciales tiene un correo .invalid: no se le escribe.
        $this->actingAs($this->cuentaDiseno())
            ->post("/diseno/articulos/{$articulo->arti_id_autor}/diseno", ['pdf' => $this->pdfDisenado()]);

        Mail::assertNotQueued(DisenoListo::class);
    }
}
