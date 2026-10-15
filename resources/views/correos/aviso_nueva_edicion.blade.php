@extends('correos.layout')

@section('contenido')
    <p>Estimada/o {{ $nombre }}:</p>
    <p>Ya puedes leer <strong>{{ $titulo }}</strong> de <strong>Emprendedores al servicio de la pequeña y mediana empresa</strong>, el newsletter mensual de EmprendeUNAM.</p>
    <p>Encontrarás la noticia del mes, los datos clave de la economía, una herramienta recomendada y la agenda de eventos de nuestra comunidad.</p>
    @include('correos.boton', ['enlace' => $enlace, 'texto' => 'Leer la nueva edición'])
    <p>Gracias por formar parte de nuestra comunidad de lectores.</p>
    <p>Atentamente,<br><strong>Comité editorial de EmprendeUNAM</strong><br>Facultad de Contaduría y Administración, UNAM</p>
@endsection

@section('pie')
    <br>Recibes este correo porque te suscribiste a las notificaciones del newsletter.
    Si ya no deseas recibirlas, puedes <a href="{{ $enlaceBaja }}" style="color:#664300;">darte de baja aquí</a>.
@endsection
