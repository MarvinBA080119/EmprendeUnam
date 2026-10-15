<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolUsuario extends Model
{
    protected $table = 'rol_usuario';

    protected $primaryKey = 'rous_id_rol_usuario';

    public $timestamps = false;

    protected $fillable = ['usua_id_usuario', 'rol_id_rol'];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usua_id_usuario', 'usua_id_usuario');
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'rol_id_rol', 'rol_id_rol');
    }
}
