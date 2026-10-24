<?php

namespace App\Http\Controllers\Diseno;

use App\Enums\EstatusArticulo;
use App\Http\Controllers\Controller;
use App\Models\Articulo;
use App\Services\DisenoService;
use App\Support\Manuscrito;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Mesa de diseño: artículos aprobados, su texto original y su PDF diseñado. */
class MesaController extends Controller
{
    public function __construct(private DisenoService $diseno) {}

    public function index(Request $request): View
    {
        $conteos = $this->diseno->conteos();
        $filtro = (string) $request->query('ver', $conteos['pendientes'] > 0 ? 'pendientes' : 'todos');
        $filtro = array_key_exists($filtro, DisenoService::FILTROS) ? $filtro : 'pendientes';

        return view('diseno.panel', [
            'articulos' => $this->diseno->articulos($filtro),
            'conteos' => $conteos,
            'filtro' => $filtro,
        ]);
    }

    /** Texto original tal como lo envió el autor (Word o PDF). */
    public function original(Articulo $articulo): StreamedResponse
    {
        $this->exigirAprobado($articulo);
        abort_unless(Storage::disk('local')->exists($articulo->arti_url_pdf), 404);

        return Storage::disk('local')->download($articulo->arti_url_pdf, DisenoService::nombreOriginal($articulo), [
            'Content-Type' => Manuscrito::tipoContenido($articulo->arti_url_pdf),
        ]);
    }

    /** PDF diseñado, para revisarlo en el navegador. */
    public function diseno(Articulo $articulo): StreamedResponse
    {
        $this->exigirAprobado($articulo);
        abort_unless($articulo->arti_url_pdf_diseno && Storage::disk('local')->exists($articulo->arti_url_pdf_diseno), 404);

        return Storage::disk('local')->response($articulo->arti_url_pdf_diseno, DisenoService::nombreDiseno($articulo), [
            'Content-Type' => 'application/pdf',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /** Todos los originales que faltan por diseñar, en un .zip. */
    public function originales(): BinaryFileResponse|RedirectResponse
    {
        $ruta = $this->diseno->zip($this->diseno->articulos('pendientes'), 'originales');
        if ($ruta === null) {
            return back()->with('aviso', 'No hay textos pendientes de diseñar.');
        }

        return response()->download($ruta, 'por-disenar-'.now()->format('Y-m-d').'.zip', [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend();
    }

    /**
     * Sube el PDF diseñado. Desde la mesa la subida va por JavaScript (con
     * barra de progreso, encabezado X-Subida-Mesa) y recibe JSON; sin
     * JavaScript es un formulario normal que regresa a la lista.
     */
    public function subir(Request $request, Articulo $articulo): RedirectResponse|JsonResponse
    {
        $this->exigirAprobado($articulo);
        $porScript = $request->hasHeader('X-Subida-Mesa');
        $volver = route('diseno.panel', ['ver' => $request->input('ver', 'pendientes')]);
        $mb = round(config('emprende.pdf_diseno_max_kb') / 1024);

        $fallo = fn (string $mensaje) => $porScript
            ? response()->json(['mensaje' => $mensaje], 422)
            : redirect()->to($volver)->with('error', $mensaje);

        $validacion = Validator::make($request->all(), [
            'pdf' => ['required', 'file', 'mimetypes:application/pdf', 'max:'.config('emprende.pdf_diseno_max_kb')],
        ], [
            'pdf.required' => 'Elige el PDF diseñado.',
            'pdf.file' => "No se pudo subir el PDF. Revisa que no pese más de {$mb} MB.",
            'pdf.uploaded' => "No se pudo subir el PDF. Revisa que no pese más de {$mb} MB.",
            'pdf.mimetypes' => 'El archivo debe ser un PDF.',
            'pdf.max' => "El PDF no debe pesar más de {$mb} MB.",
        ]);

        if ($validacion->fails()) {
            return $fallo($validacion->errors()->first('pdf'));
        }

        $reemplazo = $articulo->tieneDiseno();

        try {
            $this->diseno->subir($articulo, $request->file('pdf'), $request->user());
        } catch (ValidationException $e) {
            return $fallo((string) collect($e->errors())->flatten()->first());
        }

        // El aviso y el resaltado viajan en la sesión hasta la lista.
        session()->flash('exito', ($reemplazo ? 'Se reemplazó el PDF de «' : 'Se subió el PDF de «').$articulo->arti_titulo.'». Avisamos a la dirección editorial.');
        session()->flash('recien', $articulo->arti_id_autor);

        return $porScript ? response()->json(['url' => $volver]) : redirect()->to($volver);
    }

    private function exigirAprobado(Articulo $articulo): void
    {
        abort_unless($articulo->arti_status === EstatusArticulo::APROBADO, 404);
    }
}
