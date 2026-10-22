<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NumeroRequest;
use App\Models\Revista;
use App\Services\EdicionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/** Gestor de portada: números de la revista y los 5 artículos del índice. */
class EdicionController extends Controller
{
    public function __construct(private EdicionService $ediciones) {}

    public function index(): View
    {
        $numeros = $this->ediciones->numeros();

        return view('admin.ediciones.index', [
            'principal' => $numeros->firstWhere('revi_en_portada', true),
            'otros' => $numeros->reject(fn ($n) => $n->revi_en_portada)->values(),
            'pendientesDeMaquetar' => $this->ediciones->aprobadosSinNumero()->count(),
        ]);
    }

    public function create(): View
    {
        return view('admin.ediciones.formulario', ['revista' => new Revista(['revi_anio' => (string) now()->year])]);
    }

    public function store(NumeroRequest $request): RedirectResponse
    {
        $revista = $this->ediciones->guardarNumero(
            $request->validated(), $request->user(), null, $request->file('portada'), $request->file('pdf'),
        );

        return $this->conZip($request, $revista, redirect()->route('admin.ediciones.show', $revista)
            ->with('exito', "Se registró el número {$revista->revi_num_edicion}. Agrega sus artículos y elige los 5 destacados."));
    }

    /** Artículos del número, posición en el índice y cursos. */
    public function show(Revista $revista): View
    {
        $articulos = $revista->articulos()
            ->with(['articulo', 'cursos'])
            ->orderByRaw('arre_posicion_index is null, arre_posicion_index, arre_fecha_index is not null, arre_id_articulo_revista')
            ->get();

        return view('admin.ediciones.show', [
            'revista' => $revista,
            'articulos' => $articulos,
            'pendientesDeMaquetar' => $this->ediciones->aprobadosSinNumero()->count(),
            'cursos' => $articulos->mapWithKeys(fn ($a) => [$a->arre_id_articulo_revista => $this->ediciones->cursosDe($a)]),
        ]);
    }

    public function edit(Revista $revista): View
    {
        return view('admin.ediciones.formulario', ['revista' => $revista]);
    }

    public function update(NumeroRequest $request, Revista $revista): RedirectResponse
    {
        $this->ediciones->guardarNumero(
            $request->validated(), $request->user(), $revista, $request->file('portada'), $request->file('pdf'),
        );

        return $this->conZip($request, $revista, redirect()->route('admin.ediciones.show', $revista)
            ->with('exito', 'Se guardaron los cambios del número.'));
    }

    public function destroy(Request $request, Revista $revista): RedirectResponse
    {
        $numero = $revista->revi_num_edicion;
        $this->ediciones->eliminarNumero($revista, $request->user());

        return redirect()->route('admin.ediciones.index')->with('exito', "Se eliminó el número {$numero}.");
    }

    public function portada(Request $request, Revista $revista): RedirectResponse
    {
        $this->ediciones->ponerEnPortada($revista, $request->user());

        return back()->with('exito', "El número {$revista->revi_num_edicion} ahora aparece en el inicio del sitio.");
    }

    /** Si el formulario del número trae el .zip de PDF, se carga en el mismo paso. */
    private function conZip(Request $request, Revista $revista, RedirectResponse $respuesta): RedirectResponse
    {
        if (! $request->hasFile('zip')) {
            return $respuesta;
        }

        return $respuesta->with('zip_resultado', $this->ediciones->cargarZip($revista, $request->file('zip'), $request->user()));
    }

    /** Manuscritos de los artículos aprobados sin número, para maquetarlos. */
    public function manuscritos(): BinaryFileResponse|RedirectResponse
    {
        $ruta = $this->ediciones->zipManuscritos();
        if ($ruta === null) {
            return back()->with('aviso', 'No hay artículos aprobados pendientes de publicar.');
        }

        return response()->download($ruta, 'manuscritos-aprobados-'.now()->format('Y-m-d').'.zip', [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend();
    }

    /** PDF maquetados de los artículos del número en un .zip. */
    public function zip(Request $request, Revista $revista): RedirectResponse
    {
        $request->validate(
            ['zip' => ['required', 'file', 'mimetypes:application/zip,application/x-zip-compressed', 'max:'.config('emprende.zip_max_kb')]],
            ['zip.required' => 'Elige el .zip con los PDF de los artículos.', 'zip.mimetypes' => 'El archivo debe ser un .zip.'],
        );

        $resultado = $this->ediciones->cargarZip($revista, $request->file('zip'), $request->user());
        $vinculados = count($resultado['vinculados']) + count($resultado['agregados']);

        return back()
            ->with($vinculados > 0 ? 'exito' : 'aviso', $vinculados > 0
                ? "Se vincularon {$vinculados} PDF con sus artículos."
                : 'No se pudo vincular ningún PDF. Revisa los nombres de los archivos.')
            ->with('zip_resultado', $resultado);
    }

    public function indice(Request $request, Revista $revista): RedirectResponse
    {
        $request->validate(['posiciones' => ['array'], 'posiciones.*' => ['nullable', 'integer', 'between:1,5']]);

        $this->ediciones->guardarIndice($revista, (array) $request->input('posiciones', []), $request->user());

        return back()->with('exito', 'Se guardó el índice: esos 5 artículos son los que se ven en el inicio.');
    }
}
