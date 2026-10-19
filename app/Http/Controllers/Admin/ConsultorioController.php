<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EstatusMensaje;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FiltroMensajesRequest;
use App\Http\Requests\Admin\ResponderMensajeRequest;
use App\Models\Mensaje;
use App\Services\ConsultorioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsultorioController extends Controller
{
    public function __construct(private ConsultorioService $consultorio) {}

    /** Bandeja con la tabla Vue (los datos llegan por datos()). */
    public function index(FiltroMensajesRequest $request): View
    {
        return view('admin.consultorio.index', ['filtros' => $request->safe()->except('page')]);
    }

    public function datos(FiltroMensajesRequest $request): JsonResponse
    {
        $filtros = $request->validated();
        $mensajes = $this->consultorio->bandeja($filtros['estatus'] ?? null, $filtros['q'] ?? null, config('emprende.por_pagina'));

        return $this->jsonPaginado($mensajes, fn (Mensaje $m) => [
            'id' => $m->mens_id_mensaje,
            'remitente' => $m->mens_remitente,
            'correo' => $m->mens_correo,
            'lugar' => $m->mens_lugar,
            'asunto' => $m->mens_asunto,
            'fecha' => $m->mens_fecha_envio->format('d/m/Y H:i'),
            'estatus' => ['etiqueta' => $m->mens_status->etiqueta(), 'clase' => $m->mens_status->claseInsignia()],
            'url' => route('admin.consultorio.show', $m),
        ]);
    }

    public function show(Mensaje $mensaje): View
    {
        abort_unless($mensaje->men_mens_id_mensaje === null, 404);

        return view('admin.consultorio.show', [
            'mensaje' => $mensaje->load('respuesta'),
            'pendiente' => $mensaje->mens_status === EstatusMensaje::PENDIENTE,
        ]);
    }

    public function responder(ResponderMensajeRequest $request, Mensaje $mensaje): RedirectResponse
    {
        $this->consultorio->responder($mensaje, $request->validated('respuesta'), $request->user());

        return redirect()->route('admin.consultorio.show', $mensaje)
            ->with('exito', 'Se envió la respuesta al lector y el mensaje quedó publicado en el consultorio.');
    }

    public function rechazar(Request $request, Mensaje $mensaje): RedirectResponse
    {
        $this->consultorio->rechazar($mensaje, $request->user());

        return redirect()->route('admin.consultorio.index')->with('exito', 'El mensaje se marcó como rechazado.');
    }
}
