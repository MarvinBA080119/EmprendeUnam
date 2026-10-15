<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FiltroSuscriptoresRequest;
use App\Models\Persona;
use App\Services\NewsletterService;
use Illuminate\Http\JsonResponse;
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

    public function exportar(FiltroSuscriptoresRequest $request): StreamedResponse
    {
        $filtros = $request->validated();

        return $this->newsletter->exportarCsv($filtros['estado'] ?? null, $filtros['q'] ?? null);
    }
}
