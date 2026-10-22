@extends('layouts.admin')

@php($editando = $entrada->exists)

@section('titulo', $editando ? 'Editar artículo del número' : 'Agregar artículo al número')

@section('contenido')
    <x-encabezado :titulo="$editando ? 'Editar artículo del número' : 'Agregar artículo al número'"
                  :descripcion="'No. '.$revista->revi_num_edicion.' · '.$revista->revi_titulo"
                  :volver="route('admin.ediciones.show', $revista)" :volver-texto="'Número '.$revista->revi_num_edicion" />

    <div class="emp-formulario">
        <div>
            <form method="POST" enctype="multipart/form-data" novalidate
                  action="{{ $editando ? route('admin.ediciones.articulos.update', [$revista, $entrada]) : route('admin.ediciones.articulos.store', $revista) }}">
                @csrf
                @if ($editando)
                    @method('PUT')
                @endif

                @php($elegido = (string) old('arti_id_autor', $entrada->arti_id_autor))
                <div class="mb-3">
                    <label for="campo-arti_id_autor" class="form-label">Artículo del sistema <span class="emp-opcional">(opcional)</span></label>
                    <select id="campo-arti_id_autor" name="arti_id_autor" aria-describedby="campo-arti-ayuda"
                            @class(['form-select', 'is-invalid' => $errors->has('arti_id_autor')])>
                        <option value="">Entrada editorial (no pasó por el sistema)</option>
                        @foreach ($aprobados as $aprobado)
                            <option value="{{ $aprobado->arti_id_autor }}" @selected($elegido === (string) $aprobado->arti_id_autor)>{{ $aprobado->arti_titulo }}</option>
                        @endforeach
                    </select>
                    <div id="campo-arti-ayuda" class="form-text">
                        Si eliges un artículo aprobado, el título y los autores pueden quedar vacíos: se toman de él, y su
                        autor podrá descargar la carta de agradecimiento cuando el número sea visible.
                    </div>
                    @error('arti_id_autor')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <x-campo nombre="titulo" etiqueta="Título" maxlength="200" :valor="$entrada->arre_titulo" />
                <x-campo nombre="autores" etiqueta="Personas autoras" maxlength="300" :valor="$entrada->arre_autores"
                         ayuda="Separadas por coma; en el inicio cada una va en su renglón." />
                <div class="row">
                    <div class="col-12 col-md-8">
                        <x-campo nombre="seccion" etiqueta="Sección" opcional maxlength="60" :valor="$entrada->arre_seccion"
                                 placeholder="TALENTO HUMANO" />
                    </div>
                    <div class="col-12 col-md-4">
                        <x-campo nombre="paginas" etiqueta="Páginas" opcional maxlength="15" :valor="$entrada->arre_paginas" placeholder="7-12" />
                    </div>
                </div>
                <div class="mb-3">
                    <label for="campo-descripcion" class="form-label">Descripción breve <span class="emp-opcional">(opcional)</span></label>
                    <textarea id="campo-descripcion" name="descripcion" rows="3" maxlength="500"
                              @class(['form-control', 'is-invalid' => $errors->has('descripcion')])>{{ old('descripcion', $entrada->arre_descripcion) }}</textarea>
                    @error('descripcion')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <x-campo nombre="pdf" etiqueta="PDF del artículo" tipo="file" opcional accept="application/pdf"
                         :ayuda="($entrada->arre_url_pdf ? 'Ya tiene PDF; súbelo solo para reemplazarlo. ' : 'Sin PDF propio se abre el número completo. ').'Hasta '.round(config('emprende.pdf_max_kb') / 1024).' MB.'" />
                <x-campo nombre="imagen" etiqueta="Imagen del artículo" tipo="file" opcional accept="image/jpeg,image/png,image/webp"
                         :ayuda="($entrada->arre_url_imagen ? 'Ya tiene imagen; súbela solo para reemplazarla. ' : '').'JPG, PNG o WebP de hasta 5 MB.'" />

                <fieldset class="mb-4" aria-describedby="cursos-ayuda">
                    <legend class="form-label fs-6 mb-1">Cursos que se muestran con el artículo</legend>
                    <p id="cursos-ayuda" class="form-text mt-0">
                        Los marcados como «sugerido» se relacionan por palabras clave y título. Marca o desmarca los que
                        quieras: tu elección manda sobre la sugerencia.@unless ($editando) Las sugerencias aparecen al guardar.@endunless
                    </p>
                    @foreach ($sugeridos as $sugerido)
                        <input type="hidden" name="sugeridos[]" value="{{ $sugerido }}">
                    @endforeach
                    @forelse ($cursos as $curso)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="cursos[]" value="{{ $curso->curs_id_curso }}"
                                   id="curso-{{ $curso->curs_id_curso }}" @checked(in_array($curso->curs_id_curso, $marcados, true))>
                            <label class="form-check-label" for="curso-{{ $curso->curs_id_curso }}">
                                {{ $curso->curs_nombre_curso }} <span class="text-body-secondary">· {{ $curso->curs_institucion }}</span>
                                @if (in_array($curso->curs_id_curso, $sugeridos, true))
                                    <span class="emp-insignia emp-insignia-revision ms-1">Sugerido</span>
                                @endif
                            </label>
                        </div>
                    @empty
                        <p class="text-body-secondary mb-0">No hay cursos vigentes. Publícalos en <a href="{{ route('admin.cursos.index') }}">Cursos</a>.</p>
                    @endforelse
                    @error('cursos.*')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </fieldset>

                <div class="emp-formulario__acciones">
                    <a href="{{ route('admin.ediciones.show', $revista) }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">{{ $editando ? 'Guardar cambios' : 'Agregar al número' }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
