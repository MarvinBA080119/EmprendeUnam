<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FiltroArticulosRequest;
use App\Http\Requests\Admin\RechazarArticuloRequest;
use App\Models\Articulo;
use App\Services\ArticuloService;
use App\Services\ConstanciaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ArticuloController extends Controller
{
    public function __construct(
        private ArticuloService $articulos,
        private ConstanciaService $constancias,
    ) {}

    public function index(FiltroArticulosRequest $request): View
    {
        $estatus = $request->validated('estatus');

        return view('admin.articulos.index', [
            'articulos' => $this->articulos->listarTodos($estatus, config('emprende.por_pagina')),
            'estatus' => $estatus,
        ]);
    }

    public function show(Articulo $articulo): View
    {
        return view('admin.articulos.show', ['articulo' => $this->articulos->detalle($articulo)]);
    }

    public function pdf(Articulo $articulo): StreamedResponse
    {
        abort_unless(Storage::disk('local')->exists($articulo->arti_url_pdf), 404, 'El archivo no está disponible.');

        return Storage::disk('local')->download(
            $articulo->arti_url_pdf,
            "articulo-{$articulo->arti_id_autor}.pdf",
            ['Content-Type' => 'application/pdf'],
        );
    }

    public function aprobar(Request $request, Articulo $articulo): RedirectResponse
    {
        $constancia = $this->constancias->aprobar($articulo, $request->user());

        return redirect()->route('admin.articulos.show', $articulo)
            ->with('exito', "Artículo aprobado. Se generó la constancia con folio {$constancia->cons_folio} y se avisó al autor.");
    }

    public function rechazar(RechazarArticuloRequest $request, Articulo $articulo): RedirectResponse
    {
        $this->articulos->rechazar($articulo, $request->validated('comentario'), $request->user());

        return redirect()->route('admin.articulos.show', $articulo)
            ->with('exito', 'Artículo rechazado. Se envió el motivo al autor.');
    }
}
