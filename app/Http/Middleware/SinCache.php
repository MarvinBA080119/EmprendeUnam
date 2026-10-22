<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Prohíbe guardar en caché las páginas con sesión iniciada y las de acceso
 * (iniciar sesión, registro, contraseña). Así, al cerrar sesión, el botón
 * «Atrás» del navegador vuelve a pedir la página al servidor (que manda al
 * login) en lugar de mostrar una copia con los datos de la cuenta, y el
 * formulario de acceso nunca se muestra con un token CSRF vencido.
 */
class SinCache
{
    /** Rutas de acceso que nunca se guardan aunque no haya sesión. */
    private const RUTAS_ACCESO = ['login', 'login.store', 'logout', 'registro', 'registro.store', 'password.*'];

    public function handle(Request $request, Closure $next): Response
    {
        $respuesta = $next($request);

        if (Auth::check() || $request->routeIs(...self::RUTAS_ACCESO)) {
            $respuesta->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, private');
            $respuesta->headers->set('Pragma', 'no-cache');
            $respuesta->headers->set('Expires', '0');
        }

        return $respuesta;
    }
}
