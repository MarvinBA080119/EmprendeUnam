<?php

use App\Http\Controllers\Publico\InicioController;
use App\Http\Controllers\Publico\VerificarConstanciaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [InicioController::class, 'index'])->name('inicio');

Route::get('/constancias/verificar/{folio}', [VerificarConstanciaController::class, 'show'])
    ->where('folio', '[A-Za-z0-9-]{1,30}')
    ->middleware('throttle:verificar-constancia')
    ->name('constancias.verificar');
