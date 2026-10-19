<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemaNews extends Model
{
    protected $table = 'tema_news';

    protected $primaryKey = 'tene_id_tema_news';

    public $timestamps = false;

    protected $fillable = ['pers_id_persona', 'tene_tema_interes'];

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'pers_id_persona', 'pers_id_persona');
    }
}
