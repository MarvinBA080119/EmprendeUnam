<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PanelAdminService;
use Illuminate\View\View;

class PanelController extends Controller
{
    public function __construct(private PanelAdminService $panel) {}

    public function index(): View
    {
        return view('admin.panel', ['contadores' => $this->panel->contadores()]);
    }
}
