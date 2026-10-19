<?php

namespace Tests\Feature;

use App\Models\Persona;
use App\Models\Rol;
use App\Models\TipoComunicacion;
use App\Models\Usuario;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Comprueba que los modelos leen correctamente el esquema y los datos
 * iniciales, y que los triggers responden como espera la aplicación.
 */
class BaseDeDatosTest extends TestCase
{
    public function test_el_administrador_tiene_rol_privilegios_y_correo(): void
    {
        $admin = Usuario::with(['roles.privilegios', 'persona.correo'])
            ->where('usua_nombre_usuario', 'EmprendeUnam')
            ->firstOrFail();

        $this->assertTrue($admin->usua_activo);
        $this->assertSame(['Administrador'], $admin->roles->pluck('rol_nombre')->all());
        $this->assertCount(12, $admin->roles->first()->privilegios);
        $this->assertNotNull($admin->persona->correo);
        $this->assertSame('Principal', $admin->persona->correo->comu_etiqueta);
        $this->assertStringStartsWith('$2y$', $admin->getAuthPassword());
    }

    public function test_los_privilegios_de_cada_rol_coinciden_con_la_especificacion(): void
    {
        $privilegios = Rol::with('privilegios')->get()
            ->mapWithKeys(fn (Rol $rol) => [$rol->rol_nombre => $rol->privilegios->pluck('priv_nombre')->sort()->values()->all()]);

        $this->assertSame(
            ['CONSULTAR_ARTICULOS_PROPIOS', 'DESCARGAR_CONSTANCIA', 'ENVIAR_ARTICULO'],
            $privilegios[Rol::AUTOR],
        );
        $this->assertSame(['CONSULTAR_CONTENIDO_PUBLICO', 'ENVIAR_MENSAJE_CONSULTORIO'], $privilegios[Rol::LECTOR]);
        $this->assertSame(['CONSULTAR_CONTENIDO_PUBLICO', 'SUSCRIBIRSE_NEWSLETTER'], $privilegios[Rol::SUSCRIPTOR_NEWSLETTER]);
        $this->assertCount(12, $privilegios[Rol::ADMINISTRADOR]);
    }

    public function test_un_usuario_sin_correo_es_rechazado_por_el_trigger_diferido(): void
    {
        DB::statement('SET CONSTRAINTS ALL IMMEDIATE');

        $persona = Persona::create(['pers_nombre' => 'Sin', 'pers_primer_apellido' => 'Correo']);

        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('no tiene un correo electrónico activo');

        Usuario::create([
            'pers_id_persona' => $persona->pers_id_persona,
            'usua_nombre_usuario' => 'sincorreo',
            'usua_clave_acceso' => Hash::make('secreto-de-prueba'),
            'usua_fecha_registro' => today(),
            'usua_activo' => false,
        ]);
    }

    public function test_persona_correo_y_usuario_en_la_misma_transaccion_pasan_el_trigger(): void
    {
        DB::transaction(function () {
            $persona = Persona::create(['pers_nombre' => 'Con', 'pers_primer_apellido' => 'Correo']);
            $persona->comunicaciones()->create([
                'comu_id_tipo_comunicacion' => TipoComunicacion::correo()->value('comu_id_tipo_comunicacion'),
                'comu_valor_comunicacion' => 'con.correo@example.com',
                'comu_etiqueta' => 'Principal',
                'comu_activo' => true,
            ]);
            Usuario::create([
                'pers_id_persona' => $persona->pers_id_persona,
                'usua_nombre_usuario' => 'concorreo',
                'usua_clave_acceso' => Hash::make('secreto-de-prueba'),
                'usua_fecha_registro' => today(),
                'usua_activo' => false,
            ]);
        });

        DB::statement('SET CONSTRAINTS ALL IMMEDIATE');

        $this->assertDatabaseHas('usuario', ['usua_nombre_usuario' => 'concorreo']);
    }
}
