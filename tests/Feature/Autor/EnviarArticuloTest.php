<?php

namespace Tests\Feature\Autor;

use App\Enums\EstatusArticulo;
use App\Mail\ArticuloRecibido;
use App\Models\Articulo;
use App\Models\Palabra;
use App\Models\PalabraArticulo;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreaArticulos;
use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

class EnviarArticuloTest extends TestCase
{
    use CreaArticulos, CreaCuentas;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    private function enviar(array $cambios = [], ?UploadedFile $pdf = null)
    {
        return $this->post('/autor/articulos', array_merge($this->datosArticulo($cambios), [
            'pdf' => $pdf ?? $this->pdfDePrueba(),
            'carta_cesion' => $this->pdfDePrueba('carta-cesion-firmada.pdf'),
        ], $cambios));
    }

    public function test_muestra_el_formulario_con_los_temas(): void
    {
        $this->actingAs($this->crearAutor())
            ->get('/autor/articulos/nuevo')
            ->assertOk()
            ->assertSee('formulario-articulo')
            ->assertSee('Emprendimiento social');
    }

    public function test_el_autor_descarga_la_plantilla_de_la_carta_de_cesion(): void
    {
        $this->actingAs($this->crearAutor())
            ->get('/autor/articulos/nuevo')
            ->assertSee('autor\\/carta-cesion', false);

        $respuesta = $this->get('/autor/carta-cesion')->assertOk();
        $this->assertStringContainsString('attachment', $respuesta->headers->get('Content-Disposition'));
        $this->assertStringContainsString('wordprocessingml', $respuesta->headers->get('Content-Type'));
    }

    public function test_la_plantilla_no_lleva_datos_de_personas_ni_combinacion_de_correspondencia(): void
    {
        $zip = new \ZipArchive;
        $this->assertTrue($zip->open(resource_path('plantillas/carta_cesion_derechos.docx')) === true);
        $contenido = '';
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $contenido .= $zip->getFromIndex($i);
        }
        $zip->close();

