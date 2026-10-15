@extends('correos.layout')

@section('contenido')
    <p>Hola, {{ $nombre }}:</p>
    <p>Tu cuenta de autor en la Revista EmprendeUNAM fue activada. Ya puedes iniciar sesión con tu correo ({{ $correo }}) y enviar tus artículos.</p>
    @include('correos.boton', ['enlace' => $enlace, 'texto' => 'Iniciar sesión'])
@endsection
