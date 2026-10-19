<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegistroAutorRequest;
use App\Services\RegistroAutorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegistroAutorController extends Controller
{
    public function __construct(private RegistroAutorService $registro) {}

    public function create(): View
    {
        return view('auth.registro');
    }

    public function store(RegistroAutorRequest $request): RedirectResponse
    {
        $this->registro->registrar($request->validated());

        return redirect()->route('login')
            ->with('exito', 'Tu registro se recibió; el administrador activará tu cuenta.');
    }
}
