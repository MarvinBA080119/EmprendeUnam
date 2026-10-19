<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PalabraCurso extends Model
{
    protected $table = 'palabra_curso';

    protected $primaryKey = 'pacu_id_palabra_curso';

    public $timestamps = false;

    protected $fillable = ['curs_id_curso', 'palabra_id_palabra'];

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'curs_id_curso', 'curs_id_curso');
    }

    public function palabra(): BelongsTo
    {
        return $this->belongsTo(Palabra::class, 'palabra_id_palabra', 'palabra_id_palabra');
    }
}
