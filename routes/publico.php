<?php

/*
| Sitio público (sin sesión). Las páginas de contenido vienen del sitio
| original; los formularios (consultorio, newsletter) y el buscador usan el
| sistema. Las direcciones .php del sitio original redirigen (301) a las nuevas.
*/

use App\Http\Controllers\Publico\BuscadorController;
use App\Http\Controllers\Publico\ConsultorioController;
use App\Http\Controllers\Publico\CursoController;
use App\Http\Controllers\Publico\EdicionArchivoController;
use App\Http\Controllers\Publico\InicioController;
use App\Http\Controllers\Publico\NewsletterController;
use App\Http\Controllers\Publico\PaginaController;
use App\Http\Controllers\Publico\VerificarConstanciaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [InicioController::class, 'index'])->name('inicio');

Route::controller(PaginaController::class)->group(function () {
    Route::get('/acerca-de', 'acerca')->name('acerca');
    Route::get('/historia', 'historia')->name('historia');
    Route::get('/historico', 'historico')->name('historico');
    Route::get('/tematicas', 'tematicas')->name('tematicas');
    Route::get('/podcasts', 'podcasts')->name('podcasts');
    Route::get('/webinar', 'webinar')->name('webinar');
    Route::get('/10-de-10', 'diezDeDiez')->name('diezdediez');
    Route::get('/10-de-10/{tema}', 'diezDeDiezArea')->where('tema', '[a-z0-9-]+')->name('diezdediez.tema');
    Route::get('/las-pyme-hablan', 'pymes')->name('pyme-hablan');
    Route::get('/lineamientos', 'lineamientos')->name('lineamientos');
    Route::get('/envio-de-articulos', 'envioArticulos')->name('envio-articulos');
});

// Consultorio (6.6): consultas contestadas y formulario para lectores.
Route::get('/consultorio', [ConsultorioController::class, 'index'])->name('consultorio');
Route::post('/consultorio', [ConsultorioController::class, 'store'])->name('consultorio.store');

// Cursos vigentes (6.7) y buscador (6.9).
Route::get('/cursos', [CursoController::class, 'index'])->name('cursos');
Route::get('/buscar', [BuscadorController::class, 'index'])->name('buscador');
Route::get('/buscar/resultados', [BuscadorController::class, 'resultados'])
    ->middleware('throttle:buscador')
    ->name('buscador.resultados');

// Newsletter (6.8).
Route::controller(NewsletterController::class)->group(function () {
    Route::get('/newsletter', 'newsletter')->name('newsletter');
    Route::get('/suscripcion', 'suscripcion')->name('suscripcion');
    Route::post('/newsletter/suscribir', 'suscribir')->middleware('throttle:newsletter')->name('newsletter.suscribir');
    Route::get('/newsletter/confirmar/{token}', 'confirmar')->where('token', '[a-f0-9]{64}')->name('newsletter.confirmar');
    Route::get('/newsletter/baja/{token}', 'baja')->where('token', '[a-f0-9]{64}')->name('newsletter.baja');
    Route::post('/newsletter/baja/{token}', 'confirmarBaja')->where('token', '[a-f0-9]{64}')->name('newsletter.baja.confirmar');
});

// Archivos de los números de la revista (solo de números visibles).
Route::controller(EdicionArchivoController::class)->prefix('edicion')->name('edicion.')->group(function () {
    Route::get('/{revista}/portada', 'portada')->whereNumber('revista')->name('portada');
    Route::get('/{revista}/numero', 'numero')->whereNumber('revista')->name('numero');
    Route::get('/articulo/{entrada}/pdf', 'articuloPdf')->whereNumber('entrada')->name('articulo.pdf');
    Route::get('/articulo/{entrada}/imagen', 'articuloImagen')->whereNumber('entrada')->name('articulo.imagen');
});

// Verificación pública de constancias (6.10).
Route::get('/constancias/verificar/{folio}', [VerificarConstanciaController::class, 'show'])
    ->where('folio', '[A-Za-z0-9-]{1,30}')
    ->middleware('throttle:verificar-constancia')
    ->name('constancias.verificar');

// Direcciones del sitio original (enlaces y buscadores externos).
foreach ([
    'index.php' => 'inicio', 'acercade.php' => 'acerca', 'historia.php' => 'historia',
    'historico.php' => 'historico', 'tematicas.php' => 'tematicas', 'podcasts.php' => 'podcasts',
    'webinar.php' => 'webinar', 'diezdediez.php' => 'diezdediez', 'pyme-hablan.php' => 'pyme-hablan',
    'lineamientos.php' => 'lineamientos', 'envio-articulos.php' => 'envio-articulos',
    'consultorio.php' => 'consultorio', 'suscripcion.php' => 'suscripcion',
    'newsletter/index.html' => 'newsletter',
] as $anterior => $ruta) {
    Route::get($anterior, fn () => redirect()->route($ruta, request()->query(), 301));
}
Route::get('diezdediez-detalle.php', fn () => request()->filled('tema')
    ? redirect()->route('diezdediez.tema', ['tema' => (string) request()->query('tema')], 301)
    : redirect()->route('diezdediez', [], 301));
