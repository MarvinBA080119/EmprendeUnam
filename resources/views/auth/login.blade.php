@extends('layouts.publico')

@section('titulo', 'Iniciar sesión')

@section('contenido')
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <h1 class="emp-titulo-seccion">Iniciar sesión</h1>

            <form method="POST" action="{{ route('login.store') }}" novalidate>
                @csrf
                <x-campo nombre="acceso" etiqueta="Correo o nombre de usuario" requerido
                         autocomplete="username" maxlength="254" autofocus />
                <x-campo nombre="clave" etiqueta="Contraseña" tipo="password" requerido
                         autocomplete="current-password" maxlength="255" />

                <div class="d-flex flex-wrap gap-3 align-items-center">
                    <button type="submit" class="btn btn-primary">Entrar</button>
                    <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
                </div>
            </form>

            <hr class="my-4">
            <p class="mb-0">¿Eres autor y aún no tienes cuenta? <a href="{{ route('registro') }}">Regístrate</a>.</p>
        </div>
    </div>
@endsection
