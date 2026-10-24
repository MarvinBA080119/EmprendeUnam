@extends('layouts.admin')

@section('titulo', 'Flipbooks')

@php
    $maxMb = round(config('emprende.pdf_max_revista_kb') / 1024);
@endphp

@section('contenido')
    <x-encabezado titulo="Flipbooks"
                  descripcion="Convierte el PDF completo de un número en una revista digital que se hojea. Al terminar queda disponible como eBook del número en «Portada del sitio»." />

    {{-- Generador: datos a la izquierda, avance y vista previa a la derecha --}}
    <section class="emp-generador" aria-labelledby="titulo-generar" data-generador
             data-en-curso="{{ $enCurso }}" data-url-estado="{{ route('admin.flipbooks.estado', '__N__') }}"
             data-url-miniatura="{{ route('admin.flipbooks.miniatura', ['__N__', '__P__']) }}">
        <h2 id="titulo-generar" class="visually-hidden">Generar un flipbook</h2>

        <form method="POST" action="{{ route('admin.flipbooks.store') }}" enctype="multipart/form-data"
              class="emp-generador__datos" data-form-flipbook data-sin-carga novalidate>
            @csrf
            <div class="mb-3">
                <label for="flip-numero" class="form-label">Número de edición</label>
                <div class="emp-numero-edicion">
                    <span class="emp-numero-edicion__prefijo" aria-hidden="true">No.</span>
                    <input id="flip-numero" name="numero" class="form-control emp-num @error('numero') is-invalid @enderror"
                           inputmode="numeric" maxlength="4" required autocomplete="off" list="flip-numeros"
                           value="{{ old('numero', $numeros->first()?->revi_num_edicion) }}" aria-describedby="flip-numero-ayuda">
                    <datalist id="flip-numeros">
                        @foreach ($numeros as $numero)
                            <option value="{{ $numero->revi_num_edicion }}">{{ $numero->revi_titulo }}</option>
                        @endforeach
                    </datalist>
                </div>
                <div id="flip-numero-ayuda" class="form-text">Si el número ya existe en el gestor y no tiene eBook, queda ligado solo.</div>
                @error('numero')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <span class="form-label d-block">PDF completo de la revista</span>
                <input id="flip-pdf" name="pdf" type="file" accept="application/pdf,.pdf" class="emp-mesa__archivo" required
                       data-max-kb="{{ config('emprende.pdf_max_revista_kb') }}" aria-describedby="flip-pdf-detalle">
                <label for="flip-pdf" class="emp-carga @error('pdf') is-invalid @enderror" data-zona-pdf>
                    <span class="emp-carga__hoja" aria-hidden="true"><x-icono nombre="portada" :tamano="20" /></span>
                    <span class="emp-carga__texto">
                        <strong data-zona-nombre>Arrastra el PDF aquí o elige el archivo</strong>
                        <span id="flip-pdf-detalle" data-zona-detalle>Hasta {{ $maxMb }} MB. Cada página se convierte en una hoja del flipbook, en orden.</span>
                    </span>
                </label>
                @error('pdf')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <p class="invalid-feedback" data-error-flipbook role="alert" hidden></p>

            <button type="submit" class="btn btn-primary w-100" data-boton-generar>
                <x-icono nombre="libro" :tamano="16" /> Generar flipbook
            </button>
        </form>

        <div class="emp-generador__escena" data-escena aria-live="polite">
            {{-- Pasos: indican qué está pasando y cuánto falta --}}
            <ol class="emp-pasos-flip" data-pasos>
                <li data-paso="subir">
                    <span class="emp-pasos-flip__marca" aria-hidden="true"><x-icono nombre="check" :tamano="12" /></span>
                    <span class="emp-pasos-flip__nombre">Subir el PDF</span>
                    <span class="emp-pasos-flip__detalle" data-detalle="subir">Se envía al servidor.</span>
                </li>
                <li data-paso="convertir">
                    <span class="emp-pasos-flip__marca" aria-hidden="true"><x-icono nombre="check" :tamano="12" /></span>
                    <span class="emp-pasos-flip__nombre">Convertir las páginas</span>
                    <span class="emp-pasos-flip__detalle" data-detalle="convertir">Cada página se vuelve una imagen numerada: 1.jpg, 2.jpg…</span>
                </li>
                <li data-paso="armar">
                    <span class="emp-pasos-flip__marca" aria-hidden="true"><x-icono nombre="check" :tamano="12" /></span>
                    <span class="emp-pasos-flip__nombre">Armar el flipbook</span>
                    <span class="emp-pasos-flip__detalle" data-detalle="armar">Visor, índice del número y miniaturas.</span>
                </li>
            </ol>

            <div class="emp-progreso" data-progreso hidden>
                <div class="emp-progreso__fila">
                    <span class="emp-progreso__texto" data-progreso-texto>Preparando…</span>
                    <span class="emp-progreso__cifra emp-num" data-progreso-cifra>0 %</span>
                </div>
                <span class="emp-progreso__barra" role="progressbar" aria-label="Avance" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-progreso-barra>
                    <span></span>
                </span>
            </div>

            <ul class="emp-tira-hojas" data-tira aria-label="Páginas convertidas"></ul>

            <div class="emp-vista-flip" data-vista hidden>
                <div class="emp-vista-flip__marco">
                    <iframe title="Vista previa del flipbook" data-vista-marco loading="lazy"></iframe>
                </div>
                <div class="emp-vista-flip__acciones">
                    <p class="emp-vista-flip__nota" data-vista-nota></p>
                    <a href="#" class="btn btn-secondary btn-sm" target="_blank" rel="noopener" data-vista-abrir>
                        Abrir en otra pestaña <x-icono nombre="externo" :tamano="12" />
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Flipbooks generados ---------------------------------------------------- --}}
    <section class="emp-seccion" aria-labelledby="titulo-generados" data-galeria>
        <div class="emp-seccion__cabeza">
            <h2 id="titulo-generados" class="emp-seccion__titulo">Generados</h2>
        </div>

        @if ($flipbooks->isEmpty())
            <p class="emp-vacio">Todavía no hay flipbooks. Sube el PDF de un número para generar el primero.</p>
        @else
            <ul class="emp-flipbooks">
                @foreach ($flipbooks as $flipbook)
                    @php
                        $usos = $flipbook['usado_en'];
                        $mismoNumero = $numeros->firstWhere('revi_num_edicion', $flipbook['numero']);
                    @endphp
                    <li class="emp-flipbook">
                        <a class="emp-flipbook__portada" href="{{ $flipbook['ruta'] }}" target="_blank" rel="noopener">
                            <img src="{{ $flipbook['miniatura'] }}" alt="" loading="lazy">
                            <span class="emp-flipbook__ver"><x-icono nombre="ojo" :tamano="14" /> Ver</span>
                            <span class="visually-hidden">Ver el flipbook del número {{ $flipbook['numero'] }}</span>
                        </a>
                        <div class="emp-flipbook__datos">
                            <p class="emp-flipbook__numero">No. <span class="emp-num">{{ $flipbook['numero'] }}</span></p>
                            <p class="emp-flipbook__meta">
                                <span class="emp-num">{{ $flipbook['paginas'] }}</span> páginas ·
                                <span class="emp-num">{{ \Illuminate\Support\Carbon::parse($flipbook['generado'])->format('d/m/Y') }}</span>
                            </p>
                            @if ($usos->isNotEmpty())
                                @foreach ($usos as $uso)
                                    <span class="emp-insignia emp-insignia-aprobado">eBook del No. {{ $uso->revi_num_edicion }}</span>
                                @endforeach
                            @else
                                <span class="emp-insignia emp-insignia-rechazado">Sin usar</span>
                            @endif
                        </div>
                        <div class="emp-flipbook__acciones">
                            @if ($mismoNumero && $usos->doesntContain('revi_id_revista', $mismoNumero->revi_id_revista))
                                <form method="POST" action="{{ route('admin.flipbooks.usar', $flipbook['numero']) }}">
                                    @csrf
                                    <input type="hidden" name="revista" value="{{ $mismoNumero->revi_id_revista }}">
                                    <button type="submit" class="btn btn-secondary btn-sm">Usar en el No. {{ $mismoNumero->revi_num_edicion }}</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.flipbooks.indice', $flipbook['numero']) }}">
                                @csrf
                                <button type="submit" class="btn btn-quieto btn-sm" title="Vuelve a tomar los artículos y páginas del gestor sin convertir de nuevo">Actualizar índice</button>
                            </form>
                            <form method="POST" action="{{ route('admin.flipbooks.destroy', $flipbook['numero']) }}"
                                  data-confirmar="¿Eliminar el flipbook del número {{ $flipbook['numero'] }}? Se borran sus imágenes y los números que lo usan quedan sin eBook."
                                  data-confirmar-boton="Eliminar flipbook" data-destructivo>
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-quieto btn-sm"><x-icono nombre="borrar" :tamano="12" /> Eliminar</button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
@endsection
