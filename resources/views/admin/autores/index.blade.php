@extends('layouts.admin')

@section('titulo', 'Autores')

@section('contenido')
    <h1 class="emp-titulo-seccion">Autores</h1>

    {{-- Un solo argumento en @json: con dos, Blade toma el segundo como opciones
         y se pierde el escape de comillas y < > (XSS). --}}
    @php($props = ['urlDatos' => route('admin.autores.datos'), 'filtros' => $filtros])
    <div id="tabla-autores" data-props='@json($props)'>
        <noscript><div class="alert alert-warning">Esta sección necesita JavaScript.</div></noscript>
    </div>
@endsection
