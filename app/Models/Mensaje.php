<?php

namespace App\Models;

use App\Enums\EstatusMensaje;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Mensaje extends Model
{
    protected $table = 'mensaje';

    protected $primaryKey = 'mens_id_mensaje';

    public $timestamps = false;

    protected $fillable = [
        'usua_id_usuario',
        'men_mens_id_mensaje',
        'mens_remitente',
        'mens_lugar',
        'mens_correo',
        'mens_asunto',
        'mens_descripcion',
        'mens_status',
        'mens_fecha_envio',
    ];

    protected function casts(): array
    {
        return [
            'mens_status' => EstatusMensaje::class,
            'mens_fecha_envio' => 'datetime',
        ];
    }

    /** En una respuesta: el mensaje del lector al que contesta. */
    public function original(): BelongsTo
    {
        return $this->belongsTo(Mensaje::class, 'men_mens_id_mensaje', 'mens_id_mensaje');
    }

    /** En un mensaje del lector: la respuesta del administrador. */
    public function respuesta(): HasOne
    {
        return $this->hasOne(Mensaje::class, 'men_mens_id_mensaje', 'mens_id_mensaje');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usua_id_usuario', 'usua_id_usuario');
    }

    /** Mensajes enviados por lectores (no respuestas). */
    public function scopeOriginales(Builder $query): Builder
    {
        return $query->whereNull('men_mens_id_mensaje');
    }
}
