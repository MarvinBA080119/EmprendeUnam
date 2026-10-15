<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Services\SitioService;
use Illuminate\View\View;

class InicioController extends Controller
{
    public function __construct(private SitioService $sitio) {}

    /** Edición más reciente (index.php del sitio original). */
    public function index(): View
    {
        return view('sitio.inicio', $this->sitio->inicio());
    }
}
