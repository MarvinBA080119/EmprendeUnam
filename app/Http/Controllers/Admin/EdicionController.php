<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NumeroRequest;
use App\Models\Revista;
use App\Services\EdicionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Gestor de portada: números de la revista y los 5 artículos del índice. */
class EdicionController extends Controller
{
    public function __construct(private EdicionService $ediciones) {}

    public function index(): View
    {
        return view('admin.ediciones.index', ['numeros' => $this->ediciones->numeros()]);
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

        return redirect()->route('admin.ediciones.show', $revista)
            ->with('exito', "Se registró el número {$revista->revi_num_edicion}. Agrega sus artículos y elige los 5 del índice.");
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

        return redirect()->route('admin.ediciones.show', $revista)->with('exito', 'Se guardaron los cambios del número.');
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

    public function indice(Request $request, Revista $revista): RedirectResponse
    {
        $request->validate(['posiciones' => ['array'], 'posiciones.*' => ['nullable', 'integer', 'between:1,5']]);

        $this->ediciones->guardarIndice($revista, (array) $request->input('posiciones', []), $request->user());

        return back()->with('exito', 'Se guardó el índice: esos 5 artículos son los que se ven en el inicio.');
    }
}
