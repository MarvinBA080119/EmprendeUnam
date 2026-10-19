<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PalabraArticulo extends Model
{
    protected $table = 'palabra_articulo';

    protected $primaryKey = 'paar_id_palabra_articulo';

    public $timestamps = false;

    protected $fillable = ['palabra_id_palabra', 'arti_id_autor'];

    public function palabra(): BelongsTo
    {
        return $this->belongsTo(Palabra::class, 'palabra_id_palabra', 'palabra_id_palabra');
    }

    public function articulo(): BelongsTo
    {
        return $this->belongsTo(Articulo::class, 'arti_id_autor', 'arti_id_autor');
    }
}
