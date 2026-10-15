<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CursoRequest;
use App\Models\Curso;
use App\Services\CursoService;
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

    public function store(CursoRequest $request): RedirectResponse
    {
        $curso = $this->cursos->crear($request->validated());

        return redirect()->route('admin.cursos.index')->with('exito', "Se publicó el curso «{$curso->curs_nombre_curso}».");
    }

    public function edit(Curso $curso): View
    {
        return view('admin.cursos.formulario', ['curso' => $curso->load('palabras')]);
    }

    public function update(CursoRequest $request, Curso $curso): RedirectResponse
    {
        $this->cursos->actualizar($curso, $request->validated());

        return redirect()->route('admin.cursos.index')->with('exito', "Se guardaron los cambios del curso «{$curso->curs_nombre_curso}».");
    }

    public function destroy(Curso $curso): RedirectResponse
    {
        $nombre = $curso->curs_nombre_curso;
        $this->cursos->eliminar($curso);

        return redirect()->route('admin.cursos.index')->with('exito', "Se eliminó el curso «{$nombre}».");
    }
}
