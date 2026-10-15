<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PanelController extends Controller
{
    /** Los contadores del panel se agregan en la fase D. */
    public function index(): View
    {
        return view('admin.panel');
    }
}
