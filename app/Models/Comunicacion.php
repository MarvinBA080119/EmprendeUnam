<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comunicacion extends Model
{
    protected $table = 'comunicacion';

    protected $primaryKey = 'comu_id_comunicacion';

    public $timestamps = false;

    protected $fillable = [
        'pers_id_persona',
        'comu_id_tipo_comunicacion',
        'comu_valor_comunicacion',
        'comu_extension',
        'comu_etiqueta',
        'comu_activo',
    ];

    protected function casts(): array
    {
        return ['comu_activo' => 'boolean'];
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'pers_id_persona', 'pers_id_persona');
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoComunicacion::class, 'comu_id_tipo_comunicacion', 'comu_id_tipo_comunicacion');
    }

    /** Correos (tipo "Correo…"). */
    public function scopeCorreos(Builder $query): Builder
    {
        return $query->whereHas('tipo', fn (Builder $q) => $q->correo());
    }

    /** Búsqueda por valor sin importar mayúsculas (igual que uq_comunicacion_valor). */
    public function scopeConValor(Builder $query, string $valor): Builder
    {
        return $query->whereRaw('lower(comu_valor_comunicacion) = lower(?)', [$valor]);
    }
}
