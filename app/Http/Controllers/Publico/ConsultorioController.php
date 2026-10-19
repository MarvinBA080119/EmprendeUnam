<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Http\Requests\Publico\EnviarMensajeRequest;
use App\Services\ConsultorioService;
use App\Services\SitioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ConsultorioController extends Controller
{
    public function __construct(
        private ConsultorioService $consultorio,
        private SitioService $sitio,
    ) {}

    /** Consultas contestadas (nunca muestra correos) y formulario. */
    public function index(): View
    {
        return view('sitio.consultorio', [
            'consultas' => $this->consultorio->publicadas(),
            'config' => $this->sitio->general()['config'],
        ]);
    }

    public function store(EnviarMensajeRequest $request): RedirectResponse
    {
        // Un robot que llenó el campo trampa recibe la misma respuesta, sin guardar nada.
        if (! $request->esRobot()) {
            $this->consultorio->enviar($request->validated(), (string) $request->ip());
        }

        return redirect()->to(route('consultorio').'#enviar')->with(
            'exito',
            "Consulta recibida: «{$request->validated('asunto')}». El comité editorial responderá en un plazo no mayor a cinco días hábiles.",
        );
    }
}
