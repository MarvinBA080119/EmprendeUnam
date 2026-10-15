<?php

namespace App\Http\Controllers\Autor;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PanelController extends Controller
{
    /** El historial de artículos se agrega en la fase C. */
    public function index(): View
    {
        return view('autor.panel');
    }
}
