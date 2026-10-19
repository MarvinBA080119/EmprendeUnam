<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutorArticulo extends Model
{
    protected $table = 'autor_articulo';

    protected $primaryKey = 'auar_id_autor_articulo';

    public $timestamps = false;

    protected $fillable = ['auar_id_autor', 'arti_id_autor', 'auar_principal'];

    protected function casts(): array
    {
        return ['auar_principal' => 'boolean'];
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(Autor::class, 'auar_id_autor', 'auto_id_autor');
    }

    public function articulo(): BelongsTo
    {
        return $this->belongsTo(Articulo::class, 'arti_id_autor', 'arti_id_autor');
    }
}
