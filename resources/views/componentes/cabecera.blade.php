{{-- Encabezado global del sistema (acceso, autor y administrador): fondo azul,
     logotipos de la UNAM y de la FCA pegados a la izquierda y, a la derecha,
     la navegación que llega en $slot.
     - inicio: a dónde llevan los logotipos.
     - menu: id del panel desplegable de la navegación en pantallas angostas
       (si se omite, la navegación se queda siempre en el renglón). --}}
@props(['inicio', 'menu' => null, 'menuEtiqueta' => 'Menú'])
<header {{ $attributes->class(['emp-cab']) }}>
    <div class="emp-cab__fila">
        <a class="emp-cab__marca" href="{{ $inicio }}">
            <img class="emp-cab__logo" src="{{ asset('img/sistema/unam.png') }}" width="99" height="112"
                 alt="Universidad Nacional Autónoma de México">
            <img class="emp-cab__logo emp-cab__logo--fca" src="{{ asset('img/sistema/fca.png') }}" width="132" height="112"
                 alt="Facultad de Contaduría y Administración">
            <span class="emp-cab__nombre">
                <span class="emp-cab__revista">Revista EmprendeUNAM</span>
                <span class="emp-cab__leyenda">Facultad de Contaduría y Administración</span>
            </span>
        </a>

        <div class="emp-cab__derecha">
            {{ $slot }}

            @if ($menu)
                <button type="button" class="emp-cab__menu d-lg-none" data-bs-toggle="offcanvas"
                        data-bs-target="#{{ $menu }}" aria-controls="{{ $menu }}">
                    <x-icono nombre="menu" :tamano="20" />
                    <span class="visually-hidden">Abrir {{ mb_strtolower($menuEtiqueta) }}</span>
                </button>
            @endif
        </div>
    </div>
</header>
