<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolPrivilegio extends Model
{
    protected $table = 'rol_privilegio';

    protected $primaryKey = 'ropr_id_rol_privilegio';

    public $timestamps = false;

    protected $fillable = ['rol_id_rol', 'ropr_id_privilegio'];

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'rol_id_rol', 'rol_id_rol');
    }

    public function privilegio(): BelongsTo
    {
        return $this->belongsTo(Privilegio::class, 'ropr_id_privilegio', 'priv_id_privilegio');
    }
}
