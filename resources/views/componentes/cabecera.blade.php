{{-- Encabezado global del sistema (acceso, autor y administrador): una franja
     azul que solo lleva los logotipos de la UNAM y de la FCA, juntos y
     pegados a la izquierda. La navegación vive fuera de él. --}}
@props(['inicio'])
<header {{ $attributes->class(['emp-cab']) }}>
    <a class="emp-cab__marca" href="{{ $inicio }}" aria-label="Revista EmprendeUNAM · inicio">
        <img class="emp-cab__logo" src="{{ asset('img/sistema/unam.png') }}" width="99" height="112"
             alt="Universidad Nacional Autónoma de México">
        <img class="emp-cab__logo emp-cab__logo--fca" src="{{ asset('img/sistema/fca.png') }}" width="132" height="112"
             alt="Facultad de Contaduría y Administración">
    </a>
</header>
