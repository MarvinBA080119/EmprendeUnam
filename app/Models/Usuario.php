<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuario';

    protected $primaryKey = 'usua_id_usuario';

    public $timestamps = false;

    protected $fillable = [
        'pers_id_persona',
        'usua_nombre_usuario',
        'usua_clave_acceso',
        'usua_fecha_registro',
        'usua_ultimo_login',
        'usua_activo',
        'usua_correo_verificado',
        'usua_fecha_verificacion',
    ];

    protected $hidden = ['usua_clave_acceso'];

    protected function casts(): array
    {
        return [
            'usua_fecha_registro' => 'date',
            'usua_ultimo_login' => 'date',
            'usua_activo' => 'boolean',
            'usua_correo_verificado' => 'boolean',
            'usua_fecha_verificacion' => 'datetime',
        ];
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'pers_id_persona', 'pers_id_persona');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Rol::class, 'rol_usuario', 'usua_id_usuario', 'rol_id_rol');
    }

    /* ---- Autenticación: la contraseña está en usua_clave_acceso ---- */

    public function getAuthPasswordName(): string
    {
        return 'usua_clave_acceso';
    }

    public function getAuthPassword(): string
    {
        return $this->usua_clave_acceso;
    }

    /** La tabla no tiene remember_token: "recordarme" está desactivado. */
    public function getRememberTokenName(): string
    {
        return '';
    }
}
