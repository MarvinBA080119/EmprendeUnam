<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

abstract class Controller
{
    /**
     * Respuesta JSON de una lista paginada para las tablas Vue del administrador:
     * { datos: [...], meta: { pagina, ultima, total, desde, hasta } }.
     */
    protected function jsonPaginado(LengthAwarePaginator $paginador, callable $fila): JsonResponse
    {
        return response()->json([
            'datos' => collect($paginador->items())->map($fila)->values(),
            'meta' => [
                'pagina' => $paginador->currentPage(),
                'ultima' => $paginador->lastPage(),
                'total' => $paginador->total(),
                'desde' => $paginador->firstItem(),
                'hasta' => $paginador->lastItem(),
            ],
        ]);
    }
}
