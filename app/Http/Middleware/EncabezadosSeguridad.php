<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Encabezados de seguridad en todas las respuestas web: impiden que otro sitio
 * muestre las páginas en un iframe (clickjacking de los formularios del
 * administrador), que el navegador adivine tipos de archivo y que la URL
 * completa (con tokens) viaje como Referer a otros sitios. Con HTTPS agrega
 * HSTS.
 */
class EncabezadosSeguridad
{
    public function handle(Request $request, Closure $next): Response
    {
        $respuesta = $next($request);

        $encabezados = [
            'X-Frame-Options' => 'SAMEORIGIN',
            'Content-Security-Policy' => "frame-ancestors 'self'",
            'X-Content-Type-Options' => 'nosniff',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
        ];

        // Con HTTPS, el navegador ya no intentará conectarse por HTTP durante un año.
        if ($request->isSecure()) {
            $encabezados['Strict-Transport-Security'] = 'max-age=31536000';
        }

        foreach ($encabezados as $nombre => $valor) {
            if (! $respuesta->headers->has($nombre)) {
                $respuesta->headers->set($nombre, $valor);
            }
        }

        return $respuesta;
    }
}
