@extends('correos.layout')

@section('contenido')
    <p>Hola, {{ $nombre }}:</p>
    <p>Te informamos que tu artículo <strong>«{{ $titulo }}»</strong> no fue aceptado para su publicación en la Revista EmprendeUNAM.</p>
    <p><strong>Motivo del dictamen:</strong></p>
    <p style="border-left:4px solid #664300;padding-left:12px;white-space:pre-line;">{{ $motivo }}</p>
    <p>Agradecemos tu interés en la revista.</p>
@endsection
