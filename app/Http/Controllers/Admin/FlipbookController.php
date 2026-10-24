<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Revista;
use App\Services\FlipbookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/** Generador de flipbooks: subir el PDF de un número, seguir su avance y publicarlo. */
class FlipbookController extends Controller
{
    public function __construct(private FlipbookService $flipbooks) {}

    public function index(Request $request): View
    {
        return view('admin.flipbooks.index', [
            'flipbooks' => $this->flipbooks->listar(),
            'numeros' => Revista::orderByDesc('revi_fecha_publicacion')->get(['revi_id_revista', 'revi_num_edicion', 'revi_titulo', 'revi_url_flipbook']),
            'enCurso' => $request->query('numero'),
        ]);
    }

    /** Recibe el PDF y encola la conversión. Por JavaScript responde JSON. */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $mb = round(config('emprende.pdf_max_revista_kb') / 1024);
        $datos = $request->validate([
            'numero' => ['required', 'string', 'regex:/^[0-9]{1,4}$/'],
            'pdf' => ['required', 'file', 'mimetypes:application/pdf', 'max:'.config('emprende.pdf_max_revista_kb')],
        ], [
            'numero.required' => 'Escribe el número de edición.',
            'numero.regex' => 'El número de edición solo lleva dígitos (por ejemplo 220).',
            'pdf.required' => 'Elige el PDF completo de la revista.',
            'pdf.mimetypes' => 'El archivo debe ser un PDF.',
            'pdf.max' => "El PDF no debe pesar más de {$mb} MB.",
        ]);

        $estado = $this->flipbooks->iniciar($datos['numero'], $request->file('pdf'), $request->user());

        if ($request->wantsJson()) {
            return response()->json($this->conUrls($estado), 202);
        }

        return redirect()->route('admin.flipbooks.index', ['numero' => $datos['numero']])
            ->with('exito', "Se está generando el flipbook del número {$datos['numero']}.");
    }

    /** Avance de la conversión (lo consulta la barra de progreso). */
    public function estado(string $numero): JsonResponse
    {
        $estado = $this->flipbooks->estado($numero);
        abort_unless($estado !== null, 404);

        return response()->json($this->conUrls($estado));
    }

    /** Miniatura de una hoja ya convertida, mientras se arma o ya publicada. */
    public function miniatura(string $numero, int $pagina): BinaryFileResponse
    {
        $archivo = $this->flipbooks->archivo($numero, "img/thumbs/{$pagina}.jpg", true)
            ?? $this->flipbooks->archivo($numero, "img/thumbs/{$pagina}.jpg");
        abort_unless($archivo !== null, 404);

        return response()->file($archivo['ruta'], ['Content-Type' => 'image/jpeg', 'Cache-Control' => 'private, max-age=60']);
    }

    /** Usa el flipbook como eBook de un número del gestor. */
    public function usar(Request $request, string $numero): RedirectResponse
    {
        $revista = Revista::findOrFail($request->integer('revista'));
        abort_unless($this->flipbooks->manifiesto($numero) !== null, 404);

        $revista->forceFill(['revi_url_flipbook' => FlipbookService::ruta($numero)])->save();

        return back()->with('exito', "El flipbook {$numero} quedó como eBook del número {$revista->revi_num_edicion}.");
    }

    public function indice(string $numero): RedirectResponse
    {
        $this->flipbooks->actualizarIndice($numero);

        return back()->with('exito', "Se actualizó el índice del flipbook {$numero} con los artículos del gestor.");
    }

    public function destroy(string $numero): RedirectResponse
    {
        if ($this->flipbooks->manifiesto($numero) === null && $this->flipbooks->estado($numero) === null) {
            throw ValidationException::withMessages(['numero' => "No hay flipbook del número {$numero}."]);
        }
        $this->flipbooks->eliminar($numero);

        return redirect()->route('admin.flipbooks.index')->with('exito', "Se eliminó el flipbook del número {$numero}.");
    }

    /** @param  array<string, mixed>  $estado */
    private function conUrls(array $estado): array
    {
        $numero = (string) $estado['numero'];

        return $estado + [
            'url_estado' => route('admin.flipbooks.estado', $numero),
            'url_flipbook' => FlipbookService::ruta($numero),
            'url_miniatura' => route('admin.flipbooks.miniatura', [$numero, 0]),
        ];
    }
}
