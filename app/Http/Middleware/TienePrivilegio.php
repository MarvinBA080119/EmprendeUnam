<?php

namespace App\Http\Middleware;

use App\Models\Privilegio;
use App\Models\Usuario;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Autoriza por privilegio leído de la base:
 * usuario → rol_usuario → rol → rol_privilegio → privilegio.
 *
 * Uso: ->middleware('privilegio:ENVIAR_ARTICULO')
 *      ->middleware('privilegio:A,B')   basta con tener uno de ellos.
 *
 * Los privilegios del usuario se guardan en la sesión; se descartan al iniciar
 * y al cerrar sesión (AccesoService).
 */
class TienePrivilegio
{
    public const CLAVE_SESION = 'privilegios_usuario';

    public function handle(Request $request, Closure $next, string ...$privilegios): Response
    {
        $usuario = Auth::user();

        if (! $usuario instanceof Usuario) {
            return redirect()->guest(route('login'));
        }

        $propios = self::privilegiosDe($usuario);

        if (array_intersect($privilegios, $propios) === []) {
            abort(403, 'No tienes permiso para entrar a esta sección.');
        }

        return $next($request);
    }

    /**
     * @return list<string>
     */
    public static function privilegiosDe(Usuario $usuario): array
    {
        $guardado = session(self::CLAVE_SESION);

        if (is_array($guardado) && ($guardado['usuario'] ?? null) === $usuario->usua_id_usuario) {
            return $guardado['privilegios'];
        }

        $privilegios = Privilegio::query()
            ->whereHas('roles.usuarios', fn ($q) => $q->where('usuario.usua_id_usuario', $usuario->usua_id_usuario))
            ->orderBy('priv_nombre')
            ->pluck('priv_nombre')
            ->all();

        session([self::CLAVE_SESION => ['usuario' => $usuario->usua_id_usuario, 'privilegios' => $privilegios]]);

        return $privilegios;
    }

    public static function olvidar(): void
    {
        session()->forget(self::CLAVE_SESION);
    }
}
