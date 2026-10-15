@extends('layouts.sitio')

@php
    $tituloPagina = 'Lineamientos para autores';
    $paginaId = '';
    $recursos = ['resources/css/sitio/institucional_suizo.css', 'resources/js/sitio/interacciones.js'];
@endphp

@section('contenido')
<main id="contenido" class="suizo">

    <!-- ══════════════ ENCABEZADO ASIMÉTRICO ══════════════ -->
    <div class="container">
        <div class="row" style="padding: 5rem 0 3.5rem;">
            <div class="col-lg-7">
                <p class="rotulo">Para autores</p>
                <h1 class="titulo-gigante">Lineamientos <em>editoriales</em></h1>
                <div class="filete-rojo"></div>
            </div>
        </div>
    </div>

    <!-- ══════════════ SUB-NAVBAR NEGRA ══════════════ -->
    <nav>
        <div class="nav nav-tabs subnav" id="menuNormas" role="tablist">
            @foreach ($secciones as $i => $sec)
                <button @class(['nav-link', 'active' => $i === 0])
                        id="btn-{{ $sec['id'] }}"
                        data-bs-toggle="tab"
                        data-bs-target="#panel-{{ $sec['id'] }}"
                        type="button" role="tab"
                        aria-controls="panel-{{ $sec['id'] }}"
                        aria-selected="{{ $i === 0 ? 'true' : 'false' }}">
                    <span class="num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    {{ $sec['label'] }}
                </button>
            @endforeach
        </div>
    </nav>

    <!-- ══════════════ CONTENIDO ══════════════ -->
    <div class="tab-content" id="contenidoNormas">

        <!-- 01 · ENFOQUE Y TEMÁTICAS -->
        <div class="tab-pane fade show active" id="panel-enfoque"
             role="tabpanel" aria-labelledby="btn-enfoque" tabindex="0">
            <div class="container">
                <div class="panel">

                    <h2>Enfoque y temáticas</h2>

                    {{-- DOS COLUMNAS EXPLÍCITAS, NO FLUJO MULTICOLUMNA
                             ---------------------------------------------------
                             Antes todo el panel vivía dentro de .periodico, que
                             es un column-count: 2 de CSS. Con flujo automático
                             el rótulo "Áreas temáticas" caía donde alcanzara el
                             corte —normalmente a media columna izquierda, con la
                             lista partida entre las dos—, y la coordinación
                             editorial pidió que encabece la columna derecha.

                             Con una rejilla de Bootstrap el reparto es fijo: la
                             prosa a la izquierda, el rótulo y la lista completa
                             a la derecha. En móvil las columnas se apilan y la
                             lista queda al final, que es su orden de lectura. --}}
                    <div class="row g-5">

                        <div class="col-lg-6">

                            <p>
                                <strong>Emprendedores</strong> tiene como objetivo presentar temas de actualidad e
                                interés mediante la publicación de artículos breves que contribuyan a mejorar el
                                desempeño de las micro, pequeñas y medianas empresas. Exponemos las tendencias e
                                innovaciones en la gestión de negocios, sus fundamentos teóricos y la aplicación
                                en las organizaciones.
                            </p>

                            <p>
                                Los artículos deben ser inéditos, mostrar un lenguaje sencillo, claro y ordenado,
                                desprovisto de tecnicismos innecesarios, expresiones rebuscadas o párrafos densos.
                                Cada artículo deberá incluir cinco palabras clave que permitan identificar y
                                describir su contenido; así como un resumen de 50 palabras.
                            </p>

                            <p>
                                Nuestros lectores son personas que desean emprender un negocio y que cuentan con
                                estudios profesionales, no necesariamente de administración; propietarios o
                                colaboradores de Mipyme que necesitan mejorar su desempeño, y estudiantes de las
                                disciplinas económico-administrativas interesados en conocer la actividad
                                emprendedora en cualquier parte del mundo.
                            </p>

                        </div>

                        <div class="col-lg-6">

                            <p class="panel__sub panel__sub--cabeza">Áreas temáticas</p>

                            <ul class="lista-suiza">
                                @foreach ($areas_tematicas as $area)
                                    <li>{!! $area !!}</li>
                                @endforeach
                            </ul>

                        </div>

                    </div>

                </div>
            </div>
        </div>

        <!-- 02 · ESPECIFICACIONES -->
        <div class="tab-pane fade" id="panel-documento"
             role="tabpanel" aria-labelledby="btn-documento" tabindex="0">
            <div class="container">
                <div class="panel">

                    <h2>Especificaciones del documento</h2>

                    <div class="periodico">

                        <p class="panel__sub">De la autoría</p>

                        <p>Podrán escribir hasta <strong>dos personas autoras</strong> por artículo. Se debe incluir:</p>

                        <ul class="lista-suiza">
                            <li>Título sugerido. Procure que sea breve y atractivo (el editor tendrá la facultad de modificar títulos largos o confusos previa autorización).</li>
                            <li>Colocar el nombre completo de las personas autoras inmediatamente después del título.</li>
                            <li>Al final del texto: grado académico superior, área de desempeño principal, institución donde labora y correo electrónico.</li>
                        </ul>

                        <p class="panel__sub">Extensión y formato</p>

                        <ul class="lista-suiza">
                            <li><strong>Extensión:</strong> mínima de 5 y máxima de 7 cuartillas (tamaño carta).</li>
                            <li><strong>Márgenes:</strong> 2.5 cm (superior e inferior) y 3 cm (izquierdo y derecho).</li>
                            <li><strong>Tipografía:</strong> archivo en Word, fuente Arial a 12 puntos, interlineado 1.5.</li>
                            <li><strong>Paginación:</strong> foliadas en la esquina inferior derecha.</li>
                            <li><strong>Material gráfico:</strong> si incluye imágenes o tablas, citar fuente y adjuntar el archivo original (Excel, JPG, etc.).</li>
                        </ul>

                    </div>

                </div>
            </div>
        </div>

        <!-- 03 · ENTREGA Y DERECHOS -->
        <div class="tab-pane fade" id="panel-entrega"
             role="tabpanel" aria-labelledby="btn-entrega" tabindex="0">
            <div class="container">
                <div class="panel">

                    <h2>Entrega, selección y derechos</h2>

                    <div class="periodico">

                        <p class="panel__sub">Envío de artículos</p>
                        <p>
                            Los artículos se envían a través del
                            <a href="{{ route('envio-articulos') }}">formulario para el envío de artículos</a>.
                        </p>

                        <p class="panel__sub">Selección y exclusividad</p>
                        <p>
                            El envío implica el compromiso de no someterlo simultáneamente a otras
                            publicaciones. Los trabajos se seleccionarán para integrar el número a editar.
                            No se asume compromiso de publicación si no se cumple con la calidad y perfil
                            de la revista.
                        </p>

                        <p class="panel__sub">Cesión de derechos</p>
                        <p>
                            Si el artículo es seleccionado, las personas autoras deberán firmar una carta de
                            cesión de derechos a favor de la UNAM y de responsabilidad por el contenido, la
                            cual deberá regresar firmada en un plazo de tres días hábiles.
                        </p>

                        <p class="panel__sub">Importante</p>
                        <p>
                            Todos los trabajos que integren el cuerpo de la publicación deberán sujetarse a
                            estas normas.
                        </p>

                    </div>

                </div>
            </div>
        </div>

        <!-- 04 · CITAS Y REFERENCIAS -->
        <div class="tab-pane fade" id="panel-referencias"
             role="tabpanel" aria-labelledby="btn-referencias" tabindex="0">
            <div class="container">
                <div class="panel">

                    <h2>Citas y referencias (Chicago 18ª ed.)</h2>

                    {{-- La tabla ocupa el ancho completo: dos columnas la partirían --}}
                    <p style="max-width: 68ch;">
                        Las pautas editoriales de la revista <strong>EmprendeUNAM</strong> se basan en la
                        18ª edición de <em>The Chicago Manual of Style</em>. Los siguientes ejemplos
                        ilustran el sistema «autor-fecha» de dicho manual.
                    </p>

                    <div class="table-responsive" style="margin-top: 2.5rem;">
                        <table class="tabla-suiza">
                            <thead>
                                <tr>
                                    <th scope="col">Tipo de fuente</th>
                                    <th scope="col">Estructura y ejemplo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($referencias as $ref)
                                    <tr>
                                        <td>{!! $ref['tipo'] !!}</td>
                                        <td>
                                            {{-- La nota precede a la estructura porque en el caso
                                                     de los sitios web explica cuándo hace falta la
                                                     ficha y cuándo basta con mencionarlos en el texto. --}}
                                            @if (!empty($ref['nota']))
                                                <span class="nota-fuente">{!! $ref['nota'] !!}</span>
                                            @endif
                                            <span class="estructura">{!! $ref['estructura'] !!}</span>
                                            <span class="ejemplo">{!! $ref['ejemplo'] !!}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Nota final del documento de pautas, con las dos vías de
                             consulta del manual que indica la coordinación editorial. --}}
                    <div class="nota-manual">
                        <p class="panel__sub panel__sub--cabeza">Consulta del manual</p>
                        <p>
                            Es posible consultar algunas de las pautas del manual en la
                            <a href="https://www.chicagomanualofstyle.org/tools_citationguide.html" target="_blank" rel="noopener">guía rápida de <em>The Chicago Manual of Style</em></a>.
                        </p>
                        <p>
                            Asimismo, el manual se puede consultar de manera íntegra por medio de la
                            <a href="https://www.bidi.unam.mx/" target="_blank" rel="noopener">Biblioteca Digital UNAM</a>:
                            pestaña «Colecciones digitales» &rsaquo; apartado «Bases de datos» &rsaquo;
                            Búsqueda de bases de datos &rsaquo; escribir «The Chicago Manual of Style Online».
                        </p>
                    </div>

                </div>
            </div>
        </div>

    </div>

</main>
@endsection
