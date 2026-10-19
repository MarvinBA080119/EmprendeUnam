@extends('layouts.sitio')

@php
    $tituloPagina = 'Nuestra historia';
    $paginaId = '';
    $recursos = ['resources/css/sitio/institucional_suizo.css'];
@endphp

@section('contenido')
<main id="contenido" class="suizo">

    <!-- Marca de agua de fondo -->
    <span class="marca-agua" aria-hidden="true">FCA UNAM</span>

    <div class="container">
        <div class="row">

            <!-- ══════════════ COLUMNA FIJA ══════════════ -->
            <div class="col-lg-4">
                <div class="columna-fija">
                    <p class="rotulo">EmprendeUNAM</p>
                    <h1 class="titulo-gigante">Nuestra <em>historia</em></h1>
                    <div class="filete-rojo"></div>
                </div>
            </div>

            <!-- ══════════════ COLUMNA QUE FLUYE ══════════════ -->
            <div class="col-lg-7 offset-lg-1">
                <div class="texto-fluye" style="padding-top: 5rem;">

                    <p class="historia__antetitulo"><em>Revista Emprendedores</em> 1986-2026</p>

                    <p>Era 1986 y México se preparaba para recibir al mundo en la Copa Mundial de Fútbol. Sin embargo, el contexto no era festivo, había un profundo malestar social acumulado tras años de crisis económica, caída del Producto Interno Bruto, inflación descontrolada y un peso devaluado que afectaba el poder adquisitivo de millones de familias mexicanas.</p>
                    <p>Apenas nueve meses antes, el 19 de septiembre de 1985, la Ciudad de México había sido sacudida por uno de los terremotos más devastadores de su historia. Sin embargo, de aquella tragedia emergió una sociedad civil organizada y extraordinaria, con su impulso solidario marcaría, para siempre, el carácter dispuesto y emprendedor del pueblo mexicano.</p>

                    <h2 class="historia__sub">El mundo empieza a cambiar</h2>
                    <p>En el escenario global, 1986 representó un momento de transición histórica. La Guerra Fría, que durante décadas había dividido al mundo en dos bloques ideológicos, parecía terminar. En Reykjavik, Islandia, Reagan y Gorbachov sostuvieron conversaciones sobre el desarme nuclear, las cuales años antes parecían impensables.</p>
                    <p>Ese mismo año, la explosión del reactor de Chernóbil en Ucrania, entonces parte de la URSS, tuvo consecuencias ambientales, políticas y simbólicas que aceleraron la pérdida de legitimidad del bloque. En suma, el mundo se encontraba en el umbral de una reconfiguración que transformó por completo el mapa político internacional.</p>

                    <h2 class="historia__sub">Del petróleo al ajuste</h2>
                    <p>Para México, 1986 significó el punto más agudo de la crisis de la deuda; las exportaciones petroleras se desplomaron 57% como consecuencia del derrumbe de los precios internacionales del crudo, lo que puso al descubierto la vulnerabilidad de una economía que dependía de un solo producto primario. La inflación anual superó el 86% y continuó escalando hasta alcanzar casi el 160% en 1987, devastando el ahorro y el consumo de las clases medias y populares.</p>
                    <p>El gobierno respondió con programas de austeridad severa, recortes al gasto público y la firma de sucesivos acuerdos con el Fondo Monetario Internacional. En 1987 se instrumentó un acuerdo entre el gobierno, los empresarios y los sindicatos para estabilizar precios y salarios. Las medidas de ajuste estructural marcaron el inicio de la transformación del modelo económico mexicano: del Estado interventor y proteccionista, hacia una economía abierta, orientada al mercado y a la inserción en las cadenas globales de valor.</p>

                    <h2 class="historia__sub">México frente a la apertura comercial</h2>
                    <p>En agosto de 1986, México firmó su adhesión al Acuerdo General sobre Aranceles Aduaneros y Comercio, esto significó el abandono formal del modelo de sustitución de importaciones y el inicio de la apertura comercial y la competencia internacional.</p>
                    <p>La decisión fue motivo de debates; sectores académicos, industriales y sindicales advirtieron sobre los riesgos de exponer a las empresas nacionales, la inmensa mayoría pequeñas, a la competencia de economías con mayor desarrollo tecnológico y productividad. Pero el rumbo estaba trazado; siete años más tarde, en 1994, México firmó el Tratado de Libre Comercio de América del Norte con Estados Unidos y Canadá, por medio del cual consolidó su posición como economía abierta y se transformó, gradualmente, en una nación exportadora de manufacturas e, inevitablemente, en terreno fértil para las empresas pequeñas y medianas.</p>
                    <p>Es en este contexto que, con el impulso del doctor Alfredo Adam Adam, director de la Facultad de Contaduría y Administración surge la <em>Revista Emprendedores</em> como un medio impreso para compartir artículos de divulgación técnica así como difundir el conocimiento que se generaba en sus aulas de licenciatura y posgrado y en su Centro de Investigación.</p>

                    <h2 class="historia__sub">40 años de cambio</h2>
                    <p>Las décadas siguientes traerían cambios, uno de ellos fueron las elecciones de 1988, que provocaron gran desconfianza institucional, y llevaron a la alternancia en el gobierno de la Ciudad de México en 1997 y en la presidencia de la República en el 2000. En 2026, México es gobernado por Claudia Sheinbaum Pardo, la primera mujer en ocupar la presidencia de México, y surge un nuevo eje de poder político que, con sus propias contradicciones y debates, expresa cuánto ha cambiado el país.</p>
                    <p>México vuelve a ser sede del Mundial de Futbol —compartida con Estados Unidos y Canadá—, después de 40 años en un contexto tecnológico radicalmente distinto. Lo que en 1986 dependía del ojo humano, y que produjo episodios legendarios como “la mano de Dios” de Maradona, hoy es revisado, medido y corregido en milisegundos por algoritmos. La transformación tecnológica del deporte es sólo uno de los distintos cambios que viven las economías y las organizaciones en todos los sectores. El reto de México en 2026 es que la infraestructura pública, la agenda social y la capacidad institucional estén a la altura en todos los espacios de la economía.</p>

                    <h2 class="historia__sub">La construcción del futuro</h2>
                    <p>La inteligencia artificial, el aprendizaje automático, la automatización de procesos y la economía digital apuntan a que los próximos años concentrarán más cambios que los cuarenta anteriores. Para México, esto representa tanto una oportunidad histórica como un riesgo de rezago si no se toman decisiones estratégicas.</p>
                    <p>La dirección deseable apunta hacia una economía de servicios de valor agregado, mayor integración regional, fortalecimiento del mercado interno y políticas públicas que estimulen la innovación en las Pyme.</p>
                    <p>Hoy la capacidad de respuesta organizada y solidaria define el rumbo de la sociedad y en estos cuarenta años, México ha cambiado en gran medida en su economía, su sistema político, su estructura social y en su inserción en el mundo, y la actitud emprendedora que caracterizó la respuesta ciudadana ante la tragedia sigue siendo el activo más valioso del país.</p>
                    <p>La Revista Emprendedores de la Facultad de Contaduría y Administración de la UNAM, también se ha transformado. A principios de 1990 el Dr. Salvador Ruiz de Chávez cambia el nombre de la revista y se convierte en <em>Revista Emprendedores al servicio de la pequeña y mediana empresa</em>; la doctora María Antonieta Martín Granados actualiza la imagen de la revista y en el bimestre de septiembre-octubre de 2006 se inicia su publicación en medio electrónico; el doctor Juan Alberto Adam Siade, anticipándose a las condiciones actuales de difusión, publica en el bimestre noviembre-diciembre de 2016 el último número impreso y redobla la difusión en medio electrónico, con el maestro Tomás Humberto Rubio Pérez, cobra nuevos bríos y se internacionaliza.</p>
                    <p>Cuarenta años de actividad académica rigurosa, en la que las y los articulistas se comprometen con el emprendimiento mexicano, vinculando el conocimiento universitario con la realidad empresarial, así como a mantener actualizadas a las generaciones de profesionistas que trabajan por el desarrollo del país.</p>
                    <p>El acompañamiento y promoción del espíritu emprendedor nos trae hasta aquí, para celebrar el 40 aniversario de nuestra revista que se transforma en <em>EmprendeUNAM</em> e incorpora su <em>newsletter</em> para seguir mirando hacia adelante con la misma determinación que caracterizó a quienes construyeron lo que hoy somos con cooperación, innovación y con resultados medibles al servicio de la actividad emprendedora en México.</p>

                    <p class="historia__firma"><strong>Dr. Armando Tomé González</strong><span>Director</span></p>

                </div>
            </div>

        </div>
    </div>

</main>
@endsection
