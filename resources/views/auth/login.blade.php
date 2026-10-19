@extends('layouts.publico')

@section('titulo', 'Iniciar sesión')

@section('contenido')
    <div class="emp-acceso__encabezado">
        <h1 class="emp-titulo">Inicia sesión</h1>
        <p class="emp-subtitulo">Entra a tu panel de autor o de administración.</p>
    </div>

    <form method="POST" action="{{ route('login.store') }}" novalidate>
        @csrf
        <x-campo nombre="acceso" etiqueta="Correo o nombre de usuario" requerido
                 autocomplete="username" maxlength="254" autofocus />
        <x-campo nombre="clave" etiqueta="Contraseña" tipo="password" requerido
                 autocomplete="current-password" maxlength="255">
            <x-slot:accion>
                <a href="{{ route('password.request') }}">¿La olvidaste?</a>
            </x-slot:accion>
        </x-campo>

        <button type="submit" class="btn btn-primary btn-lg w-100 mt-2">Entrar</button>
    </form>

    <p class="emp-acceso__alterno">¿Aún no tienes cuenta? <a href="{{ route('registro') }}">Regístrate como autor</a></p>
@endsection
