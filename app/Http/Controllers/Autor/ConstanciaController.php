<?php

namespace App\Http\Controllers\Autor;

use App\Http\Controllers\Controller;
use App\Models\Articulo;
use App\Models\Constancia;
use App\Services\ConstanciaService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ConstanciaController extends Controller
{
    public function __construct(private ConstanciaService $constancias) {}

    /** Descarga la constancia del artículo (se puede repetir; RF-EST-04). */
    public function descargar(Articulo $articulo): StreamedResponse
    {
        $constancia = $articulo->constancia;
        abort_if($constancia === null, 404, 'Este artículo no tiene constancia.');

        return $this->entregar($constancia, "constancia-{$constancia->cons_folio}.pdf");
    }

    /**
     * Carta de agradecimiento: se genera la primera vez que se pide, cuando el
     * artículo ya salió en un número visible de la revista.
     */
    public function agradecimiento(Articulo $articulo): StreamedResponse
    {
        Gate::authorize('ver', $articulo);

        $carta = $this->constancias->agradecimiento($articulo);

        return $this->entregar($carta, "carta-agradecimiento-{$carta->cons_folio}.pdf");
    }

    private function entregar(Constancia $documento, string $nombre): StreamedResponse
    {
        Gate::authorize('descargar', $documento);
        abort_unless(Storage::disk('local')->exists($documento->cons_url_archivo), 404, 'El archivo no está disponible.');

        return Storage::disk('local')->download($documento->cons_url_archivo, $nombre, ['Content-Type' => 'application/pdf']);
    }
}
