<?php

/*
| Lineamientos para autores: áreas, referencias y secciones.
| Convertido del sitio público original (lineamientos.php) sin cambiar su contenido.
*/

return [
    'areas_tematicas' => [
        'Habilidades directivas',
        'Procesos creativos',
        'Diagnóstico de problemas',
        'Dirección de talento',
        'Escenarios y estrategia competitiva',
        'Cultura organizacional',
        'Análisis de mercados',
        'Comunicación',
        'Economía y sociedad',
        'Relación con el consumidor',
        'Negocios internacionales',
        'Toma de decisiones y resolución de problemas',
        'Emprendimiento social',
    ],
    'referencias' => [

        ['tipo' => 'Libro impreso',
            'estructura' => 'Apellido paterno y materno, nombre. Año. <em>Título del libro en cursivas</em>. Editorial.',
            'ejemplo' => 'Ejemplo: Corella, José María. 1998. <em>Introducción a la gestión de marketing en los servicios de salud</em>. Fondo de Publicaciones del Gobierno de Navarra.'],

        ['tipo' => 'Libro electrónico',
            'estructura' => 'Apellido paterno y materno, nombre. Año. <em>Título del libro en cursivas</em>. Editorial. Formato.',
            'ejemplo' => 'Ejemplo: Bauman, Zygmunt. 2020. <em>La globalización. Consecuencias humanas</em>. Fondo de Cultura Económica. Kindle.'],

        ['tipo' => 'Capítulo de libro',
            'estructura' => 'Apellido paterno y materno, nombre de la autora o autor del capítulo. Año. “Título del capítulo entre comillas”. En <em>Título del libro en cursivas</em>, coordinado (o editado, según el caso) por nombre y apellidos de la coordinadora o coordinador. Editorial.',
            'ejemplo' => 'Ejemplo: Oehmichen Bazán, Cristina. 2026. “México: políticas públicas del principal destino turístico internacional en América Latina y el Caribe”. En <em>Políticas turísticas en América Latina y el Caribe: perspectivas comparadas para un análisis crítico</em>, editado por Erica Schenkel y Ernest Cañada. Consejo Latinoamericano de Ciencias Sociales.'],

        ['tipo' => 'Revista impresa',
            'estructura' => 'Apellido paterno y materno, nombre. Año. “Título del artículo entre comillas”. <em>Nombre de la revista en cursivas</em> volumen(número): páginas del artículo.',
            'ejemplo' => 'Ejemplo: García Tapia, Gabino. 2022. “HIPCE Solutions: reconvertirse o morir”. <em>Emprendedores</em> 101: 24-29.'],

        ['tipo' => 'Revista en línea',
            'estructura' => 'Apellido paterno y materno, nombre. Año. “Título del artículo entre comillas”. <em>Nombre de la revista en cursivas</em> volumen(número): páginas del artículo. DOI.',
            'ejemplo' => 'Ejemplo: Navarrete Reynoso, Ramón, Jorge Armando López-Lemus, y Yesit Jovan Rodríguez Caro. 2026. “El impacto de la innovación en el desempeño organizacional: el rol mediador de la competitividad en empresas colombianas Artículos de Investigación”. <em>Contaduría y Administración</em> 71(4): 106-132. https://doi.org/10.22201/fca.24488410e.2026.5673.'],

        ['tipo' => 'Sitio web',
            'estructura' => 'Nombre del sitio. S.f. “Título del sitio entre comillas”. Consultado el (fecha) de (mes) de (año). URL.',
            'nota' => 'A menudo, basta con describir las páginas web y otros contenidos del sitio en el propio texto del artículo (e. g. “Al 15 de noviembre de 2026, la política de privacidad de Google implicaba...”). Si se requiere una cita más formal en donde la fuente no indica una fecha de publicación o revisión, utilice “s.f.” (que significa sin fecha) en lugar del año e incluya la fecha de consulta.',
            'ejemplo' => 'Ejemplo: Red Iberoamericana de Mercadotecnia en Salud. S.f. “Quiénes somos”. Consultado el 12 de agosto de 2026. https://rims.org.mx/index.php/quienes-somos/.'],

        ['tipo' => 'Podcast',
            'estructura' => 'Apellido paterno y materno, nombre de la presentadora o presentador (o de las y los invitados en caso de que no se sepa el del presentador). Año. <em>Nombre del podcast en cursivas</em>. Temporada, episodio, “Nombre del programa en cursivas”. Nombre del productor, fecha de publicación. Podcast, duración en horas, duración en minutos. URL.',
            'ejemplo' => 'Ejemplo: Carmona, Patricia, Patrick Torrent, y Ana Blasco, invitados. 2025. <em>On the go</em>. Temporada 1, episodio 9, “Turismo rural”. ATREVIA/UNU Turismo, 23 de abril. Podcast, 1 hora, 12 min. https://open.spotify.com/episode/6uUT69vIJgy2uyXJUreZk8?si=858b9d3b44654355.'],

        ['tipo' => 'Video',
            'estructura' => 'Apellido paterno y materno, nombre de las y los invitados. Año en el que se publicó. “Título del video entre comillas”. Conducido por (nombre de la o el conductor), en caso de que haya una o uno. Publicado el (fecha) de (mes) de (año), por (entidad que lo publicó). Plataforma en donde se encuentra el video, duración exacta. URL.',
            'ejemplo' => 'Ejemplo: Solís García, José Julio y Rubén Torres Benítez. 2026. “Declaraciones prellenadas y datos de los CFDI: inconsistencias”. Conducido por Susana Mireles Arreola. Publicado el 9 de abril de 2026 por la Facultad de Contaduría y Administración, UNAM. YouTube, 1:07:48. https://www.youtube.com/watch?v=uqPV6d-slfU.'],

        ['tipo' => 'Artículo de periódico',
            'estructura' => 'Apellido paterno y materno, nombre. Año. “Título del artículo entre comillas”. <em>Nombre del periódico en cursivas</em>, mes y día. URL.',
            'ejemplo' => 'Ejemplo: Cruz Martínez, Ángeles. 2025. “Rechaza SSA obligar a las personas a vacunarse; el enfoque es persuadir”. <em>La Jornada</em>, febrero 26. https://www.jornada.com.mx/noticia/2025/08/06/sociedad/rechaza-ssa-obligar-a-las-personas-a-vacunarse-el-enfoque-es-persuadir.'],

    ],
    'secciones' => [
        ['id' => 'enfoque',     'label' => 'Enfoque y temáticas'],
        ['id' => 'documento',   'label' => 'Especificaciones'],
        ['id' => 'entrega',     'label' => 'Entrega y derechos'],
        ['id' => 'referencias', 'label' => 'Citas y referencias'],
    ],
];
