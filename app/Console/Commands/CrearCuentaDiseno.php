<?php

namespace App\Console\Commands;

use App\Models\Comunicacion;
use App\Models\Persona;
use App\Models\Rol;
use App\Models\RolUsuario;
use App\Models\TipoComunicacion;
use App\Models\Usuario;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Crea la cuenta de diseño (usuario «Diseño», sin nombre propio, como la del
 * administrador) o, si ya existe, cambia su correo o su contraseña. El correo
 * y la contraseña se piden en la terminal: la contraseña va oculta y solo se
 * guarda su hash; nunca se escribe en un archivo.
 *
 * persona + comunicacion (Correo) + usuario + rol_usuario van en una sola
 * transacción: el trigger diferido revisa el correo al COMMIT.
 */
class CrearCuentaDiseno extends Command
{
    public const USUARIO = 'Diseño';

    protected $signature = 'emprende:crear-diseno';

    protected $description = 'Crea la cuenta «Diseño» o cambia su correo o contraseña (se piden en la terminal)';

    public function handle(): int
    {
        if (! Rol::where('rol_nombre', Rol::DISENO)->exists()) {
            $this->error('No existe el rol «Diseño». Aplica primero database/cambios/2026-10-24_diseno.sql.');

            return self::FAILURE;
        }

        $cuenta = Usuario::with('persona.correo')
            ->whereRaw('lower(usua_nombre_usuario) = lower(?)', [self::USUARIO])
            ->first();
        $correoActual = $cuenta?->persona?->correo?->comu_valor_comunicacion;

        $correo = trim((string) $this->ask($cuenta
            ? "Correo de la cuenta Diseño (Enter para conservar {$correoActual})"
            : 'Correo de la cuenta Diseño'));
        $correo = $correo === '' ? (string) $correoActual : mb_strtolower($correo);

        if (Validator::make(['correo' => $correo], ['correo' => ['required', 'email:rfc', 'max:254']])->fails()) {
            $this->error('Escribe un correo válido. No se cambió nada.');

            return self::FAILURE;
        }

        $ocupado = Comunicacion::correos()->conValor($correo)
            ->when($cuenta, fn ($q) => $q->where('pers_id_persona', '<>', $cuenta->pers_id_persona))
            ->exists();
        if ($ocupado) {
            $this->error('Ese correo ya pertenece a otra cuenta. No se cambió nada.');

            return self::FAILURE;
        }

        $clave = (string) $this->secret($cuenta
            ? 'Nueva contraseña (mínimo '.ClaveAdministrador::MINIMO.' caracteres; Enter para conservar la actual)'
            : 'Contraseña (mínimo '.ClaveAdministrador::MINIMO.' caracteres)');

        if ($clave !== '' || ! $cuenta) {
            if (mb_strlen($clave) < ClaveAdministrador::MINIMO || strlen($clave) > 72) {
                $this->error('La contraseña debe tener entre '.ClaveAdministrador::MINIMO.' y 72 caracteres. No se cambió nada.');

                return self::FAILURE;
            }

            if ((string) $this->secret('Escríbela de nuevo') !== $clave) {
                $this->error('Las contraseñas no coinciden. No se cambió nada.');

                return self::FAILURE;
            }
        }

        $cuenta = DB::transaction(function () use ($cuenta, $correo, $clave) {
            if ($cuenta === null) {
                $persona = Persona::create(['pers_nombre' => 'Diseño', 'pers_primer_apellido' => 'Revista EmprendeUNAM']);

                Comunicacion::create([
                    'pers_id_persona' => $persona->pers_id_persona,
                    'comu_id_tipo_comunicacion' => (int) TipoComunicacion::correo()->value('comu_id_tipo_comunicacion'),
                    'comu_valor_comunicacion' => $correo,
                    'comu_etiqueta' => 'Principal',
                    'comu_activo' => true,
                ]);

                $cuenta = new Usuario;
                $cuenta->forceFill([
                    'pers_id_persona' => $persona->pers_id_persona,
                    'usua_nombre_usuario' => self::USUARIO,
                    'usua_clave_acceso' => Hash::make($clave),
                    'usua_fecha_registro' => today(),
                    'usua_activo' => true,
                    'usua_correo_verificado' => true,
                    'usua_fecha_verificacion' => now(),
                ])->save();

                RolUsuario::create([
                    'usua_id_usuario' => $cuenta->usua_id_usuario,
                    'rol_id_rol' => Rol::where('rol_nombre', Rol::DISENO)->value('rol_id_rol'),
                ]);

                return $cuenta;
            }

            $cuenta->persona->correo?->forceFill(['comu_valor_comunicacion' => $correo])->save();

            if ($clave !== '') {
                $cuenta->forceFill(['usua_clave_acceso' => Hash::make($clave)])->save();
                // Cierra las sesiones abiertas con la contraseña anterior.
                DB::table('sessions')->where('user_id', $cuenta->usua_id_usuario)->delete();
            }

            return $cuenta;
        });

        Log::channel('admin')->info($cuenta->wasRecentlyCreated ? 'Cuenta de diseño creada desde la terminal' : 'Cuenta de diseño actualizada desde la terminal', [
            'usuario' => $cuenta->usua_nombre_usuario,
        ]);

        $this->info($cuenta->wasRecentlyCreated
            ? "Listo: se creó la cuenta «Diseño» con el correo {$correo}. Entra en /iniciar-sesion con el usuario Diseño."
            : "Listo: se actualizó la cuenta «Diseño» ({$correo}).");

        return self::SUCCESS;
    }
}
