@extends('layouts.admin')

@section('titulo', 'Autores')

@section('contenido')
    <x-encabezado titulo="Autores" descripcion="Cuentas de autor registradas. Las pendientes no pueden entrar hasta que las actives." />

    {{-- Un solo argumento en @json: con dos, Blade toma el segundo como opciones
         y se pierde el escape de comillas y < > (XSS). --}}
    @php($props = ['urlDatos' => route('admin.autores.datos'), 'filtros' => $filtros])
    <div id="tabla-autores" data-props='@json($props)'>
        <noscript><div class="alert alert-warning">Esta sección necesita JavaScript.</div></noscript>
    </div>
@endsection
