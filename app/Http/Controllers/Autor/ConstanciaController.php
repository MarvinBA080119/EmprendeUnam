<?php

namespace App\Http\Controllers\Autor;

use App\Http\Controllers\Controller;
use App\Models\Articulo;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ConstanciaController extends Controller
{
    /** Descarga la constancia del artículo (se puede repetir; RF-EST-04). */
    public function descargar(Articulo $articulo): StreamedResponse
    {
        $constancia = $articulo->constancia;
        abort_if($constancia === null, 404, 'Este artículo no tiene constancia.');

        Gate::authorize('descargar', $constancia);
        abort_unless(Storage::disk('local')->exists($constancia->cons_url_archivo), 404, 'El archivo no está disponible.');

        return Storage::disk('local')->download(
            $constancia->cons_url_archivo,
            "constancia-{$constancia->cons_folio}.pdf",
            ['Content-Type' => 'application/pdf'],
        );
    }
}
