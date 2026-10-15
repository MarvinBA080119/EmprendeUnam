<?php

namespace Tests\Concerns;

use App\Models\Usuario;
use App\Services\RegistroAutorService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

/**
 * Cuentas para las pruebas. Todo se crea dentro de la transacción de la
 * prueba y se revierte al terminar.
 */
trait CreaCuentas
{
    protected const CLAVE_PRUEBA = 'clave-de-prueba-123';

    /** @param  array<string, mixed>  $datos */
    protected function crearAutor(bool $activo = true, array $datos = []): Usuario
    {
        Mail::fake();

        $usuario = app(RegistroAutorService::class)->registrar(array_merge([
            'nombre' => 'Ana',
            'primer_apellido' => 'López',
            'segundo_apellido' => 'Ruiz',
            'correo' => 'ana.lopez'.uniqid().'@example.com',
            'clave' => self::CLAVE_PRUEBA,
            'institucion' => 'FCA UNAM',
            'grado_academico' => 'Maestría',
        ], $datos));

        if ($activo) {
            $usuario->forceFill(['usua_activo' => true])->save();
        }

        return $usuario->fresh();
    }

    /**
     * El administrador real. Su contraseña no se conoce ni se escribe: si la
     * prueba necesita iniciar sesión con ella, se cambia el hash solo dentro de
     * la transacción de la prueba, que se revierte.
     */
    protected function administrador(bool $conClaveDePrueba = false): Usuario
    {
        $admin = Usuario::where('usua_nombre_usuario', 'EmprendeUnam')->firstOrFail();

        if ($conClaveDePrueba) {
            $admin->forceFill(['usua_clave_acceso' => Hash::make(self::CLAVE_PRUEBA)])->save();
        }

        return $admin;
    }
}
