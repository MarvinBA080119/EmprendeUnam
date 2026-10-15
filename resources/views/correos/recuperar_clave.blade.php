@extends('correos.layout')

@section('contenido')
    <p>Hola, {{ $nombre }}:</p>
    <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta en la Revista EmprendeUNAM.</p>
    @include('correos.boton', ['enlace' => $enlace, 'texto' => 'Restablecer contraseña'])
    <p>El enlace vence en {{ $minutos }} minutos. Si tú no hiciste esta solicitud, ignora este correo: tu contraseña no cambiará.</p>
@endsection
