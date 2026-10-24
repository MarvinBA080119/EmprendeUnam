<?php

namespace App\Http\Middleware;

use App\Models\Usuario;
use App\Services\AccesoService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cada cuenta entra solo a su panel: el administrador no termina en el panel
 * de autor (aunque tenga todos los privilegios) ni el autor en el de diseño.
 * Quien abre (GET) una página de un panel ajeno regresa al suyo con un aviso
 * que dice con qué cuenta está y cómo cambiar de cuenta; una acción (POST,
 * PUT, DELETE) o una petición JSON a un panel ajeno recibe 403.
 *
 * Uso: ->middleware('panel:autor')
 */
class PanelDelRol
{
    public function __construct(private AccesoService $acceso) {}

    public function handle(Request $request, Closure $next, string $panel): Response
    {
        $usuario = $request->user();

        if (! $usuario instanceof Usuario) {
            return redirect()->guest(route('login'));
        }

        $propio = $this->acceso->panelDe($usuario);

        if ($propio === $panel) {
            return $next($request);
        }

        if ($propio === null || $request->expectsJson() || ! $request->isMethod('GET')) {
            abort(403, 'No tienes permiso para entrar a esta sección.');
        }

        return redirect()->to($this->acceso->rutaInicio($usuario))->with('aviso', sprintf(
            'Tu sesión es de la cuenta %s, del panel de %s. Esa página es del panel de %s: para abrirla, cierra sesión y entra con la cuenta que corresponde.',
            $usuario->usua_nombre_usuario,
            AccesoService::PANELES[$propio],
            AccesoService::PANELES[$panel] ?? $panel,
        ));
    }
}
