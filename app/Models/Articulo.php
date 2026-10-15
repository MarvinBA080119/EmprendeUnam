<?php

namespace App\Models;

use App\Enums\EstatusArticulo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Ojo: la PK se llama arti_id_autor pero es el ID DEL ARTÍCULO
 * (herencia del modelo físico). No tiene relación con el autor.
 */
class Articulo extends Model
{
    protected $table = 'articulo';

    protected $primaryKey = 'arti_id_autor';

    public $timestamps = false;

    protected $fillable = [
        'tema_id_tema',
        'arti_titulo',
        'arti_url_pdf',
        'arti_status',
        'arti_fecha_registro',
        'arti_fecha_modificacion',
        'arti_comentario_rechazo',
    ];

    protected function casts(): array
    {
        return [
            'arti_status' => EstatusArticulo::class,
            'arti_fecha_registro' => 'date',
            'arti_fecha_modificacion' => 'date',
        ];
    }

    public function tema(): BelongsTo
    {
        return $this->belongsTo(Tema::class, 'tema_id_tema', 'tema_id_tema');
    }

    public function autores(): BelongsToMany
    {
        return $this->belongsToMany(Autor::class, 'autor_articulo', 'arti_id_autor', 'auar_id_autor')
            ->withPivot('auar_principal');
    }

    public function palabras(): BelongsToMany
    {
        return $this->belongsToMany(Palabra::class, 'palabra_articulo', 'arti_id_autor', 'palabra_id_palabra');
    }

    public function constancia(): HasOne
    {
        return $this->hasOne(Constancia::class, 'arti_id_autor', 'arti_id_autor');
    }

    public function scopeAprobados(Builder $query): Builder
    {
        return $query->where('arti_status', EstatusArticulo::APROBADO->value);
    }

    public function scopeEnRevision(Builder $query): Builder
    {
        return $query->where('arti_status', EstatusArticulo::EN_REVISION->value);
    }
}
