@extends('layouts.admin')

@php
    $editando = $entrada->exists;
    $elegido = (string) old('arti_id_autor', $entrada->arti_id_autor);
    $fuentePdf = old('fuente_pdf', $entrada->arre_url_pdf ? 'archivo' : 'sistema');
    $mostrados = array_values(array_unique(array_merge($sugeridos, $marcados)));
@endphp

@section('titulo', $editando ? 'Editar artículo del número' : 'Agregar artículo al número')

@section('contenido')
    <x-encabezado :titulo="$editando ? 'Editar artículo del número' : 'Agregar artículo al número'"
                  :descripcion="'No. '.$revista->revi_num_edicion.' · '.$revista->revi_titulo"
                  :volver="route('admin.ediciones.show', $revista)" :volver-texto="'Número '.$revista->revi_num_edicion" />

    <div class="emp-formulario">
        <div>
            <form method="POST" enctype="multipart/form-data" novalidate data-form-articulo-numero
                  data-url-sugerencias="{{ route('admin.ediciones.cursos-sugeridos', $revista) }}"
                  action="{{ $editando ? route('admin.ediciones.articulos.update', [$revista, $entrada]) : route('admin.ediciones.articulos.store', $revista) }}">
                @csrf
                @if ($editando)
                    @method('PUT')
                @endif

                {{-- Datos de los aprobados para llenar el formulario al elegir uno --}}
                <script type="application/json" data-datos-articulos>@json((object) $datosArticulos)</script>

                <div class="mb-3">
                    <label for="campo-arti_id_autor" class="form-label">Artículo del sistema <span class="emp-opcional">(opcional)</span></label>
                    <select id="campo-arti_id_autor" name="arti_id_autor" aria-describedby="campo-arti-ayuda campo-arti-autollenado"
                            @class(['form-select', 'is-invalid' => $errors->has('arti_id_autor')])>
                        <option value="">Entrada editorial (no pasó por el sistema)</option>
                        @foreach ($aprobados as $aprobado)
                            <option value="{{ $aprobado->arti_id_autor }}" @selected($elegido === (string) $aprobado->arti_id_autor)>
                                {{ $aprobado->arti_titulo }} — {{ $aprobado->arti_url_pdf_diseno ? 'PDF diseñado listo' : 'falta el PDF diseñado' }}
                            </option>
                        @endforeach
                    </select>
                    <div id="campo-arti-ayuda" class="form-text">
                        Al elegir un artículo aprobado se llenan el título, las personas autoras, la sección y la descripción con sus datos; puedes ajustarlos.
                    </div>
                    <p id="campo-arti-autollenado" class="emp-autollenado" data-autollenado aria-live="polite" hidden>
                        <x-icono nombre="check" :tamano="14" /> <span data-autollenado-texto></span>
                    </p>
                    @error('arti_id_autor')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <x-campo nombre="titulo" etiqueta="Título" maxlength="200" :valor="$entrada->arre_titulo" data-autollenar="titulo" />
                <x-campo nombre="autores" etiqueta="Personas autoras" maxlength="300" :valor="$entrada->arre_autores" data-autollenar="autores"
                         ayuda="Separadas por coma; en el inicio cada una va en su renglón." />
                <div class="row">
                    <div class="col-12 col-md-8">
                        <x-campo nombre="seccion" etiqueta="Sección" opcional maxlength="60" :valor="$entrada->arre_seccion" data-autollenar="seccion"
                                 placeholder="TALENTO HUMANO" />
                    </div>
                    <div class="col-12 col-md-4">
                        <x-campo nombre="paginas" etiqueta="Páginas" opcional maxlength="15" :valor="$entrada->arre_paginas" placeholder="7-12" />
                    </div>
                </div>
                <div class="mb-3">
                    <label for="campo-descripcion" class="form-label">Descripción breve <span class="emp-opcional">(opcional)</span></label>
                    <textarea id="campo-descripcion" name="descripcion" rows="3" maxlength="500" data-autollenar="descripcion"
                              @class(['form-control', 'is-invalid' => $errors->has('descripcion')])>{{ old('descripcion', $entrada->arre_descripcion) }}</textarea>
                    @error('descripcion')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- PDF: el del sistema (mesa de diseño) o uno subido para este número; nunca los dos --}}
                <fieldset class="emp-fuente-pdf mb-3" data-fuente-pdf>
                    <legend class="form-label">PDF del artículo</legend>
                    <div class="emp-fuente-pdf__opciones" data-solo-sistema @if ($elegido === '') hidden @endif>
                        <div class="emp-opcion">
                            <input class="form-check-input" type="radio" name="fuente_pdf" value="sistema" id="fuente-sistema" @checked($fuentePdf === 'sistema')>
                            <label for="fuente-sistema">
                                <span class="emp-opcion__titulo">Usar el PDF del sistema</span>
                                <span class="emp-opcion__texto" data-estado-diseno>El PDF diseñado de la mesa de diseño.</span>
                            </label>
                        </div>
                        <div class="emp-opcion">
                            <input class="form-check-input" type="radio" name="fuente_pdf" value="archivo" id="fuente-archivo" @checked($fuentePdf === 'archivo')>
                            <label for="fuente-archivo">
                                <span class="emp-opcion__titulo">Subir un PDF solo para este número</span>
                                <span class="emp-opcion__texto">Sustituye al del sistema únicamente aquí.</span>
                            </label>
                        </div>
                    </div>
                    <div class="emp-fuente-pdf__archivo" data-archivo-pdf>
                        <label for="campo-pdf" class="visually-hidden">Archivo PDF del artículo</label>
                        <input id="campo-pdf" name="pdf" type="file" accept="application/pdf" data-max-kb="{{ config('emprende.pdf_max_kb') }}"
                               aria-describedby="campo-pdf-ayuda @error('pdf') campo-pdf-error @enderror"
                               @class(['form-control', 'is-invalid' => $errors->has('pdf')])>
                        <div id="campo-pdf-ayuda" class="form-text" data-ayuda-pdf>
                            {{ $entrada->arre_url_pdf ? 'Ya tiene un PDF subido aquí; súbelo solo para reemplazarlo. ' : 'Sin ningún PDF, el enlace abre el número completo. ' }}Hasta {{ round(config('emprende.pdf_max_kb') / 1024) }} MB.
                        </div>
                        @error('pdf')
                            <div id="campo-pdf-error" class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </fieldset>

                <x-campo nombre="imagen" etiqueta="Imagen del artículo" tipo="file" opcional accept="image/jpeg,image/png,image/webp" :data-max-kb="\App\Http\Requests\Admin\NumeroRequest::IMAGEN_MAX_KB"
                         :ayuda="($entrada->arre_url_imagen ? 'Ya tiene imagen; súbela solo para reemplazarla. ' : '').'JPG, PNG o WebP de hasta 5 MB.'" />

                <fieldset class="mb-4 emp-cursos" aria-describedby="cursos-ayuda" data-cursos>
                    <legend class="form-label mb-0">Cursos que se muestran con el artículo</legend>
                    <p id="cursos-ayuda" class="form-text mt-1">
                        Se sugieren solo por coincidencias entre el título o las palabras clave del artículo y los del curso.
                        Marca o desmarca los que quieras: tu elección manda sobre la sugerencia.
                    </p>
                    <div data-sugeridos-campos>
                        @foreach ($sugeridos as $sugerido)
                            <input type="hidden" name="sugeridos[]" value="{{ $sugerido }}">
                        @endforeach
                    </div>
                    @if ($cursos->isEmpty())
                        <p class="text-body-secondary mb-0">No hay cursos vigentes. Publícalos en <a href="{{ route('admin.cursos.index') }}">Cursos</a>.</p>
                    @else
                        <div class="emp-cursos-elegir" data-fichas>
                            @foreach ($cursos->sortByDesc(fn ($c) => in_array($c->curs_id_curso, $sugeridos, true)) as $curso)
                                @php $sugerido = in_array($curso->curs_id_curso, $sugeridos, true); @endphp
                                <span class="emp-cursos-elegir__ficha" data-ficha="{{ $curso->curs_id_curso }}"
                                      data-nombre="{{ $curso->curs_nombre_curso }}" data-institucion="{{ $curso->curs_institucion }}"
                                      data-sugerido="{{ $sugerido ? '1' : '0' }}"
                                      data-mostrado="{{ in_array($curso->curs_id_curso, $mostrados, true) ? '1' : '0' }}">
                                    <input class="emp-cursos-elegir__opcion" type="checkbox" name="cursos[]" value="{{ $curso->curs_id_curso }}"
                                           id="curso-{{ $curso->curs_id_curso }}" @checked(in_array($curso->curs_id_curso, $marcados, true))>
                                    <label for="curso-{{ $curso->curs_id_curso }}" @class(['is-sugerido' => $sugerido])>
                                        <x-icono nombre="check" :tamano="12" class="emp-cursos-elegir__marca" />
                                        <x-icono nombre="mas" :tamano="12" class="emp-cursos-elegir__sumar" />
                                        <span>{{ $curso->curs_nombre_curso }}</span>
                                        <span class="emp-cursos-elegir__inst">{{ $curso->curs_institucion }}</span>
                                        <span class="emp-cursos-elegir__sugerido" @unless ($sugerido) hidden @endunless>Sugerido</span>
                                    </label>
                                </span>
                            @endforeach
                            {{-- A un lado de las fichas: elegir un curso que no se sugirió o registrar uno nuevo --}}
                            <button type="button" class="emp-cursos-elegir__agregar" data-agregar-curso hidden>
                                <x-icono nombre="mas" :tamano="12" /> Agregar otro curso
                            </button>
                        </div>
                        <p class="emp-cursos__vacio" data-sin-sugerencias hidden>
                            Ningún curso coincide con el título ni con las palabras clave. Usa «Agregar otro curso» para elegir uno.
                        </p>
                    @endif
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

    {{-- «Agregar otro curso»: buscar entre los vigentes o registrar uno nuevo.
         Va fuera del formulario del artículo para que sus campos no se envíen con él. --}}
    @if ($cursos->isNotEmpty())
        <dialog class="emp-dialogo emp-dialogo--cursos" aria-labelledby="dialogo-cursos-titulo" data-dialogo-cursos>
            <div class="emp-dialogo__cuerpo">
                <h2 id="dialogo-cursos-titulo" class="emp-dialogo__titulo">Agregar otro curso</h2>

                <div data-paso-buscar>
                    <label for="buscar-curso" class="form-label">Buscar entre los cursos vigentes</label>
                    <input id="buscar-curso" type="search" class="form-control" placeholder="Nombre o institución" autocomplete="off" data-buscar-curso>
                    <ul class="emp-lista-cursos" data-lista-cursos role="list"></ul>
                    <p class="emp-lista-cursos__vacio" data-lista-vacia hidden>Ningún curso coincide con la búsqueda.</p>
                    <div class="emp-dialogo__separador"><span>¿No está en la lista?</span></div>
                    <button type="button" class="btn btn-secondary w-100" data-mostrar-nuevo>
                        <x-icono nombre="mas" :tamano="14" /> Registrar un curso nuevo
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.cursos.store') }}" class="emp-curso-nuevo" data-form-curso novalidate hidden>
                    @csrf
                    <p class="emp-dialogo__texto mb-3">Se publica en Cursos y queda marcado para este artículo.</p>
                    <div class="mb-3">
                        <label for="nuevo-nombre" class="form-label">Nombre del curso</label>
                        <input id="nuevo-nombre" name="nombre" class="form-control" maxlength="100" required>
                    </div>
                    <div class="mb-3">
                        <label for="nuevo-institucion" class="form-label">Institución</label>
                        <input id="nuevo-institucion" name="institucion" class="form-control" maxlength="150" required>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-5 mb-3">
                            <label for="nuevo-fecha" class="form-label">Fecha de inicio</label>
                            <input id="nuevo-fecha" name="fecha" type="date" class="form-control" min="{{ today()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-12 col-sm-7 mb-3">
                            <label for="nuevo-url" class="form-label">Enlace</label>
                            <input id="nuevo-url" name="url" type="url" class="form-control" maxlength="256" placeholder="https://" required>
                        </div>
                    </div>
                    <div class="mb-1">
                        <label for="nuevo-palabras" class="form-label">Palabras clave</label>
                        <input id="nuevo-palabras" class="form-control" placeholder="finanzas, pymes, crédito" data-palabras-nuevo required>
                        <div class="form-text">De 1 a 5, separadas por coma. Ayudan a sugerirlo con otros artículos.</div>
                    </div>
                    <p class="invalid-feedback d-block" data-error-curso role="alert" hidden></p>
                    <div class="emp-dialogo__acciones">
                        <button type="button" class="btn btn-secondary" data-volver-buscar>Volver</button>
                        <button type="submit" class="btn btn-primary" data-texto-enviando="Registrando…">Registrar y agregar</button>
                    </div>
                </form>

                <div class="emp-dialogo__acciones" data-acciones-buscar>
                    <button type="button" class="btn btn-primary" data-cerrar-dialogo>Listo</button>
                </div>
            </div>
        </dialog>
    @endif
@endsection
