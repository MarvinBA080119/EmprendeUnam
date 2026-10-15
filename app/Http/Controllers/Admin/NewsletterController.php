<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AvisarEdicionRequest;
use App\Http\Requests\Admin\FiltroSuscriptoresRequest;
use App\Models\Persona;
use App\Services\NewsletterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NewsletterController extends Controller
{
    public function __construct(private NewsletterService $newsletter) {}

    /** Lista de suscriptores con la tabla Vue (los datos llegan por datos()). */
    public function index(FiltroSuscriptoresRequest $request): View
    {
        return view('admin.newsletter.index', [
            'filtros' => $request->safe()->except('page'),
            'estados' => NewsletterService::ESTADOS,
            'activos' => Persona::suscriptoresActivos()->count(),
        ]);
    }

    public function datos(FiltroSuscriptoresRequest $request): JsonResponse
    {
        $filtros = $request->validated();
        $suscriptores = $this->newsletter->suscriptores($filtros['estado'] ?? null, $filtros['q'] ?? null, config('emprende.por_pagina'));

        return $this->jsonPaginado($suscriptores, function (Persona $persona) {
            $fila = $this->newsletter->fila($persona);

            return ['id' => $persona->pers_id_persona] + $fila + [
                'estadoEtiqueta' => NewsletterService::ESTADOS[$fila['estado']],
                'estadoClase' => match ($fila['estado']) {
                    'activo' => 'emp-insignia-aprobado',
                    'baja' => 'emp-insignia-rechazado',
                    default => 'emp-insignia-revision',
                },
            ];
        });
    }

    /** Encola un aviso por suscriptor activo con su enlace de baja. */
    public function avisar(AvisarEdicionRequest $request): RedirectResponse
    {
        $avisos = $this->newsletter->avisarNuevaEdicion(
            $request->validated('titulo'),
            $request->validated('url'),
            $request->user(),
        );

        return redirect()->route('admin.newsletter.index')->with(
            $avisos > 0 ? 'exito' : 'aviso',
            $avisos > 0
                ? "Se encolaron {$avisos} avisos de «{$request->validated('titulo')}». Los envíos fallidos quedan registrados en failed_jobs."
                : 'No hay suscriptores activos: no se envió ningún aviso.',
        );
    }

    public function exportar(FiltroSuscriptoresRequest $request): StreamedResponse
    {
        $filtros = $request->validated();

        return $this->newsletter->exportarCsv($filtros['estado'] ?? null, $filtros['q'] ?? null);
    }
}
