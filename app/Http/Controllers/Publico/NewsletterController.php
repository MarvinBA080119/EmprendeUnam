<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Http\Requests\Publico\SuscribirNewsletterRequest;
use App\Services\NewsletterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NewsletterController extends Controller
{
    public function __construct(private NewsletterService $newsletter) {}

    /** Edición del newsletter (diseño propio, con su ventana de suscripción). */
    public function newsletter(): View
    {
        return view('sitio.newsletter');
    }

    public function suscripcion(): View
    {
        return view('sitio.suscripcion');
    }

    /** Responde JSON a la ventana del newsletter y redirige al formulario de suscripción. */
    public function suscribir(SuscribirNewsletterRequest $request): JsonResponse|RedirectResponse
    {
        $datos = $request->validated();
        $estado = $request->esRobot()
            ? NewsletterService::NUEVO   // al robot se le responde igual, sin guardar nada
            : $this->newsletter->suscribir($datos)['estado'];

        $mensaje = match ($estado) {
            NewsletterService::YA_SUSCRITO => 'Este correo ya está suscrito. Te seguiremos avisando de cada nueva edición.',
            NewsletterService::SIN_CONFIRMAR => "Te reenviamos el correo de confirmación a {$datos['correo']}.",
            default => "Te enviamos un correo a {$datos['correo']} para confirmar tu suscripción.",
        };

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'mensaje' => $mensaje, 'nombre' => $datos['nombre'], 'estado' => $estado]);
        }

        return redirect()->route('suscripcion')->with($estado === NewsletterService::YA_SUSCRITO ? 'aviso' : 'exito', $mensaje);
    }

    public function confirmar(string $token): View
    {
        $persona = $this->newsletter->porToken($token);

        if ($persona === null || ! $persona->pers_subs_newsletter || $persona->pers_fecha_baja !== null) {
            return view('sitio.newsletter_estado', ['estado' => 'invalido']);
        }

        if ($this->newsletter->confirmacionVencida($persona)) {
            return view('sitio.newsletter_estado', ['estado' => 'vencido', 'persona' => $persona]);
        }

        $confirmada = $this->newsletter->confirmar($persona);

        return view('sitio.newsletter_estado', [
            'estado' => $confirmada ? 'confirmada' : 'ya_confirmada',
            'persona' => $persona,
        ]);
    }

    /**
     * Abrir el enlace solo pide confirmación; la baja ocurre al pulsar el botón
     * (POST). Los filtros antispam visitan los enlaces y darían de baja sin querer.
     */
    public function baja(string $token): View
    {
        $persona = $this->newsletter->porToken($token);

        if ($persona === null) {
            return view('sitio.newsletter_estado', ['estado' => 'invalido']);
        }

        return view('sitio.newsletter_estado', [
            'estado' => ($persona->pers_subs_newsletter && $persona->pers_fecha_baja === null) ? 'confirmar_baja' : 'ya_baja',
            'persona' => $persona,
            'token' => $token,
        ]);
    }

    public function confirmarBaja(string $token): View
    {
        $persona = $this->newsletter->porToken($token);

        if ($persona === null) {
            return view('sitio.newsletter_estado', ['estado' => 'invalido']);
        }

        $this->newsletter->darDeBaja($persona);

        return view('sitio.newsletter_estado', ['estado' => 'baja', 'persona' => $persona]);
    }
}
