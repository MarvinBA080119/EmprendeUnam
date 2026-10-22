<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Artículo de un número. arre_posicion_index (1 a 5) marca los que se ven
 * ahora en el índice del sitio; arre_fecha_index, cuándo entraron por última
 * vez (nulo = aún no se ha mostrado). arti_id_autor liga un artículo aprobado
 * del sistema o queda nulo para una entrada editorial.
 */
class ArticuloRevista extends Model
{
    public const POSICIONES = 5;

    protected $table = 'articulo_revista';

    protected $primaryKey = 'arre_id_articulo_revista';

    public $timestamps = false;

    protected $fillable = [
        'revi_id_revista',
        'arti_id_autor',
        'arre_seccion',
        'arre_titulo',
        'arre_descripcion',
        'arre_autores',
        'arre_paginas',
        'arre_url_pdf',
        'arre_url_imagen',
    ];

    protected function casts(): array
    {
        return [
            'arre_posicion_index' => 'integer',
            'arre_fecha_index' => 'date',
        ];
    }

    public function revista(): BelongsTo
    {
        return $this->belongsTo(Revista::class, 'revi_id_revista', 'revi_id_revista');
    }

    public function articulo(): BelongsTo
    {
        return $this->belongsTo(Articulo::class, 'arti_id_autor', 'arti_id_autor');
    }

    /** Ajustes del administrador (articulo_curso.arcu_incluido). */
    public function cursos(): BelongsToMany
    {
        return $this->belongsToMany(Curso::class, 'articulo_curso', 'arre_id_articulo_revista', 'curs_id_curso')
            ->withPivot('arcu_incluido');
    }
}
