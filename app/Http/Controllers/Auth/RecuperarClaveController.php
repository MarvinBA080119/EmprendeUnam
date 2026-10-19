<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RestablecerClaveRequest;
use App\Http\Requests\Auth\SolicitarClaveRequest;
use App\Services\AccesoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class RecuperarClaveController extends Controller
{
    public function __construct(private AccesoService $acceso) {}

    public function solicitar(): View
    {
        return view('auth.recuperar_clave');
    }

    public function enviar(SolicitarClaveRequest $request): RedirectResponse
    {
        $this->acceso->enviarEnlaceRecuperacion($request->string('correo')->toString());

        // Misma respuesta exista o no el correo, para no revelar cuentas.
        return back()->with('exito', 'Si el correo está registrado, te enviamos un enlace para restablecer tu contraseña.');
    }

    public function editar(Request $request, string $token): View
    {
        return view('auth.restablecer_clave', [
            'token' => $token,
            'correo' => (string) $request->query('correo', ''),
        ]);
    }

    public function actualizar(RestablecerClaveRequest $request): RedirectResponse
    {
        $estado = $this->acceso->restablecerClave(
            $request->string('correo')->toString(),
            $request->string('token')->toString(),
            $request->string('clave')->toString(),
        );

        if ($estado !== Password::PASSWORD_RESET) {
            return back()->withInput($request->only('correo'))->withErrors(['correo' => __($estado)]);
        }

        return redirect()->route('login')->with('exito', __($estado));
    }
}
