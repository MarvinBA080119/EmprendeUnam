<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ArticuloNumeroRequest;
use App\Models\Articulo;
use App\Models\ArticuloRevista;
use App\Models\Curso;
use App\Models\Revista;
use App\Services\EdicionService;
use Illuminate\Http\JsonResponse;
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

    /**
     * Cursos sugeridos en vivo mientras se llena el formulario: por el título
     * escrito y las palabras clave del artículo del sistema elegido.
     */
    public function sugerencias(Request $request, Revista $revista): JsonResponse
    {
        $datos = $request->validate([
            'titulo' => ['nullable', 'string', 'max:200'],
            'arti_id_autor' => ['nullable', 'integer'],
        ]);
        $articulo = ! empty($datos['arti_id_autor']) ? Articulo::aprobados()->with('palabras')->find($datos['arti_id_autor']) : null;
        $titulo = (string) (($datos['titulo'] ?? null) ?: $articulo?->arti_titulo);

        return response()->json($this->ediciones
            ->sugerirPara($titulo, $articulo?->palabras->pluck('pala_descripcion')->all() ?? [])
            ->map(fn (Curso $c) => ['id' => $c->curs_id_curso, 'nombre' => $c->curs_nombre_curso, 'institucion' => $c->curs_institucion])
            ->values());
    }

    private function formulario(Revista $revista, ArticuloRevista $entrada): View
    {
        $entrada->loadMissing(['articulo', 'cursos']);
        $sugeridos = $entrada->exists ? $this->ediciones->sugeridos($entrada) : collect();
        $marcados = $entrada->exists ? $this->ediciones->cursosDe($entrada)->pluck('curs_id_curso')->all() : [];

        $aprobados = $this->ediciones->aprobadosDisponibles($revista, $entrada);

        return view('admin.ediciones.articulo', [
            'revista' => $revista,
            'entrada' => $entrada,
            'aprobados' => $aprobados,
            'datosArticulos' => $this->ediciones->datosParaFormulario($aprobados),
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
