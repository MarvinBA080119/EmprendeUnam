@extends('layouts.admin')

@php($editando = $curso->exists)

@section('titulo', $editando ? 'Editar curso' : 'Publicar curso')

@section('contenido')
    <x-encabezado :titulo="$editando ? 'Editar curso' : 'Publicar curso'"
                  descripcion="Se muestra en el sitio público y en el buscador hasta su fecha de inicio."
                  :volver="route('admin.cursos.index')" volver-texto="Cursos" />

    <div class="emp-formulario">
        <div>
            <form method="POST" action="{{ $editando ? route('admin.cursos.update', $curso) : route('admin.cursos.store') }}" novalidate>
                @csrf
                @if ($editando)
                    @method('PUT')
                @endif

                <x-campo nombre="nombre" etiqueta="Nombre del curso" requerido maxlength="100" :valor="$curso->curs_nombre_curso" />
                <div class="row">
                    <div class="col-12 col-md-8">
                        <x-campo nombre="institucion" etiqueta="Institución" requerido maxlength="150" :valor="$curso->curs_institucion" />
                    </div>
                    <div class="col-12 col-md-4">
                        <x-campo nombre="fecha" etiqueta="Fecha de inicio" tipo="date" requerido :valor="$curso->curs_fecha?->format('Y-m-d')"
                                 ayuda="Desde el día siguiente a esta fecha deja de mostrarse al público." />
                    </div>
                </div>
                <x-campo nombre="url" etiqueta="Enlace" tipo="url" requerido maxlength="256" :valor="$curso->curs_url"
                         placeholder="https://" ayuda="Debe empezar con https://" />

                @php($palabras = old('palabras', $curso->palabras?->pluck('pala_descripcion')->all() ?? []))
                <fieldset class="mb-3" aria-describedby="palabras-ayuda">
                    <legend class="form-label fs-6 mb-1">Palabras clave</legend>
                    <p id="palabras-ayuda" class="form-text mt-0">De 1 a 5 palabras clave distintas. Clasifican el curso en el buscador.</p>
                    <div class="row g-2">
                        @for ($i = 0; $i < \App\Http\Requests\Admin\CursoRequest::MAX_PALABRAS; $i++)
                            <div class="col-12 col-sm-6 col-lg">
                                <label for="palabra-{{ $i }}" class="visually-hidden">Palabra clave {{ $i + 1 }}</label>
                                <input id="palabra-{{ $i }}" name="palabras[]" type="text" maxlength="100"
                                       value="{{ $palabras[$i] ?? '' }}" placeholder="Palabra {{ $i + 1 }}"
                                       @class(['form-control', 'is-invalid' => $errors->has("palabras.$i")])>
                                @error("palabras.$i")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endfor
                    </div>
                    @error('palabras')
                        <div class="invalid-feedback d-block" role="alert">{{ $message }}</div>
                    @enderror
                </fieldset>

                <div class="emp-formulario__acciones">
                    <a href="{{ route('admin.cursos.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">{{ $editando ? 'Guardar cambios' : 'Publicar curso' }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
