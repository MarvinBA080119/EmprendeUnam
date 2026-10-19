<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/** Comando emprende:clave-admin (la contraseña real nunca se escribe en pruebas). */
class ClaveAdministradorTest extends TestCase
{
    private const NUEVA = 'clave-temporal-de-prueba-2026';

    private function admin(): Usuario
    {
        return Usuario::where('usua_nombre_usuario', 'EmprendeUnam')->firstOrFail();
    }

    public function test_cambia_la_contrasena_y_cierra_las_sesiones(): void
    {
        $admin = $this->admin();
        DB::table('sessions')->insert([
            'id' => 'sesion-admin', 'user_id' => $admin->usua_id_usuario, 'ip_address' => '10.0.0.1',
            'user_agent' => 'prueba', 'payload' => '', 'last_activity' => time(),
        ]);

        $this->artisan('emprende:clave-admin')
            ->expectsQuestion('Nueva contraseña del administrador (mínimo 12 caracteres)', self::NUEVA)
            ->expectsQuestion('Escríbela de nuevo', self::NUEVA)
            ->assertSuccessful();

        $this->assertTrue(Hash::check(self::NUEVA, $admin->fresh()->usua_clave_acceso));
        $this->assertFalse(DB::table('sessions')->where('id', 'sesion-admin')->exists());
    }

    public function test_no_cambia_nada_si_no_coinciden(): void
    {
        $antes = $this->admin()->usua_clave_acceso;

        $this->artisan('emprende:clave-admin')
            ->expectsQuestion('Nueva contraseña del administrador (mínimo 12 caracteres)', self::NUEVA)
            ->expectsQuestion('Escríbela de nuevo', 'otra-cosa-distinta-2026')
            ->assertFailed();

        $this->assertSame($antes, $this->admin()->fresh()->usua_clave_acceso);
    }

    public function test_rechaza_una_contrasena_corta(): void
    {
        $antes = $this->admin()->usua_clave_acceso;

        $this->artisan('emprende:clave-admin')
            ->expectsQuestion('Nueva contraseña del administrador (mínimo 12 caracteres)', 'corta')
            ->assertFailed();

        $this->assertSame($antes, $this->admin()->fresh()->usua_clave_acceso);
    }
}
