@extends('layouts.admin')

@section('titulo', 'Portada del sitio')

@section('contenido')
    <x-encabezado titulo="Portada del sitio"
                  descripcion="El número principal aparece en el inicio del sitio con sus 5 destacados; al cambiarlo, el anterior pasa al histórico.">
        <x-slot:acciones>
            @if ($pendientesDeMaquetar > 0)
                <a href="{{ route('admin.ediciones.manuscritos') }}" class="btn btn-secondary">
                    <x-icono nombre="descargar" :tamano="14" /> Manuscritos aprobados
                    <span class="emp-num emp-boton-cuenta">{{ $pendientesDeMaquetar }}</span>
                </a>
            @endif
            <a href="{{ route('admin.ediciones.create') }}" class="btn btn-primary"><x-icono nombre="mas" :tamano="14" /> Registrar número</a>
        </x-slot:acciones>
    </x-encabezado>

    @if (! $principal && $otros->isEmpty())
        <div class="emp-vacio-grande">
            <span class="emp-vacio-grande__icono" aria-hidden="true"><x-icono nombre="portada" :tamano="22" /></span>
            <h2 class="emp-vacio-grande__titulo">Aún no hay números registrados</h2>
            <p class="emp-vacio-grande__texto">
                Registra el número con su portada, el PDF completo y el enlace al eBook. Mientras tanto, el inicio del
                sitio muestra el contenido fijo de la versión anterior.
            </p>
            <a href="{{ route('admin.ediciones.create') }}" class="btn btn-primary"><x-icono nombre="mas" :tamano="14" /> Registrar el primer número</a>
        </div>
    @else
        <section class="emp-seccion" aria-labelledby="titulo-principal">
            <h2 id="titulo-principal" class="emp-seccion__titulo">Número principal</h2>
            @if ($principal)
                <article class="emp-principal">
                    <a href="{{ route('admin.ediciones.show', $principal) }}" class="emp-principal__portada" tabindex="-1" aria-hidden="true">
                        <x-portada-numero :revista="$principal" />
                    </a>
                    <div class="emp-principal__cuerpo">
                        <p class="emp-principal__meta">
                            <span class="emp-insignia emp-insignia-aprobado">Principal</span>
                            <span>No. {{ $principal->revi_num_edicion }} · {{ $principal->revi_mes }} {{ $principal->revi_anio }}</span>
                        </p>
                        <h3 class="emp-principal__titulo">
                            <a href="{{ route('admin.ediciones.show', $principal) }}">{{ $principal->revi_titulo }}</a>
                        </h3>
                        <div class="emp-archivos">
                            <x-estado-archivo nombre="Portada" :estado="$principal->revi_url_portada ? 'listo' : 'falta'" />
                            <x-estado-archivo nombre="PDF completo" :estado="$principal->revi_url_revista_pdf ? 'listo' : 'falta'" />
                            <x-estado-archivo nombre="eBook" :estado="$principal->revi_url_flipbook ? 'listo' : 'opcional'" />
                            <x-estado-archivo nombre="PDF de artículos" :estado="$principal->articulos_count && $principal->con_pdf_count === $principal->articulos_count ? 'listo' : 'falta'"
                                              :detalle="$principal->con_pdf_count.' de '.$principal->articulos_count" />
                            <x-estado-archivo nombre="Destacados" :estado="$principal->en_indice_count >= min(5, max(1, $principal->articulos_count)) ? 'listo' : 'falta'"
                                              :detalle="$principal->en_indice_count.' de 5'" />
                        </div>
                        <div class="emp-principal__acciones">
                            <a href="{{ route('admin.ediciones.show', $principal) }}" class="btn btn-primary">Destacados y cursos</a>
                            <a href="{{ route('inicio') }}" class="btn btn-secondary" target="_blank" rel="noopener">
                                Ver el inicio <x-icono nombre="externo" :tamano="12" />
                            </a>
                        </div>
                    </div>
                </article>
            @else
                <p class="emp-vacio">
                    Ningún número es el principal: el inicio del sitio muestra el contenido anterior. Abre un número visible
                    y elige «Hacer principal».
                </p>
            @endif
        </section>

        @if ($otros->isNotEmpty())
            <section class="emp-seccion" aria-labelledby="titulo-otros">
                <h2 id="titulo-otros" class="emp-seccion__titulo">Histórico y en preparación</h2>
                <ul class="emp-galeria">
                    @foreach ($otros as $numero)
                        <li class="emp-galeria__item">
                            <a href="{{ route('admin.ediciones.show', $numero) }}" class="emp-galeria__enlace">
                                <x-portada-numero :revista="$numero" />
                                <span class="emp-galeria__numero">No. {{ $numero->revi_num_edicion }}</span>
                                <span class="emp-galeria__titulo">{{ $numero->revi_titulo }}</span>
                                <span class="emp-galeria__meta">
                                    @if ($numero->revi_visible)
                                        <span class="emp-insignia emp-insignia-rechazado">Histórico</span>
                                    @else
                                        <span class="emp-insignia emp-insignia-revision">En preparación</span>
                                    @endif
                                    <span class="emp-num">{{ $numero->revi_mes }} {{ $numero->revi_anio }}</span>
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif
    @endif
@endsection
