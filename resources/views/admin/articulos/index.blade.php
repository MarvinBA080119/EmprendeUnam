@extends('layouts.admin')

@section('titulo', 'Artículos')

@section('contenido')
    <h1 class="emp-titulo-seccion">Artículos</h1>

    @php($props = [
        'urlDatos' => route('admin.articulos.datos'),
        'filtros' => $filtros,
        'temas' => $temas->map(fn ($t) => ['id' => $t->tema_id_tema, 'nombre' => $t->tema_nombre])->values(),
        'estatus' => collect(\App\Enums\EstatusArticulo::cases())->map(fn ($e) => ['valor' => $e->value, 'etiqueta' => $e->etiqueta()]),
    ])
    <div id="tabla-articulos" data-props='@json($props)'>
        <noscript><div class="alert alert-warning">Esta sección necesita JavaScript.</div></noscript>
    </div>
@endsection
