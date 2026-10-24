<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Revista;
use App\Models\Rol;
use App\Models\Usuario;
use App\Services\FlipbookService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Entrega los archivos de un flipbook generado (/flipbook/{numero}/…).
 * Es público si un número visible lo usa como eBook; el administrador lo ve
 * siempre, para revisarlo antes de publicarlo.
 */
class FlipbookController extends Controller
{
    public function __construct(private FlipbookService $flipbooks) {}

    public function ver(Request $request, string $numero, string $ruta = ''): BinaryFileResponse
    {
        $usuario = $request->user();
        $admin = $usuario instanceof Usuario && $usuario->loadMissing('roles')->tieneRol(Rol::ADMINISTRADOR);
        $publicado = Revista::visibles()->where('revi_url_flipbook', FlipbookService::ruta($numero))->exists();
        abort_unless($admin || $publicado, 404);

        $archivo = $this->flipbooks->archivo($numero, $ruta);
        abort_unless($archivo !== null, 404);

        $html = str_starts_with($archivo['tipo'], 'text/html');

        return response()->file($archivo['ruta'], [
            'Content-Type' => $archivo['tipo'],
            // Las hojas llevan ?v= de la generación: se guardan en caché; el visor no.
            'Cache-Control' => $html || ! $publicado ? 'no-cache, private' : 'public, max-age=604800',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
