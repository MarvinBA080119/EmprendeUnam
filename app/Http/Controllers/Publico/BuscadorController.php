<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Http\Requests\Publico\BuscarRequest;
use App\Models\Tema;
use App\Services\BuscadorService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class BuscadorController extends Controller
{
    public function __construct(private BuscadorService $buscador) {}

    /** Página con el buscador (isla Vue); los resultados llegan por resultados(). */
    public function index(BuscarRequest $request): View
    {
        return view('sitio.buscador', [
            'filtros' => $request->safe()->except('page'),
            'temas' => Tema::orderBy('tema_nombre')->get(['tema_id_tema', 'tema_nombre']),
            'anios' => $this->buscador->aniosDisponibles(),
        ]);
    }

    public function resultados(BuscarRequest $request): JsonResponse
    {
        $resultados = $this->buscador->buscar(
            $request->validated(),
            (int) $request->validated('page', 1),
            config('emprende.por_pagina'),
        );

        return $this->jsonPaginado($resultados, fn (array $fila) => $fila);
    }
}
