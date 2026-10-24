<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\ArticuloRevista;
use App\Models\Revista;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Portada, PDF completo y archivos de los artículos de un número. Viven
 * fuera de public/ y solo se entregan si el número es visible (el
 * administrador también ve los de números ocultos, para revisarlos).
 */
class EdicionArchivoController extends Controller
{
    public function portada(Request $request, Revista $revista): StreamedResponse
    {
        return $this->entregar($request, $revista, $revista->revi_url_portada);
    }

    public function numero(Request $request, Revista $revista): StreamedResponse
    {
        return $this->entregar($request, $revista, $revista->revi_url_revista_pdf, "emprendedores-{$revista->revi_num_edicion}.pdf");
    }

    public function articuloPdf(Request $request, ArticuloRevista $entrada): StreamedResponse
    {
        return $this->entregar($request, $entrada->revista, $entrada->rutaPdf(), 'articulo-'.$entrada->arre_id_articulo_revista.'.pdf');
    }

    public function articuloImagen(Request $request, ArticuloRevista $entrada): StreamedResponse
    {
        return $this->entregar($request, $entrada->revista, $entrada->arre_url_imagen);
    }

    private function entregar(Request $request, Revista $revista, ?string $ruta, ?string $nombre = null): StreamedResponse
    {
        $usuario = $request->user();
        $admin = $usuario instanceof Usuario && $usuario->loadMissing('roles')->tieneRol(Rol::ADMINISTRADOR);

        abort_unless($revista->revi_visible || $admin, 404);
        abort_unless($ruta && Storage::disk('local')->exists($ruta), 404);

        $respuesta = Storage::disk('local')->response($ruta, $nombre, [
            'Cache-Control' => $revista->revi_visible ? 'public, max-age=86400' : 'private, no-store',
        ]);
        $respuesta->headers->set('X-Content-Type-Options', 'nosniff');

        return $respuesta;
    }
}
