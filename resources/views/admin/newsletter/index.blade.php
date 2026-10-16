@extends('layouts.admin')

@section('titulo', 'Newsletter')

@section('contenido')
    <x-encabezado titulo="Newsletter" descripcion="Suscriptores y aviso de nueva edición." />

    @php($props = [
        'urlDatos' => route('admin.newsletter.datos'),
        'urlExportar' => route('admin.newsletter.exportar'),
        'estados' => $estados,
        'filtros' => $filtros,
    ])
    <div id="tabla-suscriptores" data-props='@json($props)'>
        <noscript><div class="alert alert-warning">Esta sección necesita JavaScript.</div></noscript>
    </div>

    <section class="emp-ajuste" id="aviso-edicion" aria-labelledby="titulo-aviso">
        <div class="emp-ajuste__texto">
            <h2 id="titulo-aviso" class="emp-ajuste__titulo">Avisar nueva edición</h2>
            <p>
                Envía un correo a cada suscriptor activo con la liga a la nueva edición y su enlace de baja.
                El newsletter no se arma en el sistema: publícalo primero y pega aquí su liga.
            </p>
            <p class="emp-ajuste__dato"><span class="emp-num">{{ $activos }}</span> {{ $activos === 1 ? 'suscriptor activo recibirá' : 'suscriptores activos recibirán' }} el aviso.</p>
        </div>
        <form class="emp-ajuste__form" method="POST" action="{{ route('admin.newsletter.avisar') }}" novalidate>
            @csrf
            <x-campo nombre="titulo" etiqueta="Título de la edición" requerido maxlength="150"
                     placeholder="Edición 02 · Noviembre 2026" />
            <x-campo nombre="url" etiqueta="Liga al newsletter" tipo="url" requerido maxlength="256"
                     placeholder="https://" ayuda="Debe empezar con https://" />
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
            <div class="emp-ajuste__acciones">
                <button type="submit" class="btn btn-primary" @disabled($activos === 0)>Enviar aviso</button>
            </div>
        </form>
    </section>
@endsection
