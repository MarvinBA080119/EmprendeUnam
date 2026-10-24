@extends('layouts.admin')

@php
    $total = $articulos->count();
    $mostrados = $articulos->whereNotNull('arre_fecha_index')->count();
    $conPdf = $articulos->filter(fn ($a) => $a->rutaPdf() !== null)->count();
    $requeridos = min(\App\Models\ArticuloRevista::POSICIONES, $total);
    $porPosicion = $articulos->whereNotNull('arre_posicion_index')->keyBy('arre_posicion_index');
@endphp

@section('titulo', 'Número '.$revista->revi_num_edicion)

@section('contenido')
    <header class="emp-numero-cabeza">
        <a class="emp-encabezado__volver" href="{{ route('admin.ediciones.index') }}"><x-icono nombre="atras" :tamano="14" /> Portada del sitio</a>
        <div class="emp-numero-cabeza__fila">
            <x-portada-numero :revista="$revista" class="emp-portada--chica" />
            <div class="emp-numero-cabeza__texto">
                <p class="emp-encabezado__meta">
                    @if ($revista->revi_en_portada)
                        <span class="emp-insignia emp-insignia-aprobado">Principal</span>
                    @elseif ($revista->revi_visible)
                        <span class="emp-insignia emp-insignia-rechazado">Histórico</span>
                    @else
                        <span class="emp-insignia emp-insignia-revision">En preparación</span>
                    @endif
                    <span>No. {{ $revista->revi_num_edicion }} · {{ $revista->revi_mes }} {{ $revista->revi_anio }}</span>
                    <span>Publicado el <span class="emp-num">{{ $revista->revi_fecha_publicacion->format('d/m/Y') }}</span></span>
                </p>
                <h1 class="emp-titulo">{{ $revista->revi_titulo }}</h1>
            </div>
            <div class="emp-encabezado__acciones">
                <a href="{{ route('admin.ediciones.edit', $revista) }}" class="btn btn-secondary">Editar número</a>
                @if ($revista->revi_visible && ! $revista->revi_en_portada)
                    <form method="POST" action="{{ route('admin.ediciones.portada', $revista) }}"
                          data-confirmar="¿Hacer principal el número {{ $revista->revi_num_edicion }}? Aparecerá en el inicio del sitio y el número principal actual pasará al histórico."
                          data-confirmar-boton="Hacer principal">
                        @csrf
                        <button type="submit" class="btn btn-primary">Hacer principal</button>
                    </form>
                @elseif ($revista->revi_en_portada)
                    <a href="{{ route('inicio') }}" class="btn btn-secondary" target="_blank" rel="noopener">Ver el inicio <x-icono nombre="externo" :tamano="12" /></a>
                @endif
            </div>
        </div>
    </header>

    @error('revista')
        <div class="alert alert-danger" role="alert"><span class="alert-texto">{{ $message }}</span></div>
    @enderror
    @unless ($revista->revi_visible)
        <div class="alert alert-info" role="status">
            <span class="alert-texto">Este número está en preparación (oculto). Márcalo como visible en «Editar número» para poder hacerlo principal.</span>
        </div>
    @endunless

    {{-- Archivos del número --------------------------------------------------- --}}
    <section class="emp-seccion" aria-labelledby="titulo-archivos">
        <h2 id="titulo-archivos" class="emp-seccion__titulo">Archivos del número</h2>
        <div class="emp-archivos emp-archivos--fila">
            <x-estado-archivo nombre="Portada" :estado="$revista->revi_url_portada ? 'listo' : 'falta'" />
            <x-estado-archivo nombre="PDF completo" :estado="$revista->revi_url_revista_pdf ? 'listo' : 'falta'">
                @if ($revista->revi_url_revista_pdf)
                    <a href="{{ route('edicion.numero', $revista) }}" target="_blank" rel="noopener">Abrir <x-icono nombre="externo" :tamano="10" /></a>
                @endif
            </x-estado-archivo>
            <x-estado-archivo nombre="eBook" :estado="$revista->revi_url_flipbook ? 'listo' : 'opcional'">
                @if ($revista->revi_url_flipbook)
                    <a href="{{ $revista->revi_url_flipbook }}" target="_blank" rel="noopener">Abrir <x-icono nombre="externo" :tamano="10" /></a>
                @else
                    Sin enlace
                @endif
            </x-estado-archivo>
            <x-estado-archivo nombre="PDF de artículos" :estado="$total && $conPdf === $total ? 'listo' : 'falta'" :detalle="$conPdf.' de '.$total" />
        </div>

        <div class="emp-aviso-diseno">
            <span class="emp-archivo__icono" aria-hidden="true"><x-icono nombre="articulos" :tamano="18" /></span>
            <p class="emp-aviso-diseno__texto">
                <strong>El PDF de cada artículo llega desde la mesa de diseño.</strong>
                Al agregar un artículo aprobado, su PDF diseñado se liga solo; si aún no lo tiene, aparece en cuanto la diseñadora lo suba.
            </p>
            <a href="{{ route('admin.ediciones.manuscritos') }}" class="btn btn-secondary btn-sm">
                Manuscritos y diseños
                @if ($pendientesDeMaquetar > 0)
                    <span class="emp-num emp-boton-cuenta">{{ $pendientesDeMaquetar }}</span>
                @endif
            </a>
        </div>
    </section>

    {{-- Destacados del inicio ---------------------------------------------- --}}
    <section class="emp-seccion" aria-labelledby="titulo-destacados">
        <div class="emp-seccion__cabeza">
            <h2 id="titulo-destacados" class="emp-seccion__titulo">Destacados del inicio</h2>
            <a href="{{ route('admin.ediciones.articulos.create', $revista) }}" class="btn btn-secondary btn-sm"><x-icono nombre="mas" :tamano="14" /> Agregar artículo</a>
        </div>
        <p class="emp-seccion__texto">
            Elige {{ $requeridos === 5 ? '1 artículo principal y 4 secundarios' : 'la posición de cada artículo' }}: arrastra un
            artículo a una casilla del esquema (o entre casillas para intercambiarlos) o usa los botones de posición. Para rotarlos,
            cambia algunos por los que aún no se han mostrado.
            @if ($total > 0)
                Se han mostrado <strong class="emp-num">{{ $mostrados }} de {{ $total }}</strong>.
            @endif
        </p>

        @error('posiciones')
            <div class="alert alert-danger" role="alert"><span class="alert-texto">{{ $message }}</span></div>
        @enderror

        @if ($articulos->isEmpty())
            <p class="emp-vacio">
                Este número aún no tiene artículos.
                <a href="{{ route('admin.ediciones.articulos.create', $revista) }}">Agrega el primero</a>: elige un artículo aprobado y su PDF diseñado se liga solo.
            </p>
        @else
            <div class="emp-esquema" data-esquema aria-label="Esquema del inicio" role="group">
                @for ($p = 1; $p <= \App\Models\ArticuloRevista::POSICIONES; $p++)
                    @php $ocupa = $porPosicion->get($p); @endphp
                    <div @class(['emp-esquema__casilla', 'emp-esquema__casilla--principal' => $p === 1, 'is-vacia' => ! $ocupa]) data-casilla="{{ $p }}"
                         data-rotulo="{{ $p === 1 ? 'Principal' : 'Secundario '.($p - 1) }}">
                        <span class="emp-esquema__rotulo">{{ $p === 1 ? 'Principal' : 'Secundario '.($p - 1) }}</span>
                        <span class="emp-esquema__titulo" data-casilla-titulo>{{ $ocupa?->arre_titulo ?? 'Sin elegir' }}</span>
                        <span class="emp-esquema__cursos" data-casilla-cursos>
                            @if ($ocupa)
                                {{ $cursos[$ocupa->arre_id_articulo_revista]->count() }} {{ $cursos[$ocupa->arre_id_articulo_revista]->count() === 1 ? 'curso' : 'cursos' }}
                            @endif
                        </span>
                    </div>
                @endfor
            </div>

            <form id="form-indice" method="POST" action="{{ route('admin.ediciones.indice', $revista) }}" data-form-destacados data-requeridos="{{ $requeridos }}">
                @csrf
                <ul class="emp-numero-articulos">
                    @foreach ($articulos as $entrada)
                        @php
                            $id = $entrada->arre_id_articulo_revista;
                            $actual = (int) old("posiciones.$id", $entrada->arre_posicion_index);
                            $susCursos = $cursos[$id];
                        @endphp
                        <li class="emp-numero-articulo" data-articulo="{{ $id }}" data-titulo="{{ $entrada->arre_titulo }}"
                            data-cursos="{{ $susCursos->count() }}">
                            <div class="emp-numero-articulo__principal">
                                <span class="emp-asa" data-asa draggable="true" title="Arrastrar a una casilla del esquema" aria-hidden="true">
                                    <x-icono nombre="asa" :tamano="14" />
                                </span>
                                <span class="emp-numero-articulo__miniatura" aria-hidden="true">
                                    @if ($entrada->arre_url_imagen)
                                        <img src="{{ route('edicion.articulo.imagen', $entrada) }}" alt="" loading="lazy">
                                    @endif
                                </span>
                                <div class="emp-numero-articulo__texto">
                                    <p class="emp-numero-articulo__titulo" title="{{ $entrada->arre_titulo }}">{{ $entrada->arre_titulo }}</p>
                                    <p class="emp-numero-articulo__meta" title="{{ $entrada->arre_seccion ? $entrada->arre_seccion.' · ' : '' }}{{ $entrada->arre_autores }}">
                                        {{ $entrada->arre_seccion ? $entrada->arre_seccion.' · ' : '' }}{{ $entrada->arre_autores }}
                                    </p>
                                    <p class="emp-numero-articulo__estados">
                                        @switch ($entrada->origenPdf())
                                            @case ('diseno')
                                                <span class="emp-insignia emp-insignia-aprobado">PDF diseñado</span>
                                                @break
                                            @case ('numero')
                                                <span class="emp-insignia emp-insignia-aprobado">PDF subido</span>
                                                @break
                                            @default
                                                <span class="emp-insignia emp-insignia-revision">{{ $entrada->arti_id_autor ? 'Falta el PDF diseñado' : 'Sin PDF' }}</span>
                                        @endswitch
                                        @if ($entrada->arre_posicion_index)
                                            <span class="emp-insignia emp-insignia-aprobado">En el inicio desde {{ $entrada->arre_fecha_index->format('d/m/Y') }}</span>
                                        @elseif ($entrada->arre_fecha_index)
                                            <span class="emp-insignia emp-insignia-rechazado">Mostrado el {{ $entrada->arre_fecha_index->format('d/m/Y') }}</span>
                                        @else
                                            <span class="emp-insignia emp-insignia-revision">Pendiente de mostrar</span>
                                        @endif
                                    </p>
                                    @if ($susCursos->isNotEmpty())
                                        <ul class="emp-chips" aria-label="Cursos de «{{ $entrada->arre_titulo }}»">
                                            @foreach ($susCursos as $curso)
                                                <li class="emp-chip">{{ $curso->curs_nombre_curso }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                                <div class="emp-numero-articulo__acciones">
                                    <a href="{{ route('admin.ediciones.articulos.edit', [$revista, $entrada]) }}" class="btn btn-secondary btn-sm">
                                        Editar y cursos<span class="visually-hidden"> de «{{ $entrada->arre_titulo }}»</span>
                                    </a>
                                    <button type="submit" form="quitar-{{ $id }}" class="btn btn-quieto btn-sm">
                                        <x-icono nombre="quitar" :tamano="12" /> Quitar<span class="visually-hidden"> «{{ $entrada->arre_titulo }}»</span>
                                    </button>
                                </div>
                            </div>
                            <fieldset class="emp-posiciones">
                                <legend class="visually-hidden">Lugar de «{{ $entrada->arre_titulo }}» en el inicio</legend>
                                @foreach (['' => 'No se muestra', 1 => 'Principal', 2 => '2', 3 => '3', 4 => '4', 5 => '5'] as $valor => $texto)
                                    <input type="radio" class="emp-posiciones__opcion" name="posiciones[{{ $id }}]" value="{{ $valor }}"
                                           id="pos-{{ $id }}-{{ $valor === '' ? 'no' : $valor }}" @checked($valor === '' ? ! $actual : $actual === $valor)>
                                    <label for="pos-{{ $id }}-{{ $valor === '' ? 'no' : $valor }}">
                                        @if (is_int($valor) && $valor > 1)<span class="visually-hidden">Secundario </span>{{ $valor - 1 }}@else{{ $texto }}@endif
                                    </label>
                                @endforeach
                            </fieldset>
                        </li>
                    @endforeach
                </ul>
                <p class="visually-hidden" data-anuncio-destacados aria-live="polite"></p>
                <div class="emp-destacados-pie">
                    <p class="emp-destacados-pie__conteo" data-conteo-destacados aria-live="polite">
                        {{ $porPosicion->count() }} de {{ $requeridos }} elegidos
                    </p>
                    <button type="submit" class="btn btn-primary" data-texto-enviando="Guardando…">Guardar destacados</button>
                </div>
            </form>

            {{-- Formularios de «Quitar» fuera del de destacados (no se anidan). --}}
            @foreach ($articulos as $entrada)
                <form id="quitar-{{ $entrada->arre_id_articulo_revista }}" method="POST" hidden
                      action="{{ route('admin.ediciones.articulos.destroy', [$revista, $entrada]) }}"
                      data-confirmar="¿Quitar «{{ $entrada->arre_titulo }}» del número? Se borran su PDF maquetado, su imagen y sus cursos."
                      data-confirmar-boton="Quitar artículo" data-destructivo>
                    @csrf
                    @method('DELETE')
                </form>
            @endforeach
        @endif
    </section>

    <section class="emp-seccion" aria-labelledby="titulo-eliminar">
        <h2 id="titulo-eliminar" class="emp-seccion__titulo">Eliminar el número</h2>
        <p class="emp-seccion__texto">Se borran el número, su portada, su PDF y todos sus artículos. No se puede deshacer.</p>
        <form method="POST" action="{{ route('admin.ediciones.destroy', $revista) }}"
              data-confirmar="¿Eliminar el número {{ $revista->revi_num_edicion }}? Se borran su portada, su PDF y sus artículos. Esta acción no se puede deshacer."
              data-confirmar-boton="Eliminar número" data-destructivo>
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-quieto"><x-icono nombre="borrar" :tamano="14" /> Eliminar número</button>
        </form>
    </section>
@endsection
