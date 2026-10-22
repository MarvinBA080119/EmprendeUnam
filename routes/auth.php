<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RecuperarClaveController;
use App\Http\Controllers\Auth\RegistroAutorController;
use Illuminate\Support\Facades\Route;

// El login no lleva «guest»: con una sesión abierta el formulario sigue
// disponible (avisa con qué cuenta) y, al entrar con otras credenciales, la
// sesión anterior se cierra por completo antes de abrir la nueva.
Route::get('/iniciar-sesion', [LoginController::class, 'create'])->name('login');
Route::post('/iniciar-sesion', [LoginController::class, 'store'])->name('login.store');

Route::middleware('guest')->group(function () {
    Route::get('/registro', [RegistroAutorController::class, 'create'])->name('registro');
    Route::post('/registro', [RegistroAutorController::class, 'store'])
        ->middleware('throttle:registro')
        ->name('registro.store');

    Route::get('/recuperar-clave', [RecuperarClaveController::class, 'solicitar'])->name('password.request');
    Route::post('/recuperar-clave', [RecuperarClaveController::class, 'enviar'])
        ->middleware('throttle:recuperar-clave')
        ->name('password.email');
    Route::get('/restablecer-clave/{token}', [RecuperarClaveController::class, 'editar'])->name('password.reset');
    Route::post('/restablecer-clave', [RecuperarClaveController::class, 'actualizar'])->name('password.update');
});

// Sin «auth»: si la sesión ya había terminado, igual se llega al login.
Route::post('/cerrar-sesion', [LoginController::class, 'destroy'])->name('logout');

// Lo llama resources/js/sesion.js mientras hay actividad en la página, para que
// la sesión no venza mientras alguien llena un formulario largo.
Route::post('/sesion/mantener', [LoginController::class, 'mantener'])
    ->middleware(['auth', 'throttle:30,1'])
    ->name('sesion.mantener');
