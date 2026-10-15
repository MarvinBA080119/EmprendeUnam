@extends('layouts.admin')

@section('titulo', 'Newsletter')

@section('contenido')
    <h1 class="emp-titulo-seccion">Suscriptores del newsletter</h1>

    @php($props = [
        'urlDatos' => route('admin.newsletter.datos'),
        'urlExportar' => route('admin.newsletter.exportar'),
        'estados' => $estados,
        'filtros' => $filtros,
    ])
    <div id="tabla-suscriptores" data-props='@json($props)'>
        <noscript><div class="alert alert-warning">Esta sección necesita JavaScript.</div></noscript>
    </div>

    <section class="card emp-tarjeta mt-5" aria-labelledby="titulo-aviso">
        <div class="card-body">
            <h2 id="titulo-aviso" class="h4">Avisar nueva edición</h2>
            <p>
                Envía un correo a cada suscriptor activo ({{ $activos }}) con la liga a la nueva edición y su
                enlace de baja. El newsletter no se arma en el sistema: publícalo primero y pega aquí su liga.
            </p>
            <form method="POST" action="{{ route('admin.newsletter.avisar') }}" novalidate>
                @csrf
                <div class="row">
                    <div class="col-12 col-lg-5">
                        <x-campo nombre="titulo" etiqueta="Título de la edición" requerido maxlength="150"
                                 placeholder="Edición 02 · Noviembre 2026" />
                    </div>
                    <div class="col-12 col-lg-7">
                        <x-campo nombre="url" etiqueta="Liga al newsletter" tipo="url" requerido maxlength="256"
                                 placeholder="https://" ayuda="Debe empezar con https://" />
                    </div>
                </div>
                <div class="form-check mb-3">
                    <input id="confirmo" name="confirmo" type="checkbox" value="1"
                           @class(['form-check-input', 'is-invalid' => $errors->has('confirmo')])>
                    <label for="confirmo" class="form-check-label">
                        Confirmo que la edición ya está publicada y quiero avisar a los suscriptores activos.
                    </label>
                    @error('confirmo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary" @disabled($activos === 0)>Enviar aviso</button>
            </form>
        </div>
    </section>
@endsection
