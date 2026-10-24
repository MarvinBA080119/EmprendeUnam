<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CursoRequest;
use App\Models\Curso;
use App\Services\CursoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CursoController extends Controller
{
    public function __construct(private CursoService $cursos) {}

    public function index(Request $request): View
    {
        $vigencia = in_array($request->query('vigencia'), ['vigentes', 'pasados'], true) ? $request->query('vigencia') : null;

        return view('admin.cursos.index', [
            'cursos' => $this->cursos->listar($vigencia, config('emprende.por_pagina')),
            'vigencia' => $vigencia,
        ]);
    }

    public function create(): View
    {
        return view('admin.cursos.formulario', ['curso' => new Curso]);
    }

    /** Desde el formulario de un artículo del número llega por JavaScript y responde JSON. */
    public function store(CursoRequest $request): RedirectResponse|JsonResponse
    {
        $curso = $this->cursos->crear($request->validated(), $request->user());

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $curso->curs_id_curso,
                'nombre' => $curso->curs_nombre_curso,
                'institucion' => $curso->curs_institucion,
                'vigente' => $curso->curs_fecha->gte(today()),
            ], 201);
        }

        return redirect()->route('admin.cursos.index')->with('exito', "Se publicó el curso «{$curso->curs_nombre_curso}».");
    }

    public function edit(Curso $curso): View
    {
        return view('admin.cursos.formulario', ['curso' => $curso->load('palabras')]);
    }

    public function update(CursoRequest $request, Curso $curso): RedirectResponse
    {
        $this->cursos->actualizar($curso, $request->validated(), $request->user());

        return redirect()->route('admin.cursos.index')->with('exito', "Se guardaron los cambios del curso «{$curso->curs_nombre_curso}».");
    }

    public function destroy(Request $request, Curso $curso): RedirectResponse
    {
        $nombre = $curso->curs_nombre_curso;
        $this->cursos->eliminar($curso, $request->user());

        return redirect()->route('admin.cursos.index')->with('exito', "Se eliminó el curso «{$nombre}».");
    }
}
