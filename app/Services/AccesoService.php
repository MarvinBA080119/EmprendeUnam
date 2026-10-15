<?php

namespace App\Services;

use App\Http\Middleware\TienePrivilegio;
use App\Mail\RecuperarClave;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Inicio y cierre de sesión, y recuperación de contraseña.
 */
class AccesoService
{
    public const INTENTOS_POR_MINUTO = 5;

    /**
     * Valida las credenciales y abre la sesión.
     *
     * @throws ValidationException si las credenciales no son válidas, la
     *                             cuenta está inactiva o hubo demasiados intentos.
     */
    public function iniciarSesion(string $acceso, string $clave, string $ip): Usuario
    {
        $llave = $this->llaveLimite($acceso, $ip);

        if (RateLimiter::tooManyAttempts($llave, self::INTENTOS_POR_MINUTO)) {
            throw ValidationException::withMessages([
                'acceso' => __('auth.throttle', ['seconds' => RateLimiter::availableIn($llave)]),
            ]);
        }

        $proveedor = Auth::getProvider();
        $credenciales = ['acceso' => $acceso, 'password' => $clave];
        $usuario = $proveedor->retrieveByCredentials($credenciales);

        if (! $usuario instanceof Usuario || ! $proveedor->validateCredentials($usuario, $credenciales)) {
            RateLimiter::hit($llave, 60);

            throw ValidationException::withMessages(['acceso' => __('auth.failed')]);
        }

        if (! $usuario->usua_activo) {
            throw ValidationException::withMessages([
                'acceso' => 'Tu cuenta está pendiente de activación por el administrador.',
            ]);
        }

        RateLimiter::clear($llave);

        if (config('hashing.rehash_on_login', true)) {
            $proveedor->rehashPasswordIfRequired($usuario, $credenciales);
        }

        Auth::login($usuario);
        TienePrivilegio::olvidar();

        $usuario->forceFill(['usua_ultimo_login' => today()])->save();

        return $usuario;
    }

    public function cerrarSesion(): void
    {
        Auth::logout();
        TienePrivilegio::olvidar();
    }

    /** Ruta de inicio según el rol: el administrador va a /admin y el autor a /autor. */
    public function rutaInicio(Usuario $usuario): string
    {
        $usuario->loadMissing('roles');

        return match (true) {
            $usuario->tieneRol(Rol::ADMINISTRADOR) => route('admin.panel'),
            $usuario->tieneRol(Rol::AUTOR) => route('autor.panel'),
            default => route('inicio'),
        };
    }

    /**
     * Envía el enlace de recuperación al correo (tabla password_reset_tokens).
     * No revela si el correo existe: el controlador siempre responde igual.
     */
    public function enviarEnlaceRecuperacion(string $correo): void
    {
        $minutos = (int) config('auth.passwords.usuarios.expire', 60);

        Password::broker()->sendResetLink(['email' => $correo], function (Usuario $usuario, string $token) use ($minutos) {
            $destino = $usuario->correo();
            $usuario->loadMissing('persona');

            Mail::to($destino)->queue(new RecuperarClave(
                $usuario->persona->pers_nombre,
                route('password.reset', ['token' => $token, 'correo' => $destino]),
                $minutos,
            ));
        });
    }

    /**
     * Guarda la nueva contraseña si el token es válido.
     *
     * @return string Estado del broker (Password::PASSWORD_RESET si se cambió).
     */
    public function restablecerClave(string $correo, string $token, string $clave): string
    {
        return Password::broker()->reset(
            ['email' => $correo, 'token' => $token, 'password' => $clave],
            function (Usuario $usuario, string $clave) {
                $usuario->forceFill(['usua_clave_acceso' => Hash::make($clave)])->save();

                event(new PasswordReset($usuario));
            },
        );
    }

    private function llaveLimite(string $acceso, string $ip): string
    {
        return 'login:'.Str::transliterate(Str::lower(trim($acceso))).'|'.$ip;
    }
}
