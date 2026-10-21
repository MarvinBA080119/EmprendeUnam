@extends('layouts.admin')

@section('titulo', 'Panel del administrador')

@section('contenido')
    <x-encabezado titulo="Pendientes"
                  :descripcion="ucfirst(now()->locale('es')->translatedFormat('l j \d\e F')).' · Lo más antiguo aparece primero.'">
        <x-slot:acciones>
            <a href="{{ route('admin.newsletter.index') }}#aviso-edicion" class="btn btn-secondary">Avisar nueva edición</a>
            <a href="{{ route('admin.cursos.create') }}" class="btn btn-primary"><x-icono nombre="mas" :tamano="14" /> Publicar un curso</a>
        </x-slot:acciones>
    </x-encabezado>

    <ul class="emp-metricas" aria-label="Resumen">
        <li>
            <a class="emp-metrica" href="{{ route('admin.articulos.index', ['estatus' => 'EN_REVISION']) }}">
                <span class="emp-metrica__etiqueta"><x-icono nombre="articulos" :tamano="14" /> Artículos en revisión</span>
                <span class="emp-metrica__valor emp-num">{{ $contadores['articulos_en_revision'] }}</span>
                <span class="emp-metrica__nota">{{ $contadores['articulos_en_revision'] > 0 ? 'Esperan dictamen' : 'Todo dictaminado' }}</span>
            </a>
        </li>
        <li>
            <a class="emp-metrica" href="{{ route('admin.autores.index', ['estatus' => 'pendiente']) }}">
                <span class="emp-metrica__etiqueta"><x-icono nombre="autores" :tamano="14" /> Autores por activar</span>
                <span class="emp-metrica__valor emp-num">{{ $contadores['autores_pendientes'] }}</span>
                <span class="emp-metrica__nota">{{ $contadores['autores_pendientes'] > 0 ? 'Solicitudes de registro' : 'Sin solicitudes' }}</span>
            </a>
        </li>
        <li>
            <a class="emp-metrica" href="{{ route('admin.consultorio.index', ['estatus' => 'PENDIENTE']) }}">
                <span class="emp-metrica__etiqueta"><x-icono nombre="consultorio" :tamano="14" /> Mensajes sin responder</span>
                <span class="emp-metrica__valor emp-num">{{ $contadores['mensajes_pendientes'] }}</span>
                <span class="emp-metrica__nota">{{ $contadores['mensajes_pendientes'] > 0 ? 'En el consultorio' : 'Consultorio al día' }}</span>
            </a>
        </li>
        <li>
            <a class="emp-metrica" href="{{ route('admin.newsletter.index', ['estado' => 'activo']) }}">
                <span class="emp-metrica__etiqueta"><x-icono nombre="newsletter" :tamano="14" /> Suscriptores activos</span>
                <span class="emp-metrica__valor emp-num">{{ $contadores['suscriptores_activos'] }}</span>
                <span class="emp-metrica__nota">Del newsletter</span>
            </a>
        </li>
        <li>
            <a class="emp-metrica" href="{{ route('admin.cursos.index', ['vigencia' => 'vigentes']) }}">
                <span class="emp-metrica__etiqueta"><x-icono nombre="cursos" :tamano="14" /> Cursos visibles</span>
                <span class="emp-metrica__valor emp-num">{{ $cursosVigentes }}</span>
                <span class="emp-metrica__nota">En el sitio público</span>
            </a>
        </li>
    </ul>

    <div class="emp-panel">
        <section class="emp-bandeja emp-panel__principal" aria-labelledby="bandeja-articulos">
            <header class="emp-bandeja__cabeza">
                <h2 id="bandeja-articulos" class="emp-bandeja__titulo">
                    Artículos en revisión <span class="emp-cuenta emp-cuenta--neutra emp-num">{{ $contadores['articulos_en_revision'] }}</span>
                </h2>
                <a href="{{ route('admin.articulos.index', ['estatus' => 'EN_REVISION']) }}" class="emp-bandeja__todos">Ver todos</a>
            </header>
            @forelse ($bandeja['articulos'] as $articulo)
                @if ($loop->first)<ul class="emp-bandeja__lista">@endif
                <li>
                    <a class="emp-fila" href="{{ route('admin.articulos.show', $articulo) }}">
                        <span class="emp-fila__principal">
                            <span class="emp-fila__titulo">{{ $articulo->arti_titulo }}</span>
                            <span class="emp-fila__meta">{{ $articulo->autores->first()?->persona?->nombre_completo }} · {{ $articulo->tema->tema_nombre }}</span>
                        </span>
                        <span class="emp-fila__lado">
                            <time datetime="{{ $articulo->arti_fecha_registro->toDateString() }}">{{ \App\Support\Fecha::dias($articulo->arti_fecha_registro) }}</time>
                            <x-icono nombre="flecha" :tamano="14" />
                        </span>
                    </a>
                </li>
                @if ($loop->last)</ul>@endif
            @empty
                <p class="emp-vacio">No hay artículos esperando dictamen.</p>
            @endforelse
        </section>

        <div class="emp-panel__lateral">
            <section class="emp-bandeja" aria-labelledby="bandeja-autores">
                <header class="emp-bandeja__cabeza">
                    <h2 id="bandeja-autores" class="emp-bandeja__titulo">
                        Autores pendientes de activar <span class="emp-cuenta emp-cuenta--neutra emp-num">{{ $contadores['autores_pendientes'] }}</span>
                    </h2>
                    <a href="{{ route('admin.autores.index', ['estatus' => 'pendiente']) }}" class="emp-bandeja__todos">Ver todos</a>
                </header>
                @forelse ($bandeja['autores'] as $usuario)
                    @if ($loop->first)<ul class="emp-bandeja__lista">@endif
                    <li class="emp-fila">
                        <span class="emp-fila__principal">
                            <span class="emp-fila__titulo">{{ $usuario->persona->nombre_completo }}</span>
                            <span class="emp-fila__meta">{{ $usuario->persona->autor?->auto_institucion }} · {{ \App\Support\Fecha::dias($usuario->usua_fecha_registro) }}</span>
                        </span>
                        <form method="POST" action="{{ route('admin.autores.activar', $usuario) }}"
                              data-confirmar="¿Activar la cuenta de {{ $usuario->persona->nombre_completo }}? Se le avisará por correo.">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-sm">
                                Activar<span class="visually-hidden"> la cuenta de {{ $usuario->persona->nombre_completo }}</span>
                            </button>
                        </form>
                    </li>
                    @if ($loop->last)</ul>@endif
                @empty
                    <p class="emp-vacio">No hay cuentas por activar.</p>
                @endforelse
            </section>

            <section class="emp-bandeja" aria-labelledby="bandeja-mensajes">
                <header class="emp-bandeja__cabeza">
                    <h2 id="bandeja-mensajes" class="emp-bandeja__titulo">
                        Mensajes del consultorio pendientes <span class="emp-cuenta emp-cuenta--neutra emp-num">{{ $contadores['mensajes_pendientes'] }}</span>
                    </h2>
                    <a href="{{ route('admin.consultorio.index', ['estatus' => 'PENDIENTE']) }}" class="emp-bandeja__todos">Ver todos</a>
                </header>
                @forelse ($bandeja['mensajes'] as $mensaje)
                    @if ($loop->first)<ul class="emp-bandeja__lista">@endif
                    <li>
                        <a class="emp-fila" href="{{ route('admin.consultorio.show', $mensaje) }}">
                            <span class="emp-fila__principal">
                                <span class="emp-fila__titulo">{{ $mensaje->mens_asunto }}</span>
                                <span class="emp-fila__meta">{{ $mensaje->mens_remitente }}@if ($mensaje->mens_lugar) · {{ $mensaje->mens_lugar }}@endif</span>
                            </span>
                            <span class="emp-fila__lado">
                                <time datetime="{{ $mensaje->mens_fecha_envio->toIso8601String() }}">{{ \App\Support\Fecha::relativa($mensaje->mens_fecha_envio) }}</time>
                                <x-icono nombre="flecha" :tamano="14" />
                            </span>
                        </a>
                    </li>
                    @if ($loop->last)</ul>@endif
                @empty
                    <p class="emp-vacio">No hay mensajes sin responder.</p>
                @endforelse
            </section>
        </div>
    </div>
@endsection
