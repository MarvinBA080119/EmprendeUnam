@extends('layouts.publico')

@section('titulo', 'Recuperar contraseña')

@section('contenido')
    <div class="emp-acceso__encabezado">
        <h1 class="emp-titulo">Recupera tu contraseña</h1>
        <p class="emp-subtitulo">Escribe el correo con el que te registraste y te enviaremos un enlace para crear una nueva.</p>
    </div>

    <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf
        <x-campo nombre="correo" etiqueta="Correo electrónico" tipo="email" requerido maxlength="254" autocomplete="email" autofocus />
        <button type="submit" class="btn btn-primary btn-lg w-100 mt-2">Enviar enlace</button>
    </form>

    <p class="emp-acceso__alterno"><a href="{{ route('login') }}">Volver a iniciar sesión</a></p>
@endsection
