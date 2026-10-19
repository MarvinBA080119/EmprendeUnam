<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tema extends Model
{
    protected $table = 'tema';

    protected $primaryKey = 'tema_id_tema';

    public $timestamps = false;

    protected $fillable = ['tema_nombre', 'tema_descripcion'];

    public function articulos(): HasMany
    {
        return $this->hasMany(Articulo::class, 'tema_id_tema', 'tema_id_tema');
    }
}
