<?php

namespace App\Providers;

use App\Auth\UsuarioProvider;
use App\Models\Articulo;
use App\Models\Constancia;
use App\Policies\ArticuloPolicy;
use App\Policies\ConstanciaPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
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
        // 5 intentos por minuto por usuario + IP).
        RateLimiter::for('registro', fn (Request $request) => Limit::perHour(10)->by($request->ip()));
        RateLimiter::for('recuperar-clave', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));
        RateLimiter::for('verificar-constancia', fn (Request $request) => Limit::perMinute(30)->by($request->ip()));

        Paginator::useBootstrapFive();

        // Componentes Blade anónimos en resources/views/componentes (<x-nombre />).
        Blade::anonymousComponentPath(resource_path('views/componentes'));

        // En desarrollo y pruebas: avisar de consultas N+1 y de atributos
        // que no están en $fillable.
        Model::preventLazyLoading(! $this->app->isProduction());
        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());
    }
}
