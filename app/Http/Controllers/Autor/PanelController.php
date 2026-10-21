<?php

namespace App\Http\Controllers\Autor;

use App\Http\Controllers\Controller;
use App\Services\ArticuloService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PanelController extends Controller
{
    public function __construct(private ArticuloService $articulos) {}

    /** Historial de los artículos del autor (6.5) y cuántos hay en cada estatus. */
    public function index(Request $request): View
    {
        return view('autor.panel', [
            'articulos' => $this->articulos->historialDe($request->user(), config('emprende.por_pagina')),
            'resumen' => $this->articulos->resumenDe($request->user()),
            'nombre' => $request->user()->loadMissing('persona')->persona?->pers_nombre,
        ]);
    }
}
