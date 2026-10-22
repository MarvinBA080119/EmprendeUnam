<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Un número de la revista. revi_mes guarda el periodo («julio-agosto») y
 * revi_en_portada marca el único número que sale en el inicio del sitio
 * (debe ser visible: ck_revi_portada_visible, uq_revista_en_portada).
 */
class Revista extends Model
{
    protected $table = 'revista';

    protected $primaryKey = 'revi_id_revista';

    public $timestamps = false;

    protected $fillable = [
        'usua_id_usuario',
        'revi_titulo',
        'revi_anio',
        'revi_mes',
        'revi_num_edicion',
        'revi_url_portada',
        'revi_url_revista_pdf',
        'revi_fecha_publicacion',
        'revi_visible',
        'revi_url_flipbook',
    ];

    protected function casts(): array
    {
        return [
            'revi_fecha_publicacion' => 'date',
            'revi_visible' => 'boolean',
            'revi_en_portada' => 'boolean',
        ];
    }

    public function articulos(): HasMany
    {
        return $this->hasMany(ArticuloRevista::class, 'revi_id_revista', 'revi_id_revista');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usua_id_usuario', 'usua_id_usuario');
    }

    public function scopeVisibles(Builder $query): Builder
    {
        return $query->where('revi_visible', true);
    }

    /** «No. 220 · julio-agosto 2026». */
    public function getEtiquetaAttribute(): string
    {
        return "No. {$this->revi_num_edicion} · {$this->revi_mes} {$this->revi_anio}";
    }
}
