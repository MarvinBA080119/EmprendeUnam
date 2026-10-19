<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Services\ConstanciaService;
use Illuminate\View\View;

class VerificarConstanciaController extends Controller
{
    public function __construct(private ConstanciaService $constancias) {}

    /** Muestra si el folio existe; nunca entrega el PDF. */
    public function show(string $folio): View
    {
        return view('publico.verificar_constancia', [
            'folio' => $folio,
            'constancia' => $this->constancias->buscarPorFolio($folio),
        ]);
    }
}
