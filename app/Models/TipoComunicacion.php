<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoComunicacion extends Model
{
    protected $table = 'tipo_comunicacion';

    protected $primaryKey = 'comu_id_tipo_comunicacion';

    public $timestamps = false;

    protected $fillable = ['comu_nombre'];

    public function comunicaciones(): HasMany
    {
        return $this->hasMany(Comunicacion::class, 'comu_id_tipo_comunicacion', 'comu_id_tipo_comunicacion');
    }

    /** Tipo "Correo" (los triggers lo buscan con comu_nombre ILIKE 'correo%'). */
    public function scopeCorreo(Builder $query): Builder
    {
        return $query->where('comu_nombre', 'ilike', 'correo%');
    }
}
