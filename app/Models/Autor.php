<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Autor extends Model
{
    protected $table = 'autor';

    protected $primaryKey = 'auto_id_autor';

    public $timestamps = false;

    // auto_id_coautor se deja en NULL: los coautores se ligan por autor_articulo.
    protected $fillable = [
        'pers_id_persona',
        'auto_institucion',
        'auto_grado_academico',
    ];

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'pers_id_persona', 'pers_id_persona');
    }

    public function articulos(): BelongsToMany
    {
        return $this->belongsToMany(Articulo::class, 'autor_articulo', 'auar_id_autor', 'arti_id_autor')
            ->withPivot('auar_principal');
    }
}
