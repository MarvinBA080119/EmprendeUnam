@extends('correos.layout')

@section('contenido')
    <p>Hola, {{ $nombre }}:</p>
    <p>Recibimos tu registro como autor de la Revista EmprendeUNAM.</p>
    <p>El administrador revisará tu solicitud y activará tu cuenta. Te avisaremos por correo cuando puedas iniciar sesión y enviar tus artículos.</p>
@endsection
