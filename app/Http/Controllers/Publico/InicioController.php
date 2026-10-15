<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Tema;
use Illuminate\View\View;

class InicioController extends Controller
{
    /**
     * Fase A: página de prueba que lista los temas desde la base de datos.
     * En la fase E se convierte en el inicio definitivo del sitio.
     */
    public function index(): View
    {
        $temas = Tema::query()
            ->orderBy('tema_nombre')
            ->get(['tema_id_tema', 'tema_nombre'])
            ->map(fn (Tema $tema) => ['id' => $tema->tema_id_tema, 'nombre' => $tema->tema_nombre]);

        return view('publico.inicio', ['temas' => $temas]);
    }
}
