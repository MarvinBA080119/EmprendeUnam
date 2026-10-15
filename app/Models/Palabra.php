<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Palabra extends Model
{
    protected $table = 'palabra';

    protected $primaryKey = 'palabra_id_palabra';

    public $timestamps = false;

    protected $fillable = ['pala_descripcion'];

    public function articulos(): BelongsToMany
    {
        return $this->belongsToMany(Articulo::class, 'palabra_articulo', 'palabra_id_palabra', 'arti_id_autor');
    }

    public function cursos(): BelongsToMany
    {
        return $this->belongsToMany(Curso::class, 'palabra_curso', 'palabra_id_palabra', 'curs_id_curso');
    }
}
