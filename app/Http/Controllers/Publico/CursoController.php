<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use Illuminate\View\View;

class CursoController extends Controller
{
    /** Cursos con fecha de hoy en adelante (6.7). */
    public function index(): View
    {
        return view('sitio.cursos', [
            'cursos' => Curso::vigentes()
                ->with('palabras')
                ->orderBy('curs_fecha')
                ->orderBy('curs_nombre_curso')
                ->paginate(config('emprende.por_pagina')),
        ]);
    }
}
