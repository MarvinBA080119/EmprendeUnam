@extends('correos.layout')

@section('contenido')
    <p>Hola:</p>
    <p>Este es un correo de prueba de la Revista EmprendeUNAM, enviado desde <strong>{{ $enviadoDesde }}</strong>.</p>
    <p>Si lo recibiste, el servidor ya puede enviar los avisos de cuentas, dictámenes, consultorio y newsletter.</p>
@endsection
