<?php

namespace Tests\Feature\Admin;

use App\Enums\EstatusArticulo;
use App\Enums\TipoDocumento;
use App\Mail\ArticuloAprobado;
use App\Mail\ArticuloRechazado;
use App\Models\Constancia;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreaArticulos;
use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

class DictamenTest extends TestCase
{
    use CreaArticulos, CreaCuentas;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function test_el_administrador_ve_la_lista_y_el_detalle(): void
    {
        $articulo = $this->enviarArticulo($this->crearAutor(datos: ['nombre' => 'Rosa']), [
            'tiene_coautores' => true,
            'coautores' => [['nombre' => 'Hugo', 'primer_apellido' => 'Paz', 'correo' => 'hugo@example.com', 'institucion' => 'UAM']],
        ]);

        $this->actingAs($this->administrador())->getJson('/admin/articulos/datos')
            ->assertOk()
            ->assertJsonFragment(['titulo' => $articulo->arti_titulo]);

        $this->actingAs($this->administrador())->get("/admin/articulos/{$articulo->arti_id_autor}")
            ->assertOk()
            ->assertSee('Rosa')
            ->assertSee('Hugo')
            ->assertSee('Coautor')
            ->assertSee('emprendimiento social')
            ->assertSee('Aprobar y generar constancia');

        $this->actingAs($this->administrador())->get("/admin/articulos/{$articulo->arti_id_autor}/pdf")->assertOk();
    }

    public function test_aprobar_genera_constancia_con_folio_unico_y_consecutivo(): void
    {
        $autor = $this->crearAutor();
        $primero = $this->enviarArticulo($autor);
        $segundo = $this->enviarArticulo($autor);
        Mail::fake();
        $anio = today()->year;
        $previas = Constancia::where('cons_folio', 'like', "EMP-{$anio}-%")->count();

        $this->actingAs($this->administrador())
            ->post("/admin/articulos/{$primero->arti_id_autor}/aprobar")
            ->assertRedirect("/admin/articulos/{$primero->arti_id_autor}")
            ->assertSessionHas('exito');
        $this->actingAs($this->administrador())->post("/admin/articulos/{$segundo->arti_id_autor}/aprobar");

        $c1 = $primero->fresh()->constancia;
        $c2 = $segundo->fresh()->constancia;

        $this->assertSame(sprintf('EMP-%d-%04d', $anio, $previas + 1), $c1->cons_folio);
        $this->assertSame(sprintf('EMP-%d-%04d', $anio, $previas + 2), $c2->cons_folio);
        $this->assertSame(EstatusArticulo::APROBADO, $primero->fresh()->arti_status);
        $this->assertSame("constancias/{$c1->cons_folio}.pdf", $c1->cons_url_archivo);
        Storage::disk('local')->assertExists($c1->cons_url_archivo);
        $this->assertStringStartsWith('%PDF', Storage::disk('local')->get($c1->cons_url_archivo));

        Mail::assertQueued(ArticuloAprobado::class, fn ($m) => $m->hasTo($autor->correo()) && $m->folio === $c1->cons_folio);
    }

    public function test_no_se_puede_aprobar_dos_veces_ni_regenerar_la_constancia(): void
    {
        $articulo = $this->enviarArticulo($this->crearAutor());
        Mail::fake();
        $admin = $this->administrador();

        $this->actingAs($admin)->post("/admin/articulos/{$articulo->arti_id_autor}/aprobar");
        $folio = $articulo->fresh()->constancia->cons_folio;

        $this->actingAs($admin)
            ->post("/admin/articulos/{$articulo->arti_id_autor}/aprobar")
            ->assertSessionHasErrors('articulo');

        $this->assertSame(1, Constancia::where('arti_id_autor', $articulo->arti_id_autor)->deTipo(TipoDocumento::CONSTANCIA)->count());
        $this->assertSame($folio, $articulo->fresh()->constancia->cons_folio);
        Mail::assertQueued(ArticuloAprobado::class, 1);
    }

    public function test_rechazar_exige_un_motivo_de_al_menos_20_caracteres(): void
    {
        $articulo = $this->enviarArticulo($this->crearAutor());

        $this->actingAs($this->administrador())
            ->post("/admin/articulos/{$articulo->arti_id_autor}/rechazar", ['comentario' => 'Muy corto'])
            ->assertSessionHasErrors('comentario');

        $this->actingAs($this->administrador())
            ->post("/admin/articulos/{$articulo->arti_id_autor}/rechazar", ['comentario' => ''])
            ->assertSessionHasErrors('comentario');

        $this->assertSame(EstatusArticulo::EN_REVISION, $articulo->fresh()->arti_status);
    }

    public function test_rechazar_guarda_el_motivo_y_avisa_al_autor(): void
    {
        $autor = $this->crearAutor();
        $articulo = $this->enviarArticulo($autor);
        Mail::fake();
        $motivo = 'El tema no corresponde a la línea editorial de la revista.';

        $this->actingAs($this->administrador())
            ->post("/admin/articulos/{$articulo->arti_id_autor}/rechazar", ['comentario' => $motivo])
            ->assertSessionHas('exito');

        $articulo->refresh();
        $this->assertSame(EstatusArticulo::RECHAZADO, $articulo->arti_status);
        $this->assertSame($motivo, $articulo->arti_comentario_rechazo);
        $this->assertNull($articulo->constancia);
        Mail::assertQueued(ArticuloRechazado::class, fn ($m) => $m->hasTo($autor->correo()) && $m->motivo === $motivo);
    }

    public function test_un_articulo_rechazado_no_se_puede_aprobar(): void
    {
        $articulo = $this->enviarArticulo($this->crearAutor());
        $admin = $this->administrador();
        Mail::fake();

        $this->actingAs($admin)->post("/admin/articulos/{$articulo->arti_id_autor}/rechazar", [
            'comentario' => 'Motivo suficientemente largo para rechazar.',
        ]);
        $this->actingAs($admin)
            ->post("/admin/articulos/{$articulo->arti_id_autor}/aprobar")
            ->assertSessionHasErrors('articulo');

        $this->assertSame(EstatusArticulo::RECHAZADO, $articulo->fresh()->arti_status);
    }

    public function test_un_autor_no_puede_dictaminar_ni_ver_articulos_ajenos_desde_admin(): void
    {
        $autor = $this->crearAutor();
        $articulo = $this->enviarArticulo($autor);

        $this->actingAs($autor)->get('/admin/articulos')->assertForbidden();
        $this->actingAs($autor)->post("/admin/articulos/{$articulo->arti_id_autor}/aprobar")->assertForbidden();
        $this->actingAs($autor)->post("/admin/articulos/{$articulo->arti_id_autor}/rechazar", [
            'comentario' => 'Intento de autorrechazo con texto largo.',
        ])->assertForbidden();

        $this->assertSame(EstatusArticulo::EN_REVISION, $articulo->fresh()->arti_status);
    }
}
