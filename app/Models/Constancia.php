<?php

namespace App\Models;

use App\Enums\TipoDocumento;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Documentos de un artículo (cons_tipo): la constancia de aprobación, la
 * carta de agradecimiento (ambas con folio verificable) y la carta de cesión
 * de derechos firmada que sube el autor (sin folio).
 */
class Constancia extends Model
{
    protected $table = 'constancia';

    protected $primaryKey = 'cons_id_constancia';

    public $timestamps = false;

    protected $fillable = [
        'arti_id_autor',
        'cons_fecha_emision',
        'cons_url_archivo',
        'cons_folio',
        'cons_tipo',
    ];

    protected function casts(): array
    {
        return [
            'cons_fecha_emision' => 'date',
            'cons_tipo' => TipoDocumento::class,
        ];
    }

    public function scopeDeTipo(Builder $query, TipoDocumento $tipo): Builder
    {
        return $query->where('cons_tipo', $tipo->value);
    }

    public function articulo(): BelongsTo
    {
        return $this->belongsTo(Articulo::class, 'arti_id_autor', 'arti_id_autor');
    }
}
