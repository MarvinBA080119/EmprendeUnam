<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    protected function casts(): array
    {
        return ['cons_fecha_emision' => 'date'];
    }

    public function articulo(): BelongsTo
    {
        return $this->belongsTo(Articulo::class, 'arti_id_autor', 'arti_id_autor');
    }
}
