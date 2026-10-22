<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\ExpiraPorInactividad;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Usuario;
use App\Services\AccesoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(private AccesoService $acceso) {}

    public function create(Request $request): View
    {
        return view('auth.login', ['sesionAbierta' => $request->user()]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        // Quien envía el formulario con una sesión abierta quiere entrar con
        // otra cuenta: la anterior se cierra y sus datos se borran antes de
        // validar, para que nada de ella pase a la nueva.
        if ($request->user() !== null) {
            $this->acceso->cerrarSesion();
            $request->session()->invalidate();
        }

        $usuario = $this->acceso->iniciarSesion(
            $request->string('acceso')->toString(),
            $request->string('clave')->toString(),
            (string) $request->ip(),
        );

        $request->session()->regenerate();

        return redirect()->to($this->destino($request, $usuario));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $porInactividad = $request->input('motivo') === 'inactividad';

        $this->acceso->cerrarSesion();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $porInactividad
            ? redirect()->route('login')->with('aviso', ExpiraPorInactividad::MENSAJE)
            : redirect()->route('login')->with('exito', 'Cerraste tu sesión.');
    }

    /** La petición ya renovó la actividad (ExpiraPorInactividad); no hay nada más que hacer. */
    public function mantener(): Response
    {
        return response()->noContent();
    }

    /**
     * Panel del rol, o la página que se pidió antes de iniciar sesión si
     * pertenece a ese mismo panel: un autor nunca termina en una URL del
     * administrador guardada por otra cuenta, ni al revés.
     */
    private function destino(Request $request, Usuario $usuario): string
    {
        $inicio = $this->acceso->rutaInicio($usuario);
        $pedida = $request->session()->pull('url.intended');

        if (is_string($pedida) && ($pedida === $inicio || str_starts_with($pedida, $inicio.'/'))) {
            return $pedida;
        }

        return $inicio;
    }
}
