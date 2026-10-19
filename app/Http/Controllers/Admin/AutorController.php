<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FiltroAutoresRequest;
use App\Models\Usuario;
use App\Services\RegistroAutorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AutorController extends Controller
{
    public function __construct(private RegistroAutorService $registro) {}

    /** Página con la tabla Vue (los datos llegan por datos()). */
    public function index(FiltroAutoresRequest $request): View
    {
        return view('admin.autores.index', ['filtros' => $request->safe()->except('page')]);
    }

    public function datos(FiltroAutoresRequest $request): JsonResponse
    {
        $filtros = $request->validated();
        $autores = $this->registro->listar($filtros['estatus'] ?? null, $filtros['q'] ?? null, config('emprende.por_pagina'));

        return $this->jsonPaginado($autores, fn (Usuario $u) => [
            'id' => $u->usua_id_usuario,
            'nombre' => $u->persona->nombre_completo,
            'usuario' => $u->usua_nombre_usuario,
            'correo' => $u->persona->correo?->comu_valor_comunicacion,
            'institucion' => $u->persona->autor?->auto_institucion,
            'grado' => $u->persona->autor?->auto_grado_academico,
            'registro' => $u->usua_fecha_registro->format('d/m/Y'),
            'activo' => $u->usua_activo,
            'urlActivar' => route('admin.autores.activar', $u),
        ]);
    }

    public function activar(Request $request, Usuario $usuario): JsonResponse|RedirectResponse
    {
        abort_unless($this->registro->esAutor($usuario), 404);

        $activada = $this->registro->activar($usuario, $request->user());
        $mensaje = $activada
            ? "Se activó la cuenta de {$usuario->usua_nombre_usuario} y se le avisó por correo."
            : "La cuenta de {$usuario->usua_nombre_usuario} ya estaba activa.";

        if ($request->expectsJson()) {
            return response()->json(['mensaje' => $mensaje, 'activo' => true]);
        }

        return back()->with($activada ? 'exito' : 'aviso', $mensaje);
    }
}
