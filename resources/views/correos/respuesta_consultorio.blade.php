@extends('correos.layout')

@section('contenido')
    <p>Hola, {{ $nombre }}:</p>
    <p>Gracias por escribir al Consultorio EmprendeUNAM. Esta es nuestra respuesta:</p>
    <p style="border-left:4px solid #664300;padding-left:12px;white-space:pre-line;">{{ $respuesta }}</p>
    <p style="font-size:13px;margin-top:24px;"><strong>Tu mensaje: «{{ $asunto }}»</strong></p>
    <p style="font-size:13px;white-space:pre-line;">{{ $pregunta }}</p>
    <p style="font-size:13px;">Tu pregunta y esta respuesta se publicarán en la sección de consultorio del sitio, sin tu correo.</p>
@endsection
