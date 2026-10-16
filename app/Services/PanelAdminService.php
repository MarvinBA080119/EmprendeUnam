<?php

namespace App\Services;

use App\Enums\EstatusMensaje;
use App\Models\Articulo;
use App\Models\Curso;
use App\Models\Mensaje;
use App\Models\Persona;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Support\Collection;

/**
 * Contadores y bandeja de pendientes del panel del administrador.
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

    /**
     * Lo más antiguo primero de cada pendiente (máximo $limite por grupo).
     *
     * @return array{articulos: Collection<int, Articulo>, autores: Collection<int, Usuario>, mensajes: Collection<int, Mensaje>}
     */
    public function bandeja(int $limite = 5): array
    {
        return [
            'articulos' => Articulo::enRevision()
                ->with(['tema', 'autores' => fn ($q) => $q->wherePivot('auar_principal', true)->with('persona')])
                ->orderBy('arti_fecha_registro')
                ->orderBy('arti_id_autor')
                ->limit($limite)
                ->get(),
            'autores' => Usuario::where('usua_activo', false)
                ->whereHas('roles', fn ($q) => $q->where('rol_nombre', Rol::AUTOR))
                ->with(['persona.autor'])
                ->orderBy('usua_fecha_registro')
                ->orderBy('usua_id_usuario')
                ->limit($limite)
                ->get(),
            'mensajes' => Mensaje::originales()
                ->where('mens_status', EstatusMensaje::PENDIENTE->value)
                ->orderBy('mens_fecha_envio')
                ->orderBy('mens_id_mensaje')
                ->limit($limite)
                ->get(),
        ];
    }

    /** Cursos que el público ve hoy (fecha de inicio de hoy en adelante). */
    public function cursosVigentes(): int
    {
        return Curso::vigentes()->count();
    }
}
