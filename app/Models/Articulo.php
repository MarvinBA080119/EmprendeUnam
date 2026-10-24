<?php

namespace App\Models;

use App\Enums\EstatusArticulo;
use App\Enums\TipoDocumento;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'arti_resumen',
        'arti_url_pdf',
        'arti_status',
        'arti_fecha_registro',
        'arti_fecha_modificacion',
        'arti_comentario_rechazo',
        'arti_url_pdf_diseno',
        'arti_fecha_diseno',
        'arti_id_disenador',
    ];

    protected function casts(): array
    {
        return [
            'arti_status' => EstatusArticulo::class,
            'arti_fecha_registro' => 'date',
            'arti_fecha_modificacion' => 'date',
            'arti_fecha_diseno' => 'datetime',
        ];
    }

    /** Cuenta de diseño que subió el PDF diseñado (arti_id_disenador). */
    public function disenador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'arti_id_disenador', 'usua_id_usuario');
    }

    public function tieneDiseno(): bool
    {
        return $this->arti_url_pdf_diseno !== null;
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

    /** Constancia de aprobación (la fila de tipo CONSTANCIA). */
    public function constancia(): HasOne
    {
        return $this->documento(TipoDocumento::CONSTANCIA);
    }

    public function cartaAgradecimiento(): HasOne
    {
        return $this->documento(TipoDocumento::AGRADECIMIENTO);
    }

    /** Carta de cesión de derechos firmada que subió el autor al enviar. */
    public function cartaCesion(): HasOne
    {
        return $this->documento(TipoDocumento::CESION);
    }

    /** Lugar del artículo en los números de la revista (articulo_revista). */
    public function entradasRevista(): HasMany
    {
        return $this->hasMany(ArticuloRevista::class, 'arti_id_autor', 'arti_id_autor');
    }

    private function documento(TipoDocumento $tipo): HasOne
    {
        return $this->hasOne(Constancia::class, 'arti_id_autor', 'arti_id_autor')->where('cons_tipo', $tipo->value);
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
