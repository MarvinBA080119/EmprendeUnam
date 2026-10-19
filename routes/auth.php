<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RecuperarClaveController;
use App\Http\Controllers\Auth\RegistroAutorController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/iniciar-sesion', [LoginController::class, 'create'])->name('login');
    Route::post('/iniciar-sesion', [LoginController::class, 'store'])->name('login.store');

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

Route::post('/cerrar-sesion', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');
