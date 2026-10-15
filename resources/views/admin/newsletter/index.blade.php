@extends('layouts.admin')

@section('titulo', 'Newsletter')

@section('contenido')
    <h1 class="emp-titulo-seccion">Suscriptores del newsletter</h1>

    @php($props = [
        'urlDatos' => route('admin.newsletter.datos'),
        'urlExportar' => route('admin.newsletter.exportar'),
        'estados' => $estados,
        'filtros' => $filtros,
    ])
    <div id="tabla-suscriptores" data-props='@json($props)'>
        <noscript><div class="alert alert-warning">Esta sección necesita JavaScript.</div></noscript>
    </div>
@endsection
