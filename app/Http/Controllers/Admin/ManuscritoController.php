<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Articulo;
use App\Services\DisenoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Manuscritos aprobados y su estado en la mesa de diseño: el administrador
 * descarga los originales y los PDF diseñados para armar el número.
 */
class ManuscritoController extends Controller
{
    public function __construct(private DisenoService $diseno) {}

    public function index(Request $request): View
    {
        $todos = $request->query('ver') === 'todos';

        return view('admin.ediciones.manuscritos', [
            'articulos' => $this->diseno->articulos('todos', soloSinNumero: ! $todos),
            'conteos' => $this->diseno->conteos(),
            'todos' => $todos,
        ]);
    }

    public function originales(Request $request): BinaryFileResponse|RedirectResponse
    {
        return $this->zip($request, 'originales', 'originales-aprobados');
    }

    public function disenos(Request $request): BinaryFileResponse|RedirectResponse
    {
        return $this->zip($request, 'disenos', 'pdf-disenados');
    }

    public function diseno(Articulo $articulo): StreamedResponse
    {
        abort_unless($articulo->arti_url_pdf_diseno && Storage::disk('local')->exists($articulo->arti_url_pdf_diseno), 404);

        return Storage::disk('local')->download($articulo->arti_url_pdf_diseno, DisenoService::nombreDiseno($articulo), [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /** @param  'originales'|'disenos'  $tipo */
    private function zip(Request $request, string $tipo, string $nombre): BinaryFileResponse|RedirectResponse
    {
        $articulos = $this->diseno->articulos($tipo === 'disenos' ? 'disenados' : 'todos', soloSinNumero: $request->query('ver') !== 'todos');
        $ruta = $this->diseno->zip($articulos, $tipo);

        if ($ruta === null) {
            return back()->with('aviso', $tipo === 'disenos' ? 'Todavía no hay PDF diseñados en esta lista.' : 'No hay manuscritos en esta lista.');
        }

        return response()->download($ruta, $nombre.'-'.now()->format('Y-m-d').'.zip', [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend();
    }
}
