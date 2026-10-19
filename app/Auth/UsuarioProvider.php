<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;

/**
 * Proveedor de usuarios de la revista.
 *
 * La tabla usuario no tiene correo: se busca en comunicacion (tipo "Correo…",
 * activo). Se acepta una sola credencial:
 *   - "acceso": correo (si tiene @) o nombre de usuario, sin importar mayúsculas;
 *   - "email": la que usa el password broker de Laravel (siempre correo).
 */
class UsuarioProvider extends EloquentUserProvider
{
    public function retrieveByCredentials(#[\SensitiveParameter] array $credentials)
    {
        $acceso = trim((string) ($credentials['acceso'] ?? $credentials['email'] ?? ''));

        if ($acceso === '') {
            return null;
        }

        $consulta = $this->newModelQuery();

        if (str_contains($acceso, '@')) {
            $consulta->whereHas('persona.comunicaciones', fn (Builder $q) => $q
                ->correos()
                ->where('comu_activo', true)
                ->conValor($acceso));
        } else {
            $consulta->whereRaw('lower(usua_nombre_usuario) = lower(?)', [$acceso]);
        }

        return $consulta->first();
    }

    /** La tabla usuario no tiene remember_token: "recordarme" no existe. */
    public function updateRememberToken(Authenticatable $user, #[\SensitiveParameter] $token)
    {
        //
    }

    public function retrieveByToken($identifier, #[\SensitiveParameter] $token)
    {
        return null;
    }
}
