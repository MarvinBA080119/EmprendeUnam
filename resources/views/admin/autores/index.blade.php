@extends('layouts.admin')

@section('titulo', 'Autores')

@section('contenido')
    <h1 class="emp-titulo-seccion">Autores</h1>

    <div id="tabla-autores" data-props='@json(['urlDatos' => route('admin.autores.datos'), 'filtros' => $filtros])'>
        <noscript><div class="alert alert-warning">Esta sección necesita JavaScript.</div></noscript>
    </div>
@endsection
