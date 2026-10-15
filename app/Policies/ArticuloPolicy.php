<?php

namespace App\Policies;

use App\Models\Articulo;
use App\Models\Rol;
use App\Models\Usuario;
use App\Services\ArticuloService;

/**
 * RN-01: un autor solo ve y descarga sus propios artículos.
 * El administrador puede todo.
 */
class ArticuloPolicy
{
    public function __construct(private ArticuloService $articulos) {}

    public function before(Usuario $usuario): ?bool
    {
        return $usuario->loadMissing('roles')->tieneRol(Rol::ADMINISTRADOR) ? true : null;
    }

    public function ver(Usuario $usuario, Articulo $articulo): bool
    {
        return $this->articulos->esPrincipal($usuario, $articulo);
    }

    public function descargar(Usuario $usuario, Articulo $articulo): bool
    {
        return $this->ver($usuario, $articulo);
    }
}
