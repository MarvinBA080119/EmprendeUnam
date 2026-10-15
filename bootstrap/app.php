<?php

use App\Http\Middleware\TienePrivilegio;
use App\Models\Usuario;
use App\Services\AccesoService;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware(['web', 'auth'])
                ->prefix('autor')
                ->name('autor.')
                ->group(base_path('routes/autor.php'));

            Route::middleware(['web', 'auth'])
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'privilegio' => TienePrivilegio::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('login'));

        // Quien ya inició sesión y abre /iniciar-sesion o /registro va a su panel.
        $middleware->redirectUsersTo(fn (Request $request) => $request->user() instanceof Usuario
            ? app(AccesoService::class)->rutaInicio($request->user())
            : route('inicio'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
