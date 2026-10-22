<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ArticuloNumeroRequest;
use App\Models\ArticuloRevista;
use App\Models\Curso;
use App\Models\Revista;
use App\Services\EdicionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Artículos de un número: datos, PDF, imagen y cursos que se muestran con él. */
class ArticuloNumeroController extends Controller
{
    public function __construct(private EdicionService $ediciones) {}

    public function create(Revista $revista): View
    {
        return $this->formulario($revista, new ArticuloRevista);
    }

    public function store(ArticuloNumeroRequest $request, Revista $revista): RedirectResponse
    {
        $entrada = $this->ediciones->guardarArticulo(
            $revista, $request->validated(), $request->user(),
            null, $request->file('pdf'), $request->file('imagen'),
        );

        return redirect()->route('admin.ediciones.show', $revista)->with('exito', "Se agregó «{$entrada->arre_titulo}» al número.");
    }

    public function edit(Revista $revista, ArticuloRevista $entrada): View
    {
        $this->delNumero($revista, $entrada);

        return $this->formulario($revista, $entrada);
    }

    public function update(ArticuloNumeroRequest $request, Revista $revista, ArticuloRevista $entrada): RedirectResponse
    {
        $this->delNumero($revista, $entrada);

        $this->ediciones->guardarArticulo(
            $revista, $request->validated(), $request->user(),
            $entrada, $request->file('pdf'), $request->file('imagen'),
        );

        return redirect()->route('admin.ediciones.show', $revista)->with('exito', "Se guardaron los cambios de «{$entrada->arre_titulo}».");
    }

    public function destroy(Request $request, Revista $revista, ArticuloRevista $entrada): RedirectResponse
    {
        $this->delNumero($revista, $entrada);
        $titulo = $entrada->arre_titulo;
        $this->ediciones->eliminarArticulo($entrada, $request->user());

        return redirect()->route('admin.ediciones.show', $revista)->with('exito', "Se quitó «{$titulo}» del número.");
    }

    private function formulario(Revista $revista, ArticuloRevista $entrada): View
    {
        $entrada->loadMissing(['articulo', 'cursos']);
        $sugeridos = $entrada->exists ? $this->ediciones->sugeridos($entrada) : collect();
        $marcados = $entrada->exists ? $this->ediciones->cursosDe($entrada)->pluck('curs_id_curso')->all() : [];

        return view('admin.ediciones.articulo', [
            'revista' => $revista,
            'entrada' => $entrada,
            'aprobados' => $this->ediciones->aprobadosDisponibles($revista, $entrada),
            'cursos' => Curso::vigentes()->orderBy('curs_nombre_curso')->get(),
            'sugeridos' => $sugeridos->pluck('curs_id_curso')->all(),
            'marcados' => array_map('intval', old('cursos', $marcados)),
        ]);
    }

    private function delNumero(Revista $revista, ArticuloRevista $entrada): void
    {
        abort_unless($entrada->revi_id_revista === $revista->revi_id_revista, 404);
    }
}
