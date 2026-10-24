<?php

use App\Http\Middleware\EncabezadosSeguridad;
use App\Http\Middleware\ExpiraPorInactividad;
use App\Http\Middleware\PanelDelRol;
use App\Http\Middleware\SinCache;
use App\Http\Middleware\TienePrivilegio;
use App\Models\Usuario;
use App\Services\AccesoService;
use App\Support\ErrorBaseDatos;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware(['web', 'auth', 'panel:autor'])
                ->prefix('autor')
                ->name('autor.')
                ->group(base_path('routes/autor.php'));

            Route::middleware(['web', 'auth', 'panel:admin'])
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));

            Route::middleware(['web', 'auth', 'panel:diseno'])
                ->prefix('diseno')
                ->name('diseno.')
                ->group(base_path('routes/diseno.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'privilegio' => TienePrivilegio::class,
            'panel' => PanelDelRol::class,
        ]);

        $middleware->web(append: [
            EncabezadosSeguridad::class,
            SinCache::class,
            ExpiraPorInactividad::class,
        ]);

        // Solo se aceptan peticiones dirigidas al dominio de APP_URL: los enlaces
        // de los correos (recuperar contraseña, newsletter) no dependen del
        // encabezado Host que mande el cliente. No aplica en local ni en pruebas.
        $middleware->trustHosts();

        $middleware->redirectGuestsTo(fn () => route('login'));

        // Quien ya inició sesión y abre /registro o la recuperación de contraseña
        // va a su panel (el login no lleva «guest»: ver routes/auth.php).
        $middleware->redirectUsersTo(fn (Request $request) => $request->user() instanceof Usuario
            ? app(AccesoService::class)->rutaInicio($request->user())
            : route('inicio'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Las contraseñas nunca regresan a la sesión como «old input».
        $exceptions->dontFlash(['clave', 'clave_confirmation', 'clave_actual']);

        // Reglas de la base (duplicado, regla de negocio, registro en uso): se
        // registran como aviso, sin la traza, y se muestran como mensaje claro.
        $exceptions->report(function (QueryException $e) {
            if (ErrorBaseDatos::mensaje($e) === null) {
                return null;
            }

            Log::warning('Regla de la base de datos', [
                'sqlstate' => ErrorBaseDatos::codigo($e),
                'error' => strtok($e->getPrevious()?->getMessage() ?? $e->getMessage(), "\n"),
            ]);

            return false;
        });

        // Token CSRF vencido (419) en el login o al cerrar sesión: la sesión ya
        // había terminado o la página llevaba mucho tiempo abierta. Se vuelve
        // al login con un aviso claro en lugar de la página de error.
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() !== 419 || $request->expectsJson()) {
                return null;
            }

            if ($request->routeIs('logout')) {
                return redirect()->route('login')->with('aviso', $request->input('motivo') === 'inactividad'
                    ? ExpiraPorInactividad::MENSAJE
                    : 'Tu sesión ya había terminado.');
            }

            if ($request->routeIs('login.store')) {
                return redirect()->route('login')
                    ->withInput($request->only('acceso'))
                    ->with('aviso', 'La página estuvo abierta demasiado tiempo. Escribe de nuevo tu contraseña.');
            }

            return null;
        });

        $exceptions->render(function (QueryException $e, Request $request) {
            $mensaje = ErrorBaseDatos::mensaje($e);

            if ($mensaje === null) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => $mensaje, 'errors' => ['general' => [$mensaje]]], 422);
            }

            if ($request->isMethod('GET') || $request->isMethod('HEAD')) {
                return response()->view('errors.409', ['mensaje' => $mensaje], 409);
            }

            return back()
                ->withInput($request->except(['clave', 'clave_confirmation', 'clave_actual']))
                ->withErrors(['general' => $mensaje])
                ->with('error', $mensaje);
        });
    })->create();
