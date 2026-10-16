@extends('layouts.publico')

@section('titulo', 'Nueva contraseña')

@section('contenido')
    <div class="emp-acceso__encabezado">
        <h1 class="emp-titulo">Crea una nueva contraseña</h1>
        <p class="emp-subtitulo">Usa al menos 8 caracteres.</p>
    </div>

    <form method="POST" action="{{ route('password.update') }}" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <x-campo nombre="correo" etiqueta="Correo electrónico" tipo="email" :valor="$correo" requerido maxlength="254" autocomplete="email" />
        <x-campo nombre="clave" etiqueta="Nueva contraseña" tipo="password" requerido minlength="8" maxlength="72"
                 autocomplete="new-password" />
        <x-campo nombre="clave_confirmation" etiqueta="Confirma la nueva contraseña" tipo="password" requerido
                 minlength="8" maxlength="72" autocomplete="new-password" />
        <button type="submit" class="btn btn-primary btn-lg w-100 mt-2">Guardar contraseña</button>
    </form>

    <p class="emp-acceso__alterno"><a href="{{ route('login') }}">Volver a iniciar sesión</a></p>
@endsection
