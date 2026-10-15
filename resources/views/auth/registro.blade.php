@extends('layouts.publico')

@section('titulo', 'Registro de autores')

@section('contenido')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9 col-xl-8">
            <h1 class="emp-titulo-seccion">Registro de autores</h1>
            <p>Después de registrarte, el administrador revisará tu solicitud y activará tu cuenta. Te avisaremos por correo.</p>
            <p class="small">Los campos marcados con <span aria-hidden="true">*</span><span class="visually-hidden">asterisco</span> son obligatorios.</p>

            <form method="POST" action="{{ route('registro.store') }}" novalidate>
                @csrf
                <fieldset class="mb-3">
                    <legend class="h5">Datos personales</legend>
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <x-campo nombre="nombre" etiqueta="Nombre" requerido maxlength="100" autocomplete="given-name" />
                        </div>
                        <div class="col-12 col-md-4">
                            <x-campo nombre="primer_apellido" etiqueta="Primer apellido" requerido maxlength="100" autocomplete="family-name" />
                        </div>
                        <div class="col-12 col-md-4">
                            <x-campo nombre="segundo_apellido" etiqueta="Segundo apellido" maxlength="100" />
                        </div>
                        <div class="col-12 col-md-7">
                            <x-campo nombre="institucion" etiqueta="Institución" requerido maxlength="100" autocomplete="organization" />
                        </div>
                        <div class="col-12 col-md-5">
                            <x-campo nombre="grado_academico" etiqueta="Grado académico" maxlength="100"
                                     ayuda="Por ejemplo: Licenciatura, Maestría, Doctorado." />
                        </div>
                    </div>
                </fieldset>

                <fieldset class="mb-3">
                    <legend class="h5">Datos de acceso</legend>
                    <div class="row">
                        <div class="col-12 col-md-7">
                            <x-campo nombre="correo" etiqueta="Correo electrónico" tipo="email" requerido maxlength="254"
                                     autocomplete="email" ayuda="Con este correo iniciarás sesión." />
                        </div>
                        <div class="col-12 col-md-5">
                            <x-campo nombre="nombre_usuario" etiqueta="Nombre de usuario" maxlength="50" autocomplete="username"
                                     ayuda="Opcional. Si lo dejas vacío, se genera a partir de tu correo." />
                        </div>
                        <div class="col-12 col-md-6">
                            <x-campo nombre="clave" etiqueta="Contraseña" tipo="password" requerido minlength="8" maxlength="72"
                                     autocomplete="new-password" ayuda="Mínimo 8 caracteres." />
                        </div>
                        <div class="col-12 col-md-6">
                            <x-campo nombre="clave_confirmation" etiqueta="Confirma la contraseña" tipo="password" requerido
                                     minlength="8" maxlength="72" autocomplete="new-password" />
                        </div>
                    </div>
                </fieldset>

                <p class="small border-start border-3 border-primary ps-3">
                    <strong>Aviso de privacidad.</strong> Tus datos se usan únicamente para gestionar tus artículos en la
                    Revista EmprendeUNAM, conforme a la normatividad aplicable a la UNAM.
                </p>

                <button type="submit" class="btn btn-primary">Enviar registro</button>
            </form>
        </div>
    </div>
@endsection
