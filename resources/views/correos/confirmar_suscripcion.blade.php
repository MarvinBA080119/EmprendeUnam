@extends('correos.layout')

@section('contenido')
    <p>Estimada/o {{ $nombre }}:</p>
    <p>Gracias por suscribirte a <strong>Emprendedores al servicio de la pequeña y mediana empresa</strong>, el newsletter mensual de EmprendeUNAM.</p>
    <p>Para empezar a recibir el aviso de cada nueva edición, confirma tu suscripción:</p>
    @include('correos.boton', ['enlace' => $enlaceConfirmar, 'texto' => 'Confirmar suscripción'])
    <p>El enlace vale {{ config('emprende.newsletter_horas_confirmacion') }} horas. Mientras no lo abras, tu suscripción queda pendiente de confirmación.</p>
    <p>Si tú no solicitaste esta suscripción, ignora este correo.</p>
    <p>Atentamente,<br><strong>Comité editorial de EmprendeUNAM</strong><br>Facultad de Contaduría y Administración, UNAM</p>
@endsection

@section('pie')
    <br>Si ya no deseas recibir avisos, puedes <a href="{{ $enlaceBaja }}" style="color:#664300;">darte de baja aquí</a>.
@endsection
