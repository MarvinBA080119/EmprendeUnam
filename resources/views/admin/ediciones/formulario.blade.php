@extends('layouts.admin')

@php $editando = $revista->exists; @endphp

@section('titulo', $editando ? 'Editar número' : 'Registrar número')

@section('contenido')
    <x-encabezado :titulo="$editando ? 'Editar el número '.$revista->revi_num_edicion : 'Registrar número'"
                  descripcion="Datos del número, portada, PDF completo, eBook y, si ya los tienes, los PDF maquetados de los artículos en un .zip."
                  :volver="$editando ? route('admin.ediciones.show', $revista) : route('admin.ediciones.index')"
                  :volver-texto="$editando ? 'Número '.$revista->revi_num_edicion : 'Portada del sitio'" />

    <form method="POST" enctype="multipart/form-data" novalidate class="emp-numero-form"
          action="{{ $editando ? route('admin.ediciones.update', $revista) : route('admin.ediciones.store') }}">
        @csrf
        @if ($editando)
            @method('PUT')
        @endif

        <div class="emp-numero-form__datos">
            <x-campo nombre="titulo" etiqueta="Título del número" requerido maxlength="60" :valor="$revista->revi_titulo"
                     ayuda="Es el titular del inicio del sitio." />
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
                    <x-campo nombre="flipbook" etiqueta="Enlace al eBook" tipo="url" opcional maxlength="256"
                             :valor="$revista->revi_url_flipbook" placeholder="https://" />
                </div>
            </div>

            <fieldset class="emp-grupo">
                <legend>Archivos</legend>

                @foreach ([
                    ['pdf', 'Número completo (PDF)', 'application/pdf,.pdf', (bool) $revista->revi_url_revista_pdf,
                        'Hasta '.round(config('emprende.pdf_max_revista_kb') / 1024).' MB.', ! $editando],
                    ['zip', 'PDF maquetados de los artículos (.zip)', '.zip,application/zip', false,
                        'Opcional. Si cada PDF empieza con el número del artículo (como en «Manuscritos aprobados»), se vincula solo.', false],
                ] as [$campo, $etiqueta, $acepta, $guardado, $ayuda, $requerido])
                    <div class="mb-3">
                        <div class="emp-etiqueta-fila">
                            <label for="campo-{{ $campo }}" class="form-label">
                                {{ $etiqueta }}@unless ($requerido) <span class="emp-opcional">(opcional)</span>@endunless
                            </label>
                            <span class="emp-insignia {{ $guardado ? 'emp-insignia-aprobado' : ($requerido ? 'emp-insignia-revision' : 'emp-insignia-rechazado') }}"
                                  data-estado-de="campo-{{ $campo }}"
                                  data-texto-nuevo="Por subir">{{ $guardado ? 'Guardado' : ($requerido ? 'Falta' : 'Sin archivo') }}</span>
                        </div>
                        <input id="campo-{{ $campo }}" name="{{ $campo }}" type="file" accept="{{ $acepta }}"
                               aria-describedby="campo-{{ $campo }}-ayuda @error($campo) campo-{{ $campo }}-error @enderror"
                               @class(['form-control', 'is-invalid' => $errors->has($campo)]) data-archivo-estado>
                        <div id="campo-{{ $campo }}-ayuda" class="form-text" data-archivo-detalle>{{ $ayuda }}</div>
                        @error($campo)
                            <div id="campo-{{ $campo }}-error" class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        @if ($guardado && $campo === 'pdf')
                            <a class="form-text d-inline-block" href="{{ route('edicion.numero', $revista) }}" target="_blank" rel="noopener">Ver el PDF guardado</a>
                        @endif
                    </div>
                @endforeach
            </fieldset>

            <div class="form-check form-switch mb-4">
                <input type="hidden" name="visible" value="0">
                <input class="form-check-input" type="checkbox" role="switch" id="campo-visible" name="visible" value="1"
                       @checked(old('visible', $revista->revi_visible ?? false))>
                <label class="form-check-label" for="campo-visible">Visible en el sitio</label>
                <div class="form-text">Solo un número visible puede ser el principal. Ocultarlo lo quita del inicio y del histórico.</div>
            </div>

            <div class="emp-formulario__acciones">
                <a href="{{ $editando ? route('admin.ediciones.show', $revista) : route('admin.ediciones.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary" data-texto-enviando="Subiendo archivos…">{{ $editando ? 'Guardar cambios' : 'Registrar número' }}</button>
            </div>
        </div>

        {{-- Portada con vista previa inmediata (resources/js/gestor.js). --}}
        <aside class="emp-numero-form__portada">
            <label for="campo-portada" class="form-label">
                Portada
                @unless ($editando)
                    <span class="visually-hidden">(obligatoria)</span>
                @endunless
            </label>
            <label for="campo-portada" @class(['emp-portada-carga', 'tiene-imagen' => $editando && $revista->revi_url_portada, 'is-invalid' => $errors->has('portada')])
                   data-vista-portada>
                @if ($editando && $revista->revi_url_portada)
                    <img src="{{ route('edicion.portada', $revista) }}" alt="Portada actual del número {{ $revista->revi_num_edicion }}">
                @else
                    <img alt="" hidden>
                @endif
                <span class="emp-portada-carga__vacia">
                    <x-icono nombre="portada" :tamano="22" />
                    <strong>Elige la portada</strong>
                    <span>JPG, PNG o WebP de hasta 5 MB</span>
                </span>
                <span class="emp-portada-carga__cambiar">Cambiar portada</span>
            </label>
            <input id="campo-portada" name="portada" type="file" accept="image/jpeg,image/png,image/webp" class="visually-hidden"
                   aria-describedby="portada-estado @error('portada') portada-error @enderror" @required(! $editando)>
            <p id="portada-estado" class="form-text" data-portada-estado>
                {{ $editando && $revista->revi_url_portada ? 'Portada guardada. Elige otra para reemplazarla.' : 'Aún no hay portada.' }}
            </p>
            @error('portada')
                <div id="portada-error" class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </aside>
    </form>
@endsection
