<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FiltroArticulosRequest;
use App\Http\Requests\Admin\RechazarArticuloRequest;
use App\Models\Articulo;
use App\Models\Tema;
use App\Services\ArticuloService;
use App\Services\ConstanciaService;
use Illuminate\Http\JsonResponse;
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

    /** Página con la tabla Vue (los datos llegan por datos()). */
    public function index(FiltroArticulosRequest $request): View
    {
        return view('admin.articulos.index', [
            'filtros' => $request->safe()->except('page'),
            'temas' => Tema::orderBy('tema_nombre')->get(['tema_id_tema', 'tema_nombre']),
        ]);
    }

    public function datos(FiltroArticulosRequest $request): JsonResponse
    {
        $articulos = $this->articulos->listarTodos($request->validated(), config('emprende.por_pagina'));

        return $this->jsonPaginado($articulos, fn (Articulo $a) => [
            'id' => $a->arti_id_autor,
            'titulo' => $a->arti_titulo,
            'autor' => $a->autores->first()?->persona?->nombre_completo,
            'tema' => $a->tema->tema_nombre,
            'fecha' => $a->arti_fecha_registro->format('d/m/Y'),
            'estatus' => ['etiqueta' => $a->arti_status->etiqueta(), 'clase' => $a->arti_status->claseInsignia()],
            'url' => route('admin.articulos.show', $a),
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

    /** Carta de cesión de derechos firmada que subió el autor. */
    public function cartaCesion(Articulo $articulo): StreamedResponse
    {
        $carta = $articulo->cartaCesion;
        abort_unless($carta && Storage::disk('local')->exists($carta->cons_url_archivo), 404, 'El artículo no tiene carta de cesión.');

        return Storage::disk('local')->download(
            $carta->cons_url_archivo,
            "carta-cesion-articulo-{$articulo->arti_id_autor}.pdf",
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