        $this->assertStringNotContainsString('mailMerge', $contenido);
        $this->assertStringNotContainsString('recipientData', $contenido);
        $this->assertStringContainsString('cesión', $contenido);
    }

    public function test_un_invitado_no_descarga_la_plantilla(): void
    {
        $this->get('/autor/carta-cesion')->assertRedirect('/iniciar-sesion');
    }

    public function test_envia_un_articulo_con_5_palabras_clave(): void
    {
        $autor = $this->crearAutor();
        Mail::fake();

        $this->actingAs($autor)->enviar()
            ->assertRedirect('/autor')
            ->assertSessionHas('exito');

        $articulo = Articulo::with(['palabras', 'autores'])->latest('arti_id_autor')->firstOrFail();

        $this->assertSame(EstatusArticulo::EN_REVISION, $articulo->arti_status);
        $this->assertStringStartsWith('Cómo las pequeñas empresas', $articulo->arti_resumen);
        $this->assertSame(today()->toDateString(), $articulo->arti_fecha_registro->toDateString());
        $this->assertSame("articulos/{$articulo->arti_id_autor}.pdf", $articulo->arti_url_pdf);
        Storage::disk('local')->assertExists($articulo->arti_url_pdf);

        $this->assertCount(1, $articulo->autores);
        $this->assertTrue($articulo->autores->first()->pivot->auar_principal);
        $this->assertSame($autor->persona->autor->auto_id_autor, $articulo->autores->first()->auto_id_autor);

        // Normalizadas: minúsculas y sin espacios sobrantes.
        $this->assertEqualsCanonicalizing(
            ['innovación', 'pymes', 'emprendimiento social', 'méxico', 'redes'],
            $articulo->palabras->pluck('pala_descripcion')->all(),
        );

        Mail::assertQueued(ArticuloRecibido::class, fn ($m) => $m->hasTo($autor->correo()));
    }

    public function test_reutiliza_palabras_existentes_sin_importar_mayusculas_ni_espacios(): void
    {
        Palabra::create(['pala_descripcion' => 'pymes']);
        $antes = Palabra::count();

        $this->actingAs($this->crearAutor())->enviar([
            'palabras' => ['  PyMEs ', 'uno', 'dos   tres', 'cuatro', 'cinco'],
        ])->assertRedirect('/autor');

        $this->assertSame($antes + 4, Palabra::count());
        $this->assertDatabaseHas('palabra', ['pala_descripcion' => 'dos tres']);
    }

    public function test_rechaza_4_palabras_clave(): void
    {
        $this->actingAs($this->crearAutor())
            ->enviar(['palabras' => ['a', 'b', 'c', 'd']])
            ->assertSessionHasErrors(['palabras' => 'Escribe exactamente 5 palabras clave.']);

        $this->assertDatabaseMissing('articulo', ['arti_titulo' => 'Innovación social en las pymes mexicanas']);
    }

    public function test_rechaza_6_palabras_clave(): void
    {
        $this->actingAs($this->crearAutor())
            ->enviar(['palabras' => ['a', 'b', 'c', 'd', 'e', 'f']])
            ->assertSessionHasErrors('palabras');

        $this->assertDatabaseMissing('articulo', ['arti_titulo' => 'Innovación social en las pymes mexicanas']);
        Storage::disk('local')->assertDirectoryEmpty('articulos');
    }

    public function test_rechaza_palabras_repetidas_tras_normalizar(): void
    {
        $this->actingAs($this->crearAutor())
            ->enviar(['palabras' => ['Pymes', ' pymes ', 'c', 'd', 'e']])
            ->assertSessionHasErrors(['palabras' => 'Las 5 palabras clave deben ser distintas.']);
    }

    public function test_el_trigger_de_la_base_rechaza_una_sexta_palabra(): void
    {
        $articulo = $this->enviarArticulo($this->crearAutor());
        $extra = Palabra::create(['pala_descripcion' => 'palabra sexta']);

        try {
            PalabraArticulo::create(['palabra_id_palabra' => $extra->palabra_id_palabra, 'arti_id_autor' => $articulo->arti_id_autor]);
            $this->fail('El trigger tr_max_palabras_articulo debió rechazar la sexta palabra.');
        } catch (QueryException $e) {
            $this->assertSame('23514', $e->getCode());
        }
    }

    public function test_rechaza_un_archivo_que_no_es_pdf(): void
    {
        $falso = UploadedFile::fake()->createWithContent('articulo.pdf', 'esto es texto, no un PDF');

        $this->actingAs($this->crearAutor())
            ->enviar(pdf: $falso)
            ->assertSessionHasErrors('pdf');
    }

    public function test_rechaza_un_pdf_mayor_a_7_mb(): void
    {
        $this->actingAs($this->crearAutor())
            ->enviar(pdf: $this->pdfDePrueba(kb: 7200))
            ->assertSessionHasErrors(['pdf' => 'El archivo no debe pesar más de 7 MB.']);
    }

    /** .docx mínimo (un ZIP con word/document.xml); con $macros agrega vbaProject.bin como un .docm. */
    private function wordDePrueba(bool $macros = false): UploadedFile
    {
        $ruta = tempnam(sys_get_temp_dir(), 'docx');
        $zip = new \ZipArchive;
        $zip->open($ruta, \ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', '<Types/>');
        $zip->addFromString('word/document.xml', '<w:document><w:body><w:p><w:r><w:t>Texto</w:t></w:r></w:p></w:body></w:document>');
        if ($macros) {
            $zip->addFromString('word/vbaProject.bin', 'macro');
        }
        $zip->close();

        return new UploadedFile($ruta, 'articulo.docx', null, null, true);
    }

    public function test_acepta_el_articulo_en_word_y_lo_entrega_como_docx(): void
    {
        $autor = $this->crearAutor();
        $this->actingAs($autor)->enviar(pdf: $this->wordDePrueba())->assertRedirect('/autor');

        $articulo = Articulo::latest('arti_id_autor')->firstOrFail();
        $this->assertStringEndsWith('.docx', $articulo->arti_url_pdf);
        Storage::disk('local')->assertExists($articulo->arti_url_pdf);

        $respuesta = $this->get("/autor/articulos/{$articulo->arti_id_autor}/pdf")->assertOk();
        $this->assertStringContainsString('wordprocessingml', $respuesta->headers->get('Content-Type'));
        $this->assertStringContainsString('.docx', $respuesta->headers->get('Content-Disposition'));
    }

    public function test_rechaza_un_word_con_macros(): void
    {
        $this->actingAs($this->crearAutor())
            ->enviar(pdf: $this->wordDePrueba(macros: true))
            ->assertSessionHasErrors('pdf');
    }

    public function test_el_resumen_es_obligatorio_y_de_maximo_50_palabras(): void
    {
        $autor = $this->crearAutor();

        $this->actingAs($autor)->enviar(['resumen' => ''])->assertSessionHasErrors('resumen');
        $this->actingAs($autor)->enviar(['resumen' => trim(str_repeat('palabra ', 51))])
            ->assertSessionHasErrors(['resumen' => 'El resumen no debe tener más de 50 palabras.']);
        $this->actingAs($autor)->enviar(['resumen' => str_repeat('a', 501)])->assertSessionHasErrors('resumen');

        $this->actingAs($autor)->enviar(['resumen' => trim(str_repeat('palabra ', 50))])->assertRedirect('/autor');
    }

    public function test_admite_como_maximo_un_coautor(): void
    {
        $this->actingAs($this->crearAutor())->enviar([
            'tiene_coautores' => '1',
            'coautores' => [
                ['nombre' => 'Carla', 'primer_apellido' => 'Núñez', 'correo' => 'carla2@example.com', 'institucion' => 'IPN'],
                ['nombre' => 'Raúl', 'primer_apellido' => 'Soto', 'correo' => 'raul2@example.com', 'institucion' => 'UAM'],
            ],
        ])->assertSessionHasErrors(['coautores' => 'Solo se admite un coautor por artículo (hasta dos personas autoras).']);
    }

    public function test_registra_un_coautor_nuevo_sin_cuenta_de_usuario(): void
    {
        $usuarios = DB::table('usuario')->count();

        $this->actingAs($this->crearAutor())->enviar([
            'tiene_coautores' => '1',
            'coautores' => [
                ['nombre' => 'Carla', 'primer_apellido' => 'Núñez', 'segundo_apellido' => '', 'correo' => 'Carla@Example.com', 'institucion' => 'IPN'],
            ],
        ])->assertRedirect('/autor');

        $articulo = Articulo::with('autores.persona.correo')->latest('arti_id_autor')->firstOrFail();
        $coautores = $articulo->autores->where('pivot.auar_principal', false);

        $this->assertCount(1, $coautores);
        $carla = $coautores->first(fn ($a) => $a->persona->pers_nombre === 'Carla');
        $this->assertSame('carla@example.com', $carla->persona->correo->comu_valor_comunicacion);
        $this->assertSame('Coautor', $carla->persona->correo->comu_etiqueta);
        $this->assertSame('IPN', $carla->auto_institucion);
        $this->assertNull($carla->auto_id_coautor);
        $this->assertSame($usuarios + 1, DB::table('usuario')->count()); // solo el autor principal
    }

    public function test_un_coautor_con_correo_existente_reutiliza_su_persona_y_autor(): void
    {
        $colega = $this->crearAutor(datos: ['correo' => 'colega@example.com']);
        $personas = DB::table('persona')->count();

        $this->actingAs($this->crearAutor())->enviar([
            'tiene_coautores' => '1',
            'coautores' => [['nombre' => 'Otro', 'primer_apellido' => 'Nombre', 'correo' => 'COLEGA@example.com', 'institucion' => 'X']],
        ])->assertRedirect('/autor');

        $articulo = Articulo::with('autores')->latest('arti_id_autor')->firstOrFail();
        $this->assertTrue($articulo->autores->contains('auto_id_autor', $colega->persona->autor->auto_id_autor));
        $this->assertSame($personas + 1, DB::table('persona')->count()); // +1 del autor principal
    }

    public function test_con_la_casilla_marcada_exige_al_menos_un_coautor(): void
    {
        $this->actingAs($this->crearAutor())
            ->enviar(['tiene_coautores' => '1', 'coautores' => []])
            ->assertSessionHasErrors('coautores');
    }

    public function test_sin_la_casilla_se_ignoran_los_coautores(): void
    {
        $this->actingAs($this->crearAutor())->enviar([
            'tiene_coautores' => '0',
            'coautores' => [['nombre' => '', 'correo' => 'basura']],
        ])->assertRedirect('/autor');

        $this->assertCount(1, Articulo::with('autores')->latest('arti_id_autor')->first()->autores);
    }

    public function test_no_puede_ser_su_propio_coautor(): void
    {
        $autor = $this->crearAutor();

        $this->actingAs($autor)->enviar([
            'tiene_coautores' => '1',
            'coautores' => [['nombre' => 'Yo', 'primer_apellido' => 'Mismo', 'correo' => $autor->correo(), 'institucion' => 'X']],
        ])->assertSessionHasErrors('coautores.0.correo');
    }

    public function test_no_existen_rutas_para_editar_ni_reenviar(): void
    {
        $autor = $this->crearAutor();
        $articulo = $this->enviarArticulo($autor);

        $this->actingAs($autor);
        $this->put("/autor/articulos/{$articulo->arti_id_autor}", [])->assertNotFound();
        $this->patch("/autor/articulos/{$articulo->arti_id_autor}", [])->assertNotFound();
        $this->get("/autor/articulos/{$articulo->arti_id_autor}/editar")->assertNotFound();
        $this->post("/autor/articulos/{$articulo->arti_id_autor}/reenviar")->assertNotFound();

        $nombres = collect(Route::getRoutes()->getRoutesByName())->keys();
        $this->assertEmpty($nombres->filter(fn ($n) => preg_match('/^(autor|admin)\.articulos\.(edit|update|reenviar)/', $n)));
    }

    public function test_si_falla_la_transaccion_se_borra_el_pdf(): void
    {
        $autor = $this->crearAutor();

        // Una palabra de más de 100 caracteres hace fallar el INSERT después de guardar el PDF.
        try {
            $this->enviarArticulo($autor, ['palabras' => ['a', 'b', 'c', 'd', str_repeat('x', 101)]]);
            $this->fail('La transacción debió fallar.');
        } catch (QueryException) {
            // esperado
        }

        Storage::disk('local')->assertDirectoryEmpty('articulos');
        $this->assertDatabaseMissing('articulo', ['arti_titulo' => 'Innovación social en las pymes mexicanas']);
    }
}
