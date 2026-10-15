@extends('correos.layout')

@section('contenido')
    <p>Hola, {{ $nombre }}:</p>
    <p>Recibimos tu artículo <strong>«{{ $titulo }}»</strong>. Quedó con estatus <strong>En revisión</strong>.</p>
    <p>Te avisaremos por correo cuando el administrador emita el dictamen. También puedes consultar su estatus en tu panel de autor.</p>
@endsection
