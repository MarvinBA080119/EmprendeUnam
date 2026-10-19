<?php

/*
| Módulo del autor. Prefijo /autor, nombre "autor.", middleware web + auth
| (registrado en bootstrap/app.php). Cada ruta declara su privilegio y las de
| un artículo concreto pasan además por ArticuloPolicy / ConstanciaPolicy.
| No hay rutas para editar ni reenviar artículos (RF-AUT-07 y RF-AUT-08).
*/

use App\Http\Controllers\Autor\ArticuloController;
use App\Http\Controllers\Autor\ConstanciaController;
use App\Http\Controllers\Autor\PanelController;
use Illuminate\Support\Facades\Route;

Route::middleware('privilegio:CONSULTAR_ARTICULOS_PROPIOS')->group(function () {
    Route::get('/', [PanelController::class, 'index'])->name('panel');
    Route::get('/articulos/{articulo}/pdf', [ArticuloController::class, 'pdf'])->name('articulos.pdf');
    Route::get('/articulos/{articulo}/aprobado', [ArticuloController::class, 'aprobado'])->name('articulos.aprobado');
});

Route::middleware('privilegio:ENVIAR_ARTICULO')->group(function () {
    Route::get('/articulos/nuevo', [ArticuloController::class, 'create'])->name('articulos.create');
    Route::post('/articulos', [ArticuloController::class, 'store'])->name('articulos.store');
});

Route::get('/articulos/{articulo}/constancia', [ConstanciaController::class, 'descargar'])
    ->middleware('privilegio:DESCARGAR_CONSTANCIA')
    ->name('articulos.constancia');
