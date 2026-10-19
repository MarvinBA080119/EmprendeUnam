@extends('layouts.sitio')

{{-- Cursos vigentes (6.7): solo los que tienen fecha de hoy en adelante. --}}
@php
    $tituloPagina = 'Cursos';
    $paginaId = '';
    $recursos = ['resources/css/sitio/premium_sections.css'];
@endphp

@section('contenido')
<main id="contenido">

    <header class="head-solo">
        <div class="container">
            <h1>Cursos</h1>
        </div>
    </header>

    <div class="container pb-5">

        <p class="consultorio__entrada">
            Cursos, diplomados y talleres para fortalecer las capacidades de su empresa.
        </p>

        @if ($cursos->isEmpty())
            <p class="tem-vacio">Por ahora no hay cursos próximos. Vuelva pronto.</p>
        @else
            <aside class="curso-vip">
                <span class="curso-vip__label">Próximos cursos</span>
                <ul class="curso-vip__lista">
                    @foreach ($cursos as $curso)
                        <li>
                            <a href="{{ $curso->curs_url }}" target="_blank" rel="noopener noreferrer">
                                <span class="curso-vip__num" aria-hidden="true">{{ str_pad($cursos->firstItem() + $loop->index, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="curso-vip__nombre">
                                    {{ $curso->curs_nombre_curso }}
                                    <span class="curso-vip__detalle">
                                        {{ $curso->curs_institucion }} · {{ $curso->curs_fecha->locale('es')->translatedFormat('j \d\e F \d\e Y') }}
                                        @if ($curso->palabras->isNotEmpty())
                                            · {{ $curso->palabras->pluck('pala_descripcion')->join(', ') }}
                                        @endif
                                    </span>
                                </span>
                                <span class="curso-vip__flecha" aria-hidden="true">&rarr;</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </aside>

            <div class="mt-4">{{ $cursos->links() }}</div>
        @endif

    </div>

</main>
@endsection
