@extends('correos.layout')

@section('contenido')
    <p>Hola:</p>
    @if ($reemplazo)
        <p>La mesa de diseño <strong>actualizó</strong> el PDF del artículo <strong>«{{ $titulo }}»</strong>. La versión anterior se reemplazó.</p>
    @else
        <p>La mesa de diseño subió el PDF diseñado del artículo <strong>«{{ $titulo }}»</strong>.</p>
    @endif
    <p>Puedes descargarlo para el armado del número. Si el artículo ya está en un número, el sitio publica este PDF de inmediato.</p>
    @include('correos.boton', ['enlace' => $enlace, 'texto' => 'Ver manuscritos y diseños'])
@endsection
