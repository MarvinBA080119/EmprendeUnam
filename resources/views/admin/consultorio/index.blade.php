@extends('layouts.admin')

@section('titulo', 'Consultorio')

@section('contenido')
    <x-encabezado titulo="Consultorio" descripcion="Preguntas de los lectores. Al responder, la respuesta se envía por correo y se publica en el sitio." />

    @php($props = [
        'urlDatos' => route('admin.consultorio.datos'),
        'filtros' => $filtros,
        'estatus' => collect(\App\Enums\EstatusMensaje::cases())->map(fn ($e) => ['valor' => $e->value, 'etiqueta' => $e->etiqueta()]),
    ])
    <div id="tabla-mensajes" data-props='@json($props)'>
        <noscript><div class="alert alert-warning">Esta sección necesita JavaScript.</div></noscript>
    </div>
@endsection
