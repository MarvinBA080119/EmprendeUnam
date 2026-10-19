<?php

namespace App\Console\Commands;

use App\Models\Usuario;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Cambia la contraseña del administrador (usuario EmprendeUnam) desde la
 * terminal del servidor. La contraseña se pide oculta y solo se guarda su
 * hash en usuario.usua_clave_acceso: nunca se escribe en ningún archivo.
 * Se usa al instalar en producción, porque datos_iniciales.sql trae un hash
 * que está en el repositorio.
 */
class ClaveAdministrador extends Command
{
    protected $signature = 'emprende:clave-admin';

    protected $description = 'Cambia la contraseña del administrador EmprendeUnam (se pide oculta en la terminal)';

    public const MINIMO = 12;

    public function handle(): int
    {
        $admin = Usuario::where('usua_nombre_usuario', 'EmprendeUnam')->first();

        if (! $admin) {
            $this->error('No existe el usuario EmprendeUnam. Carga primero database/datos_iniciales.sql.');

            return self::FAILURE;
        }

        $clave = (string) $this->secret('Nueva contraseña del administrador (mínimo '.self::MINIMO.' caracteres)');

        if (mb_strlen($clave) < self::MINIMO || strlen($clave) > 72) {
            $this->error('La contraseña debe tener entre '.self::MINIMO.' y 72 caracteres. No se cambió nada.');

            return self::FAILURE;
        }

        if ((string) $this->secret('Escríbela de nuevo') !== $clave) {
            $this->error('Las contraseñas no coinciden. No se cambió nada.');

            return self::FAILURE;
        }

        DB::transaction(function () use ($admin, $clave) {
            $admin->forceFill(['usua_clave_acceso' => Hash::make($clave)])->save();

            // Cierra las sesiones abiertas con la contraseña anterior.
            DB::table('sessions')->where('user_id', $admin->usua_id_usuario)->delete();
        });

        Log::channel('admin')->info('Contraseña del administrador cambiada desde la terminal', [
            'usuario' => $admin->usua_nombre_usuario,
        ]);

        $this->info('Listo: se cambió la contraseña de EmprendeUnam y se cerraron sus sesiones abiertas.');

        return self::SUCCESS;
    }
}
