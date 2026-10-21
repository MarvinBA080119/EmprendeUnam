@extends('layouts.autor')

@section('titulo', 'Mis artículos')

@php $Estatus = \App\Enums\EstatusArticulo::class; @endphp

@section('contenido')
    @php
        $partes = \App\Support\Texto::lista([
            $resumen['en_revision'] > 0 ? \App\Support\Texto::cantidad($resumen['en_revision'], 'artículo en revisión', 'artículos en revisión') : null,
            $resumen['aprobados'] > 0 ? \App\Support\Texto::cantidad($resumen['aprobados'], 'aprobado', 'aprobados') : null,
            $resumen['rechazados'] > 0 ? \App\Support\Texto::cantidad($resumen['rechazados'], 'no aprobado', 'no aprobados') : null,
        ]);
    @endphp
    <header class="emp-saludo">
        <div class="emp-saludo__texto">
            <p class="emp-saludo__fecha">{{ \App\Support\Fecha::hoyLargo() }}</p>
            <h1 class="emp-saludo__titulo">{{ $nombre ? 'Hola, '.$nombre : 'Mis artículos' }}</h1>
            <p class="emp-saludo__resumen">
                @if ($resumen['total'] > 0)
                    Tienes {{ $partes }}. Te avisamos por correo cada cambio.
                @else
                    Aquí verás el estado de tus envíos y, cuando se aprueben, tus constancias.
                @endif
            </p>
        </div>
        @if ($articulos->isNotEmpty())
            <div class="emp-saludo__acciones">
                <a href="{{ route('autor.articulos.create') }}" class="btn btn-primary"><x-icono nombre="mas" :tamano="14" /> Enviar nuevo artículo</a>
            </div>
        @endif
    </header>

    @if ($articulos->isEmpty())
        <div class="emp-vacio-grande">
            <span class="emp-vacio-grande__icono" aria-hidden="true"><x-icono nombre="articulos" :tamano="22" /></span>
            <h2 class="emp-vacio-grande__titulo">Aún no has enviado artículos</h2>
            <p class="emp-vacio-grande__texto">
                Envía tu artículo en PDF con título, resumen de hasta 50 palabras, tema y cinco palabras clave.
                Aquí verás su estado y, cuando se apruebe, tu constancia.
            </p>
            <a href="{{ route('autor.articulos.create') }}" class="btn btn-primary"><x-icono nombre="mas" :tamano="14" /> Enviar mi primer artículo</a>
        </div>
    @else
        <section aria-labelledby="titulo-historial">
            <h2 id="titulo-historial" class="emp-seccion__titulo">Historial de envíos</h2>
            <div class="emp-lista">
            <ul class="emp-envios">
                @foreach ($articulos as $articulo)
                    @php $esPrincipal = (bool) $articulo->autores->first()?->pivot->auar_principal; @endphp
                    <li class="emp-envio">
                        <div class="emp-envio__principal">
                            <h2 class="emp-envio__titulo">{{ $articulo->arti_titulo }}</h2>
                            <p class="emp-envio__meta">
                                <span>{{ $articulo->tema->tema_nombre }}</span>
                                <span>{{ $esPrincipal ? 'Autor principal' : 'Coautor' }}</span>
                                <span>Enviado el <span class="emp-num">{{ $articulo->arti_fecha_registro->format('d/m/Y') }}</span></span>
                            </p>

                            @if ($articulo->arti_status === $Estatus::EN_REVISION)
                                <p class="emp-envio__nota">El equipo editorial lo está revisando. Te avisaremos por correo cuando haya dictamen.</p>
                            @elseif ($articulo->arti_status === $Estatus::RECHAZADO)
                                <div class="emp-envio__motivo">
                                    <span class="emp-envio__motivo-titulo">Motivo del dictamen</span>
                                    <p class="emp-texto-libre mb-0">{{ $articulo->arti_comentario_rechazo }}</p>
                                </div>
                            @elseif ($articulo->constancia)
                                <p class="emp-envio__nota">Constancia con folio <strong>{{ $articulo->constancia->cons_folio }}</strong>.</p>
                            @endif
                        </div>

                        <div class="emp-envio__lado">
                            <x-insignia :estatus="$articulo->arti_status" />
                            <div class="emp-envio__acciones">
                                <a href="{{ route('autor.articulos.pdf', $articulo) }}" class="btn btn-quieto btn-sm">
                                    <x-icono nombre="descargar" :tamano="14" /> <span>PDF<span class="visually-hidden"> de «{{ $articulo->arti_titulo }}»</span></span>
                                </a>
                                @if ($articulo->arti_status === $Estatus::APROBADO && $articulo->constancia)
                                    <a href="{{ route('autor.articulos.constancia', $articulo) }}" class="btn btn-secondary btn-sm">
                                        Descargar constancia<span class="visually-hidden"> de «{{ $articulo->arti_titulo }}»</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
            @if ($articulos->hasPages())
                <div class="emp-marco-tabla__pie">{{ $articulos->links() }}</div>
            @endif
            </div>
        </section>
    @endif
@endsection
