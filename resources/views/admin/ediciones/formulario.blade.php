@extends('layouts.admin')

@php($editando = $revista->exists)

@section('titulo', $editando ? 'Editar número' : 'Registrar número')

@section('contenido')
    <x-encabezado :titulo="$editando ? 'Editar el número '.$revista->revi_num_edicion : 'Registrar número'"
                  descripcion="Portada, PDF completo y enlace del Flipbook. Los artículos y el índice se eligen después."
                  :volver="$editando ? route('admin.ediciones.show', $revista) : route('admin.ediciones.index')"
                  :volver-texto="$editando ? 'Número '.$revista->revi_num_edicion : 'Portada del sitio'" />

    <div class="emp-formulario">
        <div>
            <form method="POST" enctype="multipart/form-data" novalidate
                  action="{{ $editando ? route('admin.ediciones.update', $revista) : route('admin.ediciones.store') }}">
                @csrf
                @if ($editando)
                    @method('PUT')
                @endif

                <x-campo nombre="titulo" etiqueta="Título del número" requerido maxlength="60" :valor="$revista->revi_titulo"
                         ayuda="Se muestra como titular en el inicio del sitio." />
                <div class="row">
                    <div class="col-6 col-md-3">
                        <x-campo nombre="numero" etiqueta="Número" requerido maxlength="3" inputmode="numeric" :valor="$revista->revi_num_edicion" />
                    </div>
                    <div class="col-6 col-md-3">
                        <x-campo nombre="anio" etiqueta="Año" requerido maxlength="4" inputmode="numeric" :valor="$revista->revi_anio" />
                    </div>
                    <div class="col-12 col-md-6">
                        <x-campo nombre="periodo" etiqueta="Periodo (bimestre)" requerido maxlength="20" :valor="$revista->revi_mes"
                                 placeholder="julio-agosto" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <x-campo nombre="fecha_publicacion" etiqueta="Fecha de publicación" tipo="date" requerido
                                 :valor="$revista->revi_fecha_publicacion?->format('Y-m-d')" />
                    </div>
                    <div class="col-12 col-md-6">
                        <x-campo nombre="flipbook" etiqueta="Enlace del Flipbook" tipo="url" opcional maxlength="256"
                                 :valor="$revista->revi_url_flipbook" placeholder="https://" />
                    </div>
                </div>

                <x-campo nombre="portada" etiqueta="Portada" tipo="file" :requerido="! $editando" accept="image/jpeg,image/png,image/webp"
                         :ayuda="$editando ? 'Déjalo vacío para conservar la portada actual. JPG, PNG o WebP de hasta 5 MB.' : 'JPG, PNG o WebP de hasta 5 MB.'" />
                @if ($editando)
                    <p class="form-text mt-n2 mb-3"><a href="{{ route('edicion.portada', $revista) }}" target="_blank" rel="noopener">Ver la portada actual</a></p>
                @endif

                <x-campo nombre="pdf" etiqueta="Número completo (PDF)" tipo="file" :requerido="! $editando" accept="application/pdf"
                         :ayuda="($editando ? 'Déjalo vacío para conservar el PDF actual. ' : '').'Hasta '.round(config('emprende.pdf_max_revista_kb') / 1024).' MB.'" />
                @if ($editando)
                    <p class="form-text mt-n2 mb-3"><a href="{{ route('edicion.numero', $revista) }}" target="_blank" rel="noopener">Ver el PDF actual</a></p>
                @endif

                <div class="form-check form-switch mb-4">
                    <input type="hidden" name="visible" value="0">
                    <input class="form-check-input" type="checkbox" role="switch" id="campo-visible" name="visible" value="1"
                           @checked(old('visible', $revista->revi_visible ?? false))>
                    <label class="form-check-label" for="campo-visible">Visible en el sitio</label>
                    <div class="form-text">Solo un número visible puede ponerse en portada. Ocultarlo lo quita de la portada.</div>
                </div>

                <div class="emp-formulario__acciones">
                    <a href="{{ $editando ? route('admin.ediciones.show', $revista) : route('admin.ediciones.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">{{ $editando ? 'Guardar cambios' : 'Registrar número' }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
