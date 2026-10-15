<?php

namespace App\Services;

use App\Enums\EstatusMensaje;
use App\Models\Articulo;
use App\Models\Mensaje;
use App\Models\Persona;
use App\Models\Rol;
use App\Models\Usuario;

/**
 * Contadores del panel del administrador (fase D).
 */
class PanelAdminService
{
    /** @return array{autores_pendientes: int, articulos_en_revision: int, mensajes_pendientes: int, suscriptores_activos: int} */
    public function contadores(): array
    {
        return [
            'autores_pendientes' => Usuario::where('usua_activo', false)
                ->whereHas('roles', fn ($q) => $q->where('rol_nombre', Rol::AUTOR))
                ->count(),
            'articulos_en_revision' => Articulo::enRevision()->count(),
            'mensajes_pendientes' => Mensaje::originales()->where('mens_status', EstatusMensaje::PENDIENTE->value)->count(),
            'suscriptores_activos' => Persona::suscriptoresActivos()->count(),
        ];
    }
}
