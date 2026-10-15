<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AccesoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(private AccesoService $acceso) {}

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $usuario = $this->acceso->iniciarSesion(
            $request->string('acceso')->toString(),
            $request->string('clave')->toString(),
            (string) $request->ip(),
        );

        $request->session()->regenerate();

        return redirect()->intended($this->acceso->rutaInicio($usuario));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $this->acceso->cerrarSesion();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('inicio')->with('exito', 'Cerraste tu sesión.');
    }
}
