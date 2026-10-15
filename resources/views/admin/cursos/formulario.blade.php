@extends('layouts.admin')

@php($editando = $curso->exists)

@section('titulo', $editando ? 'Editar curso' : 'Publicar curso')

@section('contenido')
    <nav aria-label="Ruta de navegación">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.cursos.index') }}">Cursos</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $editando ? 'Editar' : 'Publicar' }}</li>
        </ol>
    </nav>

    <h1 class="emp-titulo-seccion">{{ $editando ? 'Editar curso' : 'Publicar curso' }}</h1>

    <div class="row">
        <div class="col-12 col-xl-9">
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
                    <legend class="form-label fs-6 mb-1">Palabras clave <span aria-hidden="true">*</span></legend>
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
                        <div class="text-danger small mt-2" role="alert">{{ $message }}</div>
                    @enderror
                </fieldset>

                <div class="d-flex gap-3 align-items-center">
                    <button type="submit" class="btn btn-primary">{{ $editando ? 'Guardar cambios' : 'Publicar curso' }}</button>
                    <a href="{{ route('admin.cursos.index') }}">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection
