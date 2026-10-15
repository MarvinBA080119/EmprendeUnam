<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Curso extends Model
{
    protected $table = 'curso';

    protected $primaryKey = 'curs_id_curso';

    public $timestamps = false;

    protected $fillable = [
        'curs_nombre_curso',
        'curs_url',
        'curs_institucion',
        'curs_fecha',
    ];

    protected function casts(): array
    {
        return ['curs_fecha' => 'date'];
    }

    public function palabras(): BelongsToMany
    {
        return $this->belongsToMany(Palabra::class, 'palabra_curso', 'curs_id_curso', 'palabra_id_palabra');
    }

    /** Cursos visibles al público: fecha de hoy en adelante. */
    public function scopeVigentes(Builder $query): Builder
    {
        return $query->whereDate('curs_fecha', '>=', today());
    }
}
