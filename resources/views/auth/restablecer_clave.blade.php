@extends('layouts.publico')

@section('titulo', 'Nueva contraseña')

@section('contenido')
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <h1 class="emp-titulo-seccion">Nueva contraseña</h1>

            <form method="POST" action="{{ route('password.update') }}" novalidate>
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <x-campo nombre="correo" etiqueta="Correo electrónico" tipo="email" :valor="$correo" requerido maxlength="254" autocomplete="email" />
                <x-campo nombre="clave" etiqueta="Nueva contraseña" tipo="password" requerido minlength="8" maxlength="72"
                         autocomplete="new-password" ayuda="Mínimo 8 caracteres." />
                <x-campo nombre="clave_confirmation" etiqueta="Confirma la nueva contraseña" tipo="password" requerido
                         minlength="8" maxlength="72" autocomplete="new-password" />
                <button type="submit" class="btn btn-primary">Guardar contraseña</button>
            </form>
        </div>
    </div>
@endsection
