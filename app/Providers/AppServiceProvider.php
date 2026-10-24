<?php

namespace App\Providers;

use App\Auth\UsuarioProvider;
use App\Models\Articulo;
use App\Models\Constancia;
use App\Models\Usuario;
use App\Policies\ArticuloPolicy;
use App\Policies\ConstanciaPolicy;
use App\Services\AccesoService;
use App\Services\DisenoService;
use App\Services\PanelAdminService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Proveedor de usuarios propio: correo (comunicacion) o nombre de usuario.
        Auth::provider('usuarios', fn ($app, array $config) => new UsuarioProvider($app['hash'], $config['model']));

        // Las llaves de las rutas son enteros (evita errores de PostgreSQL con texto).
        Route::pattern('articulo', '[0-9]+');
        Route::pattern('usuario', '[0-9]+');
        Route::pattern('curso', '[0-9]+');
        Route::pattern('mensaje', '[0-9]+');

        Gate::policy(Articulo::class, ArticuloPolicy::class);
        Gate::policy(Constancia::class, ConstanciaPolicy::class);

        // Límites de peticiones (el del inicio de sesión vive en AccesoService:
        // 5 intentos por minuto por usuario + IP y 20 cada 15 minutos por usuario).
        RateLimiter::for('registro', fn (Request $request) => Limit::perHour(10)->by($request->ip()));
        RateLimiter::for('recuperar-clave', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));
        RateLimiter::for('verificar-constancia', fn (Request $request) => Limit::perMinute(30)->by($request->ip()));
        RateLimiter::for('buscador', fn (Request $request) => Limit::perMinute(60)->by($request->ip()));
        RateLimiter::for('newsletter', fn (Request $request) => Limit::perMinutes(10, 6)->by($request->ip()));

        // En producción, todas las URL (incluidas las de los correos) se arman
        // con APP_URL y no con el encabezado Host de la petición.
        if ($this->app->isProduction()) {
            URL::forceRootUrl(config('app.url'));
            if (str_starts_with((string) config('app.url'), 'https://')) {
                URL::forceScheme('https');
            }
        }

        Paginator::useBootstrapFive();

        // Contadores de pendientes en la barra lateral del administrador.
        View::composer('layouts.admin', function ($vista) {
            $vista->with('pendientes', app(PanelAdminService::class)->contadores());
        });

        // Pendientes de la mesa de diseño en su barra lateral.
        View::composer('layouts.diseno', function ($vista) {
            $vista->with('conteosDiseno', app(DisenoService::class)->conteos());
        });

        // Sesión abierta en el sitio público: quién está dentro y qué panel le toca.
        View::composer(['layouts.sitio', 'sitio.envio_articulos', 'auth.login'], function ($vista) {
            $usuario = Auth::user();
            $acceso = app(AccesoService::class);
            $panel = $usuario instanceof Usuario ? $acceso->panelDe($usuario) : null;

            $vista->with('sesionSitio', $usuario instanceof Usuario ? [
                'usuario' => $usuario->usua_nombre_usuario,
                'panel' => $panel,
                'nombrePanel' => AccesoService::PANELES[$panel] ?? null,
                'ruta' => $acceso->rutaInicio($usuario),
            ] : null);
        });

        // Componentes Blade anónimos en resources/views/componentes (<x-nombre />).
        Blade::anonymousComponentPath(resource_path('views/componentes'));

        // En desarrollo y pruebas: avisar de consultas N+1 y de atributos
        // que no están en $fillable.
        Model::preventLazyLoading(! $this->app->isProduction());
        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());
    }
}
