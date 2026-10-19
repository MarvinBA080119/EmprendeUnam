<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Privilegio extends Model
{
    protected $table = 'privilegio';

    protected $primaryKey = 'priv_id_privilegio';

    public $timestamps = false;

    protected $fillable = ['priv_nombre', 'priv_descripcion'];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Rol::class, 'rol_privilegio', 'ropr_id_privilegio', 'rol_id_rol');
    }
}
