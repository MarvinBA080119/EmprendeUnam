<?php

/*
| Módulo del administrador. Prefijo /admin, nombre "admin.", middleware
| web + auth (registrado en bootstrap/app.php). Cada ruta declara su privilegio.
| Las rutas ".../datos" devuelven JSON para las tablas Vue (misma sesión y CSRF).
*/

use App\Http\Controllers\Admin\ArticuloController;
use App\Http\Controllers\Admin\AutorController;
use App\Http\Controllers\Admin\ConsultorioController;
use App\Http\Controllers\Admin\CursoController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\PanelController;
use Illuminate\Support\Facades\Route;

// El panel lo ve quien tenga cualquiera de los privilegios exclusivos del administrador.
Route::get('/', [PanelController::class, 'index'])
    ->middleware('privilegio:ACTIVAR_AUTOR,DICTAMINAR_ARTICULO,CONSULTAR_TODOS_ARTICULOS,GESTIONAR_CONSULTORIO,GESTIONAR_CURSOS,CONSULTAR_SUSCRIPTORES')
    ->name('panel');

Route::middleware('privilegio:ACTIVAR_AUTOR')->group(function () {
    Route::get('/autores', [AutorController::class, 'index'])->name('autores.index');
    Route::get('/autores/datos', [AutorController::class, 'datos'])->name('autores.datos');
    Route::post('/autores/{usuario}/activar', [AutorController::class, 'activar'])->name('autores.activar');
});

Route::middleware('privilegio:CONSULTAR_TODOS_ARTICULOS')->group(function () {
    Route::get('/articulos', [ArticuloController::class, 'index'])->name('articulos.index');
    Route::get('/articulos/datos', [ArticuloController::class, 'datos'])->name('articulos.datos');
    Route::get('/articulos/{articulo}', [ArticuloController::class, 'show'])->name('articulos.show');
    Route::get('/articulos/{articulo}/pdf', [ArticuloController::class, 'pdf'])->name('articulos.pdf');
    Route::get('/articulos/{articulo}/carta-cesion', [ArticuloController::class, 'cartaCesion'])->name('articulos.carta-cesion');
});

Route::middleware('privilegio:DICTAMINAR_ARTICULO')->group(function () {
    Route::post('/articulos/{articulo}/aprobar', [ArticuloController::class, 'aprobar'])->name('articulos.aprobar');
    Route::post('/articulos/{articulo}/rechazar', [ArticuloController::class, 'rechazar'])->name('articulos.rechazar');
});

Route::middleware('privilegio:GESTIONAR_CONSULTORIO')->group(function () {
    Route::get('/consultorio', [ConsultorioController::class, 'index'])->name('consultorio.index');
    Route::get('/consultorio/datos', [ConsultorioController::class, 'datos'])->name('consultorio.datos');
    Route::get('/consultorio/{mensaje}', [ConsultorioController::class, 'show'])->name('consultorio.show');
    Route::post('/consultorio/{mensaje}/responder', [ConsultorioController::class, 'responder'])->name('consultorio.responder');
    Route::post('/consultorio/{mensaje}/rechazar', [ConsultorioController::class, 'rechazar'])->name('consultorio.rechazar');
});

Route::middleware('privilegio:GESTIONAR_CURSOS')->group(function () {
    Route::get('/cursos', [CursoController::class, 'index'])->name('cursos.index');
    Route::get('/cursos/nuevo', [CursoController::class, 'create'])->name('cursos.create');
    Route::post('/cursos', [CursoController::class, 'store'])->name('cursos.store');
    Route::get('/cursos/{curso}/editar', [CursoController::class, 'edit'])->name('cursos.edit');
    Route::put('/cursos/{curso}', [CursoController::class, 'update'])->name('cursos.update');
    Route::delete('/cursos/{curso}', [CursoController::class, 'destroy'])->name('cursos.destroy');
});

Route::middleware('privilegio:CONSULTAR_SUSCRIPTORES')->group(function () {
    Route::get('/newsletter', [NewsletterController::class, 'index'])->name('newsletter.index');
    Route::get('/newsletter/datos', [NewsletterController::class, 'datos'])->name('newsletter.datos');
    Route::get('/newsletter/exportar', [NewsletterController::class, 'exportar'])->name('newsletter.exportar');
    Route::post('/newsletter/avisar', [NewsletterController::class, 'avisar'])->name('newsletter.avisar');
});
