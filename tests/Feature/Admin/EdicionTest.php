<?php

namespace Tests\Feature\Admin;

use App\Models\ArticuloRevista;
use App\Models\Revista;
use App\Services\ConstanciaService;
use App\Services\CursoService;
use App\Services\EdicionService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreaArticulos;
use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

/** Gestor de portada: números, artículos, los 5 del índice y sus cursos. */
class EdicionTest extends TestCase
{
    use CreaArticulos, CreaCuentas;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Mail::fake();
    }

    /** @return array<string, mixed> */
    private function datosNumero(array $cambios = []): array
    {
        return array_merge([
            'titulo' => 'Diferenciación para generar ventajas',
            'numero' => '220',
            'anio' => '2026',
            'periodo' => 'julio-agosto',
            'fecha_publicacion' => '2026-07-01',
            'flipbook' => 'https://flipbook.ejemplo.mx/220',
            'visible' => '1',
            'portada' => UploadedFile::fake()->image('portada.jpg', 700, 910),
            'pdf' => $this->pdfDePrueba('numero.pdf'),
        ], $cambios);
    }

    /** Archivo real (no simulado): su tipo se detecta por el contenido, como en producción. */
    private function archivoReal(string $nombre, string $contenido): UploadedFile
    {
        $ruta = tempnam(sys_get_temp_dir(), 'emp');
        file_put_contents($ruta, $contenido);

        return new UploadedFile($ruta, $nombre, null, null, true);
    }

    private function crearNumero(array $cambios = []): Revista
    {
        $this->actingAs($this->administrador())->post('/admin/ediciones', $this->datosNumero($cambios))->assertRedirect();

        return Revista::latest('revi_id_revista')->firstOrFail();
    }

    private function agregar(Revista $revista, int $cuantos): void
    {
        for ($i = 1; $i <= $cuantos; $i++) {
            $this->post("/admin/ediciones/{$revista->revi_id_revista}/articulos", [
                'titulo' => "Artículo {$i}",
                'autores' => "Autora {$i}, Autor {$i}",
                'seccion' => 'TALENTO HUMANO',
                'paginas' => "{$i}-".($i + 4),
            ])->assertRedirect();
        }
    }

    /** @return array<int, int> arre_id => posición, para los primeros $ids */
    private function posiciones(Revista $revista, array $titulos): array
    {
        $ids = $revista->articulos()->pluck('arre_id_articulo_revista', 'arre_titulo');

        return collect($titulos)->mapWithKeys(fn ($t, $i) => [$ids[$t] => $i + 1])->all();
    }

    public function test_un_autor_no_entra_al_gestor(): void
    {
        $this->actingAs($this->crearAutor())->get('/admin/ediciones')->assertForbidden();
    }

    public function test_registra_un_numero_con_portada_pdf_y_flipbook(): void
    {
        $revista = $this->crearNumero();

        $this->assertSame('220', $revista->revi_num_edicion);
        $this->assertSame('julio-agosto', $revista->revi_mes);
        $this->assertSame('https://flipbook.ejemplo.mx/220', $revista->revi_url_flipbook);
        Storage::disk('local')->assertExists([$revista->revi_url_portada, $revista->revi_url_revista_pdf]);
        $this->assertStringEndsWith('.jpg', $revista->revi_url_portada);
        $this->assertStringNotContainsString('portada', basename($revista->revi_url_portada), 'El nombre del archivo es aleatorio.');
    }

    public function test_la_portada_debe_ser_una_imagen_real(): void
    {
        $this->actingAs($this->administrador())
            ->post('/admin/ediciones', $this->datosNumero(['portada' => $this->archivoReal('portada.jpg', '<?php echo 1;')]))
            ->assertSessionHasErrors('portada');
        $this->assertSame(0, Revista::count());
    }

    public function test_solo_un_numero_visible_en_portada_a_la_vez(): void
    {
        $primero = $this->crearNumero();
        $segundo = $this->crearNumero(['numero' => '221', 'periodo' => 'septiembre-octubre']);
        $oculto = $this->crearNumero(['numero' => '222', 'visible' => '0']);

        $this->post("/admin/ediciones/{$primero->revi_id_revista}/portada")->assertRedirect();
        $this->post("/admin/ediciones/{$segundo->revi_id_revista}/portada")->assertRedirect();
        $this->post("/admin/ediciones/{$oculto->revi_id_revista}/portada")->assertSessionHasErrors('revista');

        $this->assertFalse($primero->fresh()->revi_en_portada);
        $this->assertTrue($segundo->fresh()->revi_en_portada);
        $this->assertSame(1, Revista::where('revi_en_portada', true)->count());
    }

    public function test_un_articulo_del_sistema_debe_estar_aprobado_y_toma_sus_datos(): void
    {
        $revista = $this->crearNumero();
        $enRevision = $this->enviarArticulo($this->crearAutor());
        $aprobado = $this->enviarArticulo($this->crearAutor(datos: ['nombre' => 'Mariana', 'primer_apellido' => 'Fuentes']));
        app(ConstanciaService::class)->aprobar($aprobado, $this->administrador());

        $this->actingAs($this->administrador())
            ->post("/admin/ediciones/{$revista->revi_id_revista}/articulos", ['arti_id_autor' => $enRevision->arti_id_autor])
            ->assertSessionHasErrors('arti_id_autor');

        $this->post("/admin/ediciones/{$revista->revi_id_revista}/articulos", ['arti_id_autor' => $aprobado->arti_id_autor])
            ->assertRedirect();

        $entrada = $revista->articulos()->firstOrFail();
        $this->assertSame($aprobado->arti_titulo, $entrada->arre_titulo);
        $this->assertStringContainsString('Mariana Fuentes', $entrada->arre_autores);
    }

    public function test_el_indice_exige_cinco_articulos_en_posiciones_distintas(): void
    {
        $revista = $this->crearNumero();
        $this->agregar($revista, 7);
        $url = "/admin/ediciones/{$revista->revi_id_revista}/indice";

        $this->post($url, ['posiciones' => $this->posiciones($revista, ['Artículo 1', 'Artículo 2', 'Artículo 3'])])
            ->assertSessionHasErrors(['posiciones' => 'Elige exactamente 5 artículos para el índice (llevas 3).']);

        $repetida = $this->posiciones($revista, ['Artículo 1', 'Artículo 2', 'Artículo 3', 'Artículo 4', 'Artículo 5']);
        $repetida[array_key_last($repetida)] = 1;
        $this->post($url, ['posiciones' => $repetida])->assertSessionHasErrors('posiciones');

        $this->post($url, ['posiciones' => $this->posiciones($revista, ['Artículo 1', 'Artículo 2', 'Artículo 3', 'Artículo 4', 'Artículo 5'])])
            ->assertSessionHasNoErrors();

        $this->assertSame(5, $revista->articulos()->whereNotNull('arre_posicion_index')->count());
        $this->assertSame(5, $revista->articulos()->whereDate('arre_fecha_index', today())->count());
    }

    public function test_rotar_conserva_la_fecha_de_los_que_siguen_y_marca_los_pendientes(): void
    {
        $revista = $this->crearNumero();
        $this->agregar($revista, 7);
        $url = "/admin/ediciones/{$revista->revi_id_revista}/indice";
        $this->post($url, ['posiciones' => $this->posiciones($revista, ['Artículo 1', 'Artículo 2', 'Artículo 3', 'Artículo 4', 'Artículo 5'])]);

        $this->travel(15)->days();
        $this->flushSession(); // la sesión anterior venció por inactividad
        $this->actingAs($this->administrador());
        // Salen el 4 y el 5; entran el 6 y el 7; el 1 pasa a la posición 2 y el 2 a la 1.
        $this->post($url, ['posiciones' => $this->posiciones($revista, ['Artículo 2', 'Artículo 1', 'Artículo 3', 'Artículo 6', 'Artículo 7'])])
            ->assertSessionHasNoErrors();

        $porTitulo = $revista->articulos()->get()->keyBy('arre_titulo');
        $this->assertSame(1, $porTitulo['Artículo 2']->arre_posicion_index);
        $this->assertTrue($porTitulo['Artículo 1']->arre_fecha_index->isSameDay(today()->subDays(15)), 'Sigue en el índice: conserva su fecha.');
        $this->assertTrue($porTitulo['Artículo 6']->arre_fecha_index->isToday());
        $this->assertNull($porTitulo['Artículo 4']->arre_posicion_index);
        $this->assertNotNull($porTitulo['Artículo 4']->arre_fecha_index, 'Ya se mostró: no vuelve a «pendiente».');

        $this->get("/admin/ediciones/{$revista->revi_id_revista}")->assertOk()->assertSee('Se han mostrado')->assertSee('7 de 7');
    }

    public function test_los_cursos_se_sugieren_y_la_eleccion_del_administrador_manda(): void
    {
        $revista = $this->crearNumero();
        $cursos = app(CursoService::class);
        $finanzas = $cursos->crear(['nombre' => 'Finanzas para emprendedores', 'institucion' => 'FCA UNAM', 'fecha' => now()->addMonth()->toDateString(), 'url' => 'https://fca.unam.mx/finanzas', 'palabras' => ['finanzas']]);
        $mercado = $cursos->crear(['nombre' => 'Mercadotecnia digital', 'institucion' => 'FCA UNAM', 'fecha' => now()->addMonth()->toDateString(), 'url' => 'https://fca.unam.mx/mkt', 'palabras' => ['mercadotecnia']]);

        $this->actingAs($this->administrador())->post("/admin/ediciones/{$revista->revi_id_revista}/articulos", [
            'titulo' => 'Finanzas sanas en la microempresa', 'autores' => 'Laura Méndez',
        ]);
        $entrada = $revista->articulos()->firstOrFail();
        $servicio = app(EdicionService::class);

        $this->assertSame([$finanzas->curs_id_curso], $servicio->cursosDe($entrada)->pluck('curs_id_curso')->all(), 'Sugerido por el título.');

        // El administrador quita el sugerido y agrega otro.
        $this->put("/admin/ediciones/{$revista->revi_id_revista}/articulos/{$entrada->arre_id_articulo_revista}", [
            'titulo' => 'Finanzas sanas en la microempresa', 'autores' => 'Laura Méndez', 'cursos' => [$mercado->curs_id_curso],
            'sugeridos' => [$finanzas->curs_id_curso],
        ])->assertRedirect();

        $entrada = $entrada->fresh();
        $this->assertSame([$mercado->curs_id_curso], $servicio->cursosDe($entrada)->pluck('curs_id_curso')->all());
        $this->assertDatabaseHas('articulo_curso', ['curs_id_curso' => $finanzas->curs_id_curso, 'arcu_incluido' => false]);
        $this->assertDatabaseHas('articulo_curso', ['curs_id_curso' => $mercado->curs_id_curso, 'arcu_incluido' => true]);
    }

    public function test_los_archivos_solo_se_ven_si_el_numero_es_visible(): void
    {
        $visible = $this->crearNumero();
        $oculto = $this->crearNumero(['numero' => '221', 'visible' => '0']);
        auth()->logout();

        $this->get("/edicion/{$visible->revi_id_revista}/portada")->assertOk()->assertHeader('Content-Type', 'image/jpeg');
        $this->get("/edicion/{$visible->revi_id_revista}/numero")->assertOk();
        $this->get("/edicion/{$oculto->revi_id_revista}/portada")->assertNotFound();

        $this->actingAs($this->administrador())->get("/edicion/{$oculto->revi_id_revista}/portada")->assertOk();
    }

    public function test_el_inicio_muestra_los_cinco_del_indice_del_numero_en_portada(): void
    {
        $revista = $this->crearNumero();
        $this->agregar($revista, 6);
        $this->post("/admin/ediciones/{$revista->revi_id_revista}/indice", [
            'posiciones' => $this->posiciones($revista, ['Artículo 3', 'Artículo 1', 'Artículo 2', 'Artículo 4', 'Artículo 5']),
        ]);

        // Sin portada, el inicio conserva el contenido anterior.
        $this->get('/')->assertOk()->assertDontSee('Artículo 3');

        $this->post("/admin/ediciones/{$revista->revi_id_revista}/portada");
        auth()->logout();

        $this->get('/')->assertOk()
            ->assertSee('Diferenciación para generar ventajas')
            ->assertSee('No. 220')
            ->assertSee('https://flipbook.ejemplo.mx/220', false)
            ->assertSeeInOrder(['Artículo 3', 'Artículo 1', 'Artículo 2', 'Artículo 4', 'Artículo 5'])
            ->assertDontSee('Artículo 6');
    }

    public function test_al_ponerlo_como_principal_el_inicio_cambia_portada_pdf_y_ebook_aunque_falten_destacados(): void
    {
        $revista = $this->crearNumero();
        $this->post("/admin/ediciones/{$revista->revi_id_revista}/portada")->assertRedirect();
        auth()->logout();

        $this->get('/')->assertOk()
            ->assertSee('No. 220')
            ->assertSee('Diferenciación para generar ventajas')
            ->assertSee(route('edicion.portada', $revista), false)
            ->assertSee(route('edicion.numero', $revista), false)
            ->assertSee('https://flipbook.ejemplo.mx/220', false)
            ->assertDontSee('Artículo principal');
    }

    public function test_el_numero_que_deja_de_ser_principal_pasa_al_historico(): void
    {
        $anterior = $this->crearNumero(['titulo' => 'Número anterior']);
        $nuevo = $this->crearNumero(['numero' => '221', 'periodo' => 'septiembre-octubre', 'titulo' => 'Número nuevo']);
        $this->post("/admin/ediciones/{$anterior->revi_id_revista}/portada");
        $this->post("/admin/ediciones/{$nuevo->revi_id_revista}/portada");
        auth()->logout();

        $this->get('/historico')->assertOk()
            ->assertSee('Número anterior')
            ->assertSee(route('edicion.portada', $anterior), false)
            ->assertSee(route('edicion.numero', $anterior), false)
            ->assertDontSee('Número nuevo');
        $this->get('/')->assertSee('Número nuevo');
    }

    /** @param  array<string, string>  $archivos  nombre => contenido */
    private function zip(array $archivos): UploadedFile
    {
        $ruta = tempnam(sys_get_temp_dir(), 'zip');
        $zip = new \ZipArchive;
        $zip->open($ruta, \ZipArchive::OVERWRITE);
        foreach ($archivos as $nombre => $contenido) {
            $zip->addFromString($nombre, $contenido);
        }
        $zip->close();

        return new UploadedFile($ruta, 'articulos.zip', null, null, true);
    }

    public function test_el_zip_vincula_cada_pdf_con_su_articulo_por_numero_o_por_titulo(): void
    {
        $revista = $this->crearNumero();
        $porNumero = $this->enviarArticulo($this->crearAutor(), ['titulo' => 'Finanzas sanas en la microempresa']);
        $porTitulo = $this->enviarArticulo($this->crearAutor(), ['titulo' => 'Logística inversa en talleres']);
        foreach ([$porNumero, $porTitulo] as $articulo) {
            app(ConstanciaService::class)->aprobar($articulo, $this->administrador());
        }
        $pdf = "%PDF-1.4\n%%EOF\n";

        $this->actingAs($this->administrador())->post("/admin/ediciones/{$revista->revi_id_revista}/zip", [
            'zip' => $this->zip([
                "maquetados/{$porNumero->arti_id_autor}-finanzas.pdf" => $pdf,
                'Logistica inversa en talleres.pdf' => $pdf,
                'otro-articulo.pdf' => $pdf,
                'falso.pdf' => 'no soy pdf',
                '../../escapar.pdf' => $pdf,
                'notas.txt' => 'hola',
            ]),
        ])->assertRedirect()->assertSessionHas('exito', 'Se vincularon 2 PDF con sus artículos.');

        $entradas = $revista->articulos()->with('articulo.palabras')->get()->keyBy('arti_id_autor');
        $this->assertCount(2, $entradas);
        foreach ([$porNumero, $porTitulo] as $articulo) {
            $entrada = $entradas[$articulo->arti_id_autor];
            Storage::disk('local')->assertExists($entrada->arre_url_pdf);
            $this->assertSame($articulo->arti_titulo, $entrada->arre_titulo);
        }
        // Las palabras clave del artículo del sistema quedan disponibles para sugerir cursos.
        $this->assertCount(5, $entradas[$porNumero->arti_id_autor]->articulo->palabras);

        $resultado = session('zip_resultado');
        $this->assertContains('otro-articulo.pdf', $resultado['no_reconocidos']);
        $this->assertContains('notas.txt (no es PDF)', $resultado['no_reconocidos']);
        $this->assertTrue(collect($resultado['no_reconocidos'])->contains(fn ($n) => str_starts_with($n, 'escapar.pdf') || str_starts_with($n, 'falso.pdf')));
        Storage::disk('local')->assertMissing('escapar.pdf');
    }

    public function test_un_zip_invalido_no_rompe_nada(): void
    {
        $revista = $this->crearNumero();
        $falso = tempnam(sys_get_temp_dir(), 'zip');
        file_put_contents($falso, 'no soy zip');

        $this->actingAs($this->administrador())
            ->post("/admin/ediciones/{$revista->revi_id_revista}/zip", ['zip' => new UploadedFile($falso, 'a.zip', null, null, true)])
            ->assertSessionHasErrors('zip');
    }

    public function test_descarga_los_manuscritos_aprobados_sin_numero_con_su_id(): void
    {
        $articulo = $this->enviarArticulo($this->crearAutor(), ['titulo' => 'Finanzas sanas']);
        app(ConstanciaService::class)->aprobar($articulo, $this->administrador());

        $respuesta = $this->actingAs($this->administrador())->get('/admin/ediciones/manuscritos')->assertOk();
        $zip = new \ZipArchive;
        $zip->open($respuesta->baseResponse->getFile()->getPathname());
        $this->assertSame("{$articulo->arti_id_autor}-finanzas-sanas.pdf", $zip->getNameIndex(0));
        $zip->close();
    }

    public function test_eliminar_un_numero_borra_sus_articulos_y_archivos(): void
    {
        $revista = $this->crearNumero();
        $this->agregar($revista, 2);
        $portada = $revista->revi_url_portada;

        $this->delete("/admin/ediciones/{$revista->revi_id_revista}")->assertRedirect('/admin/ediciones');

        $this->assertSame(0, ArticuloRevista::count());
        Storage::disk('local')->assertMissing($portada);
    }
}
