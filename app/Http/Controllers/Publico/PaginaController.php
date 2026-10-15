<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Services\SitioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Páginas de contenido del sitio público (convertidas del sitio original).
 */
class PaginaController extends Controller
{
    public function __construct(private SitioService $sitio) {}

    public function acerca(): View
    {
        return view('sitio.acerca', $this->sitio->acerca());
    }

    public function historia(): View
    {
        return view('sitio.historia');
    }

    public function historico(): View
    {
        return view('sitio.historico', $this->sitio->historico());
    }

    public function tematicas(Request $request): View
    {
        return view('sitio.tematicas', $this->sitio->tematicas($request->query('cat')));
    }

    public function podcasts(): View
    {
        return view('sitio.podcasts', $this->sitio->podcasts());
    }

    public function webinar(): View
    {
        return view('sitio.webinar', $this->sitio->webinar());
    }

    public function diezDeDiez(): View
    {
        return view('sitio.diezdediez', ['areas' => $this->sitio->diezDeDiez()]);
    }

    public function diezDeDiezArea(string $tema): View|RedirectResponse
    {
        $coleccion = $this->sitio->diezDeDiezArea($tema);

        // Si el área no existe se vuelve al índice (como en el sitio original).
        if ($coleccion === null) {
            return redirect()->route('diezdediez');
        }

        return view('sitio.diezdediez_area', ['coleccion' => $coleccion]);
    }

    public function pymes(): View
    {
        return view('sitio.pyme_hablan', ['casos_pyme' => $this->sitio->pymes()]);
    }

    public function lineamientos(): View
    {
        return view('sitio.lineamientos', $this->sitio->lineamientos());
    }

    public function envioArticulos(): View
    {
        return view('sitio.envio_articulos');
    }
}
