<?php

/*
| Módulo del administrador. Prefijo /admin, nombre "admin.", middleware
| web + auth (registrado en bootstrap/app.php). Cada ruta declara su privilegio.
*/

use App\Http\Controllers\Admin\AutorController;
use App\Http\Controllers\Admin\PanelController;
use Illuminate\Support\Facades\Route;

// El panel lo ve quien tenga cualquiera de los privilegios exclusivos del administrador.
Route::get('/', [PanelController::class, 'index'])
    ->middleware('privilegio:ACTIVAR_AUTOR,DICTAMINAR_ARTICULO,CONSULTAR_TODOS_ARTICULOS,GESTIONAR_CONSULTORIO,GESTIONAR_CURSOS,CONSULTAR_SUSCRIPTORES')
    ->name('panel');

Route::middleware('privilegio:ACTIVAR_AUTOR')->group(function () {
    Route::get('/autores', [AutorController::class, 'index'])->name('autores.index');
    Route::post('/autores/{usuario}/activar', [AutorController::class, 'activar'])->name('autores.activar');
});
