@extends('layouts.base')

@section('cuerpo')
    <div class="container-fluid flex-grow-1 d-flex flex-column">
        <div class="row flex-grow-1">
            <aside class="col-12 col-lg-3 col-xl-2 emp-lateral py-3" aria-label="Secciones del administrador">
                <p class="small text-uppercase fw-semibold mb-2 px-3 opacity-75">Administración</p>
                <ul class="nav flex-column">
                    <x-enlace-lateral ruta="admin.panel" activa="admin.panel">Panel</x-enlace-lateral>
                    <x-enlace-lateral ruta="admin.autores.index" activa="admin.autores.*">Autores</x-enlace-lateral>
                    <x-enlace-lateral ruta="admin.articulos.index" activa="admin.articulos.*">Artículos</x-enlace-lateral>
                    <x-enlace-lateral ruta="admin.consultorio.index" activa="admin.consultorio.*">Consultorio</x-enlace-lateral>
                    <x-enlace-lateral ruta="admin.cursos.index" activa="admin.cursos.*">Cursos</x-enlace-lateral>
                    <x-enlace-lateral ruta="admin.newsletter.index" activa="admin.newsletter.*">Newsletter</x-enlace-lateral>
                </ul>
            </aside>
            <main id="contenido" class="col-12 col-lg-9 col-xl-10 py-4 px-3 px-lg-4">
                <x-alertas />
                @yield('contenido')
            </main>
        </div>
    </div>
@endsection
