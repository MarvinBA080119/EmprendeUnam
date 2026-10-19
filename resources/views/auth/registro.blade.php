@extends('layouts.publico')

@section('titulo', 'Registro de autores')
@section('ancho', true)

@section('contenido')
    <div class="emp-acceso__encabezado">
        <h1 class="emp-titulo">Regístrate como autor</h1>
        <p class="emp-subtitulo">El equipo editorial revisará tu solicitud y activará tu cuenta. Te avisaremos por correo.</p>
    </div>

    <form method="POST" action="{{ route('registro.store') }}" novalidate>
        @csrf
        <fieldset class="emp-grupo">
            <legend>Datos personales</legend>
            <div class="row gx-3">
                <div class="col-12 col-sm-6">
                    <x-campo nombre="nombre" etiqueta="Nombre" requerido maxlength="100" autocomplete="given-name" />
                </div>
                <div class="col-12 col-sm-6">
                    <x-campo nombre="primer_apellido" etiqueta="Primer apellido" requerido maxlength="100" autocomplete="family-name" />
                </div>
                <div class="col-12 col-sm-6">
                    <x-campo nombre="segundo_apellido" etiqueta="Segundo apellido" opcional maxlength="100" />
                </div>
                <div class="col-12 col-sm-6">
                    <x-campo nombre="grado_academico" etiqueta="Grado académico" opcional maxlength="100"
                             placeholder="Licenciatura, Maestría, Doctorado" />
                </div>
                <div class="col-12">
                    <x-campo nombre="institucion" etiqueta="Institución" requerido maxlength="100" autocomplete="organization" />
                </div>
            </div>
        </fieldset>

        <fieldset class="emp-grupo">
            <legend>Datos de acceso</legend>
            <x-campo nombre="correo" etiqueta="Correo electrónico" tipo="email" requerido maxlength="254"
                     autocomplete="email" ayuda="Con este correo iniciarás sesión y recibirás los avisos." />
            <x-campo nombre="nombre_usuario" etiqueta="Nombre de usuario" opcional maxlength="50" autocomplete="username"
                     ayuda="Si lo dejas vacío, se genera a partir de tu correo." />
            <div class="row">
                <div class="col-12 col-sm-6">
                    <x-campo nombre="clave" etiqueta="Contraseña" tipo="password" requerido minlength="8" maxlength="72"
                             autocomplete="new-password" ayuda="Mínimo 8 caracteres." />
                </div>
                <div class="col-12 col-sm-6">
                    <x-campo nombre="clave_confirmation" etiqueta="Confírmala" tipo="password" requerido
                             minlength="8" maxlength="72" autocomplete="new-password" />
                </div>
            </div>
        </fieldset>

        <p class="form-text mb-4">
            Tus datos se usan únicamente para gestionar tus artículos en la Revista EmprendeUNAM, conforme a la
            normatividad aplicable a la UNAM.
        </p>

        <button type="submit" class="btn btn-primary btn-lg w-100">Crear cuenta</button>
    </form>

    <p class="emp-acceso__alterno">¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a></p>
@endsection
