<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Manejo central de QueryException (bootstrap/app.php + App\Support\ErrorBaseDatos):
 * las reglas de la base se muestran como mensajes claros y nunca como error 500.
 *
 * Las rutas de prueba escriben dentro de DB::transaction(), que aquí es un
 * savepoint: tras el error, la transacción de la prueba sigue utilizable.
 */
class ErroresBaseDatosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $duplicado = fn () => DB::transaction(fn () => DB::table('tema')->insert([
            'tema_nombre' => 'Comunicación', 'tema_descripcion' => 'Comunicación',
        ]));
        $enUso = fn () => DB::transaction(fn () => DB::table('rol')->where('rol_nombre', 'Autor')->delete());
        $regla = fn () => DB::transaction(fn () => DB::table('persona')->insert([
            'pers_nombre' => '   ', 'pers_primer_apellido' => 'Pérez',
        ]));

        Route::middleware('web')->group(function () use ($duplicado, $enUso, $regla) {
            Route::post('/_prueba/duplicado', $duplicado);
            Route::get('/_prueba/duplicado', $duplicado);
            Route::delete('/_prueba/en-uso', $enUso);
            Route::post('/_prueba/regla', $regla);
            Route::post('/_prueba/sql-invalido', fn () => DB::transaction(fn () => DB::select('select * from tabla_que_no_existe')));
        });
    }

    public function test_un_duplicado_regresa_al_formulario_con_mensaje_claro_y_sin_la_contrasena(): void
    {
        $this->from('/formulario')
            ->post('/_prueba/duplicado', ['nombre' => 'Luis', 'clave' => 'secreta-123', 'clave_confirmation' => 'secreta-123'])
            ->assertRedirect('/formulario')
            ->assertSessionHas('error', 'Ese tema ya existe.')
            ->assertSessionHasErrors(['general' => 'Ese tema ya existe.'])
            ->assertSessionHasInput('nombre', 'Luis')
            ->assertSessionMissing('_old_input.clave')
            ->assertSessionMissing('_old_input.clave_confirmation');

        // La transacción de la prueba sigue viva: el savepoint se revirtió.
        $this->assertSame(13, DB::table('tema')->count());
    }

    public function test_borrar_un_registro_en_uso_muestra_mensaje_y_no_borra(): void
    {
        $this->from('/admin')
            ->delete('/_prueba/en-uso')
            ->assertRedirect('/admin')
            ->assertSessionHas('error', 'No se puede eliminar: el registro está en uso.');

        $this->assertTrue(DB::table('rol')->where('rol_nombre', 'Autor')->exists());
    }

    public function test_una_regla_de_negocio_se_traduce(): void
    {
        $this->from('/formulario')
            ->post('/_prueba/regla')
            ->assertRedirect('/formulario')
            ->assertSessionHas('error', 'El nombre y el primer apellido son obligatorios.');
    }

    public function test_las_peticiones_json_reciben_422_con_el_mensaje(): void
    {
        $this->postJson('/_prueba/duplicado')
            ->assertStatus(422)
            ->assertJson(['message' => 'Ese tema ya existe.', 'errors' => ['general' => ['Ese tema ya existe.']]]);
    }

    public function test_en_una_pagina_get_muestra_la_pagina_409(): void
    {
        $this->get('/_prueba/duplicado')
            ->assertStatus(409)
            ->assertSee('No se pudo completar la operación')
            ->assertSee('Ese tema ya existe.');
    }

    public function test_otros_errores_de_sql_no_se_disfrazan(): void
    {
        $this->withoutExceptionHandling();
        $this->expectException(QueryException::class);

        $this->post('/_prueba/sql-invalido');
    }

    public function test_un_error_de_validacion_no_guarda_la_contrasena_en_la_sesion(): void
    {
        $this->from('/registro')
            ->post('/registro', ['nombre' => 'Luis', 'clave' => 'una-clave-segura', 'clave_confirmation' => 'otra'])
            ->assertRedirect('/registro')
            ->assertSessionHasErrors('correo')
            ->assertSessionHasInput('nombre', 'Luis')
            ->assertSessionMissing('_old_input.clave')
            ->assertSessionMissing('_old_input.clave_confirmation');
    }

    public function test_las_paginas_de_error_estan_en_espanol(): void
    {
        $this->get('/pagina-que-no-existe')
            ->assertNotFound()
            ->assertSee('Página no encontrada')
            ->assertSee('Ir al inicio');
    }
}
