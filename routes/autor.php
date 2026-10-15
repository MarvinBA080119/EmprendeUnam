<?php

/*
| Módulo del autor. Prefijo /autor, nombre "autor.", middleware web + auth
| (registrado en bootstrap/app.php). Cada ruta declara su privilegio.
*/

use App\Http\Controllers\Autor\PanelController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PanelController::class, 'index'])
    ->middleware('privilegio:CONSULTAR_ARTICULOS_PROPIOS')
    ->name('panel');
