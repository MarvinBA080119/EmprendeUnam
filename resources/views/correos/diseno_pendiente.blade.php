@extends('correos.layout')

@section('contenido')
    <p>Hola:</p>
    <p>La dirección editorial aprobó un artículo y ya está en tu mesa de diseño:</p>
    <p><strong>«{{ $titulo }}»</strong>@if ($seccion)<br>{{ $seccion }}@endif</p>
    <p>Descarga el texto original desde tu panel y, cuando lo tengas listo, sube ahí mismo el PDF diseñado.</p>
    @include('correos.boton', ['enlace' => $enlace, 'texto' => 'Ir a la mesa de diseño'])
@endsection
