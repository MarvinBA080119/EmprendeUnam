<?php

/*
| Módulo de diseño. Prefijo /diseno, nombre "diseno.", middleware web + auth +
| panel:diseno (registrado en bootstrap/app.php). La cuenta de diseño solo ve
| artículos aprobados: descarga el texto original y sube el PDF diseñado.
*/

use App\Http\Controllers\Diseno\MesaController;
use Illuminate\Support\Facades\Route;

Route::middleware('privilegio:DESCARGAR_ORIGINALES')->group(function () {
    Route::get('/', [MesaController::class, 'index'])->name('panel');
    Route::get('/originales', [MesaController::class, 'originales'])->name('originales');
    Route::get('/articulos/{articulo}/original', [MesaController::class, 'original'])->name('articulos.original');
    Route::get('/articulos/{articulo}/diseno', [MesaController::class, 'diseno'])->name('articulos.diseno');
});

Route::middleware('privilegio:SUBIR_DISENO')->group(function () {
    Route::post('/articulos/{articulo}/diseno', [MesaController::class, 'subir'])->name('articulos.subir');
});
