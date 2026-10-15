<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Persona extends Model
{
    protected $table = 'persona';

    protected $primaryKey = 'pers_id_persona';

    public $timestamps = false;

    protected $fillable = [
        'pers_nombre',
        'pers_primer_apellido',
        'pers_segundo_apellido',
        'pers_subs_newsletter',
        'pers_acepta_aviso',
        'pers_fecha_suscripcion',
        'pers_token_confirmacion',
        'pers_fecha_confirmacion',
        'pers_fecha_baja',
    ];

    protected $hidden = ['pers_token_confirmacion'];

    protected function casts(): array
    {
        return [
            'pers_subs_newsletter' => 'boolean',
            'pers_acepta_aviso' => 'boolean',
            'pers_fecha_suscripcion' => 'datetime',
            'pers_fecha_confirmacion' => 'datetime',
            'pers_fecha_baja' => 'datetime',
        ];
    }

    public function usuario(): HasOne
    {
        return $this->hasOne(Usuario::class, 'pers_id_persona', 'pers_id_persona');
    }

    public function autor(): HasOne
    {
        return $this->hasOne(Autor::class, 'pers_id_persona', 'pers_id_persona');
    }

    public function comunicaciones(): HasMany
    {
        return $this->hasMany(Comunicacion::class, 'pers_id_persona', 'pers_id_persona');
    }

    /** Correo activo de la persona (tipo de comunicación "Correo…"). */
    public function correo(): HasOne
    {
        return $this->hasOne(Comunicacion::class, 'pers_id_persona', 'pers_id_persona')
            ->where('comu_activo', true)
            ->whereHas('tipo', fn (Builder $q) => $q->where('comu_nombre', 'ilike', 'correo%'));
    }

    public function temasNews(): HasMany
    {
        return $this->hasMany(TemaNews::class, 'pers_id_persona', 'pers_id_persona');
    }

    /** Suscriptor activo: suscrito, confirmado y sin baja. */
    public function scopeSuscriptoresActivos(Builder $query): Builder
    {
        return $query->where('pers_subs_newsletter', true)
            ->whereNotNull('pers_fecha_confirmacion')
            ->whereNull('pers_fecha_baja');
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim(implode(' ', array_filter([
            $this->pers_nombre,
            $this->pers_primer_apellido,
            $this->pers_segundo_apellido,
        ])));
    }
}
