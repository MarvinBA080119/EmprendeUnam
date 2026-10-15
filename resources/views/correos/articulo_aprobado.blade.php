@extends('correos.layout')

@section('contenido')
    <p>Hola, {{ $nombre }}:</p>
    <p>Nos da gusto informarte que tu artículo <strong>«{{ $titulo }}»</strong> fue <strong>aprobado</strong> para su publicación en la Revista EmprendeUNAM. ¡Gracias por tu contribución!</p>
    <p>Tu constancia tiene el folio <strong>{{ $folio }}</strong>. Puedes descargarla desde tu panel de autor:</p>
    @include('correos.boton', ['enlace' => $enlace, 'texto' => 'Descargar constancia'])
@endsection
