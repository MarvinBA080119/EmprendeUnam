<?php

namespace App\Http\Controllers\Autor;

use App\Enums\EstatusArticulo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Autor\EnviarArticuloRequest;
use App\Models\Articulo;
use App\Models\Tema;
use App\Services\ArticuloService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Envío y consulta de artículos del autor. Un artículo enviado no se edita
 * ni se reenvía: no existen rutas para eso.
 */
class ArticuloController extends Controller
{
    public function __construct(private ArticuloService $articulos) {}

    public function create(): View
    {
        return view('autor.articulos.crear', [
            'temas' => Tema::orderBy('tema_nombre')->get(['tema_id_tema', 'tema_nombre']),
        ]);
    }

    public function store(EnviarArticuloRequest $request): RedirectResponse
    {
        $articulo = $this->articulos->enviar(
            $request->user(), $request->validated(), $request->file('pdf'), $request->file('carta_cesion'),
        );

        return redirect()->route('autor.panel')->with(
            'exito',
            "Recibimos tu artículo «{$articulo->arti_titulo}». Quedó en revisión; te avisaremos por correo cuando haya un dictamen.",
        );
    }

    public function pdf(Articulo $articulo): StreamedResponse
    {
        Gate::authorize('descargar', $articulo);

        return $this->descargar($articulo->arti_url_pdf, "articulo-{$articulo->arti_id_autor}.pdf");
    }

    /** Plantilla en Word de la carta de cesión de derechos, para llenarla y firmarla. */
    public function cartaCesion(): BinaryFileResponse
    {
        return response()->download(
            resource_path('plantillas/carta_cesion_derechos.docx'),
            'Carta de cesion de derechos - EmprendeUNAM.docx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        );
    }

    /** Pantalla de agradecimiento de un artículo aprobado. */
    public function aprobado(Articulo $articulo): View
    {
        Gate::authorize('ver', $articulo);
        abort_unless($articulo->arti_status === EstatusArticulo::APROBADO, 404);

        return view('autor.articulos.aprobado', ['articulo' => $articulo->load('constancia')]);
    }

    private function descargar(string $ruta, string $nombre): StreamedResponse
    {
        abort_unless(Storage::disk('local')->exists($ruta), 404, 'El archivo no está disponible.');

        return Storage::disk('local')->download($ruta, $nombre, ['Content-Type' => 'application/pdf']);
    }
}
