<?php

namespace App\Policies;

use App\Models\Constancia;
use App\Models\Rol;
use App\Models\Usuario;
use App\Services\ArticuloService;

/**
 * RN-01: un autor solo descarga la constancia de sus artículos.
 * El administrador puede todo.
 */
class ConstanciaPolicy
{
    public function __construct(private ArticuloService $articulos) {}

    public function before(Usuario $usuario): ?bool
    {
        return $usuario->loadMissing('roles')->tieneRol(Rol::ADMINISTRADOR) ? true : null;
    }

    public function descargar(Usuario $usuario, Constancia $constancia): bool
    {
        return $this->articulos->esPrincipal($usuario, $constancia->articulo);
    }
}
