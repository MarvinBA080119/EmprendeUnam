@extends('layouts.publico')

@section('titulo', 'Recuperar contraseña')

@section('contenido')
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <h1 class="emp-titulo-seccion">Recuperar contraseña</h1>
            <p>Escribe el correo con el que te registraste y te enviaremos un enlace para crear una nueva contraseña.</p>

            <form method="POST" action="{{ route('password.email') }}" novalidate>
                @csrf
                <x-campo nombre="correo" etiqueta="Correo electrónico" tipo="email" requerido maxlength="254" autocomplete="email" autofocus />
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    <button type="submit" class="btn btn-primary">Enviar enlace</button>
                    <a href="{{ route('login') }}">Volver a iniciar sesión</a>
                </div>
            </form>
        </div>
    </div>
@endsection
