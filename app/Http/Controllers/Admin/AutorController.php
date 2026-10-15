<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FiltroAutoresRequest;
use App\Models\Usuario;
use App\Services\RegistroAutorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AutorController extends Controller
{
    public function __construct(private RegistroAutorService $registro) {}

    public function index(FiltroAutoresRequest $request): View
    {
        $filtros = $request->validated();

        return view('admin.autores.index', [
            'autores' => $this->registro->listar(
                $filtros['estatus'] ?? null,
                $filtros['texto'] ?? null,
                config('emprende.por_pagina'),
            ),
            'filtros' => $filtros,
        ]);
    }

    public function activar(Request $request, Usuario $usuario): RedirectResponse
    {
        abort_unless($this->registro->esAutor($usuario), 404);

        $activada = $this->registro->activar($usuario, $request->user());

        return back()->with(
            $activada ? 'exito' : 'aviso',
            $activada
                ? "Se activó la cuenta de {$usuario->usua_nombre_usuario} y se le avisó por correo."
                : "La cuenta de {$usuario->usua_nombre_usuario} ya estaba activa.",
        );
    }
}
