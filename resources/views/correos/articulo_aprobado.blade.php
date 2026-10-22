@extends('correos.layout')

@section('contenido')
    <p>Hola, {{ $nombre }}:</p>
    <p>Nos da gusto informarte que tu artículo <strong>«{{ $titulo }}»</strong> fue <strong>aprobado</strong> para su publicación en la Revista EmprendeUNAM. ¡Gracias por tu contribución!</p>
    <p>Tu constancia tiene el folio <strong>{{ $folio }}</strong>. En tu panel de autor ya están tus documentos:</p>
    <ul>
        <li>la constancia de aprobación,</li>
        <li>la carta de agradecimiento de la dirección editorial y</li>
        <li>el formato de la carta de cesión de derechos, junto con la copia firmada que enviaste.</li>
    </ul>
    @include('correos.boton', ['enlace' => $enlace, 'texto' => 'Ver mis documentos'])
@endsection
