<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Rol extends Model
{
    public const ADMINISTRADOR = 'Administrador';

    public const AUTOR = 'Autor';

    public const DISENO = 'Diseño';

    public const LECTOR = 'Lector';

    public const SUSCRIPTOR_NEWSLETTER = 'Suscriptor_newsletter';

    protected $table = 'rol';

    protected $primaryKey = 'rol_id_rol';

    public $timestamps = false;

    protected $fillable = ['rol_nombre', 'rol_descripcion'];

    public function privilegios(): BelongsToMany
    {
        return $this->belongsToMany(Privilegio::class, 'rol_privilegio', 'rol_id_rol', 'ropr_id_privilegio');
    }

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'rol_usuario', 'rol_id_rol', 'usua_id_usuario');
    }
}
