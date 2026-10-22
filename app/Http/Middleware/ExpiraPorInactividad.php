<?php

namespace App\Http\Middleware;

use App\Services\AccesoService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cierra la sesión que pasó más de session.inactividad minutos (30 por
 * defecto) sin peticiones y manda al login con el aviso. El contador vive en
 * la sesión (ultima_actividad) y se renueva con cada petición; en el
 * navegador, resources/js/sesion.js avisa antes y mantiene viva la sesión
 * mientras haya actividad aunque la página no se recargue.
 */
class ExpiraPorInactividad
{
    public const CLAVE_SESION = 'ultima_actividad';

    public const MENSAJE = 'Tu sesión expiró por inactividad. Vuelve a iniciar sesión.';

    public function __construct(private AccesoService $acceso) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $sesion = $request->session();
        $ultima = $sesion->get(self::CLAVE_SESION);

        if (is_int($ultima) && now()->getTimestamp() - $ultima > self::segundos()) {
            $this->acceso->cerrarSesion();
            $sesion->invalidate();
            $sesion->regenerateToken();

            // En una página pública se sigue como visitante; solo las rutas que
            // piden sesión mandan al login con el aviso.
            if (in_array('auth', $request->route()?->gatherMiddleware() ?? [], true)) {
                return $request->expectsJson()
                    ? response()->json(['message' => self::MENSAJE], 401)
                    : redirect()->route('login')->with('aviso', self::MENSAJE);
            }

            return $next($request);
        }

        $sesion->put(self::CLAVE_SESION, now()->getTimestamp());

        return $next($request);
    }

    public static function segundos(): int
    {
        return max(1, (int) config('session.inactividad', 30)) * 60;
    }
}
