# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

- **Equipo editorial (administrador).** De 1 a 3 personas de la revista en la Facultad de Contaduría y Administración (FCA, UNAM). Trabajan en computadora de escritorio o laptop, en jornadas de trabajo: activan cuentas de autores, dictaminan artículos (aprobar o rechazar con motivo), responden el consultorio, publican cursos y avisan nuevas ediciones del newsletter. Usan el panel todos los días; la eficiencia y la claridad de estados pesan más que la expresión.
- **Autores.** Principalmente académicos e investigadores de la UNAM y de otras universidades. Se registran, esperan la activación, envían su artículo en PDF (título, resumen de hasta 50 palabras, tema, 5 palabras clave, hasta 1 coautor), consultan su historial y descargan su constancia con QR. Lo usan pocas veces al año; cada visita debe ser obvia y confiable.
- **Público lector** del sitio de la revista (consultorio, buscador, cursos, newsletter). El sitio público tiene diseño propio y no forma parte del sistema.

## Product Purpose

Sistema de gestión de la Revista EmprendeUNAM: recibir artículos, dictaminarlos y emitir constancias verificables, además de operar el consultorio, los cursos y el newsletter. Éxito: un autor envía su artículo sin ayuda y recibe su constancia; el equipo editorial sabe en todo momento qué está pendiente y lo resuelve en pocos pasos.

## Positioning

Es la revista de emprendimiento de la FCA de la UNAM: el respaldo institucional y la constancia con folio verificable públicamente son lo que la distingue de una convocatoria por correo o un formulario genérico.

## Operating Context

- Flujo del artículo: EN_REVISION → APROBADO (genera constancia con folio `EMP-AAAA-NNNN` y QR) o RECHAZADO (con motivo obligatorio). No hay reenvío.
- Cuentas de autor inactivas hasta que el administrador las activa.
- Correos en cola para cada paso (cuenta recibida, activada, artículo recibido, aprobado, rechazado, respuesta del consultorio, newsletter).
- Verificación pública de constancias en `/constancias/verificar/{folio}`.

## Capabilities and Constraints

- Laravel 13, Vue 3 en islas sobre Blade, Bootstrap 5 + Sass, CSS propio en `resources/css/emprende.css`. Sin CDN: fuentes y librerías desde npm.
- Todo el texto en español de México.
- Layouts del sistema: `layouts/publico` (login, registro), `layouts/autor`, `layouts/admin`. El sitio público (`layouts/sitio`) **no se rediseña**.
- Estados del artículo y del consultorio se muestran con insignias de la paleta y siempre con texto.
- PDF del artículo de hasta 7 MB; la constancia se genera con dompdf.

## Brand Commitments

- Nombre: **Revista EmprendeUNAM**, con la leyenda «UNAM · Facultad de Contaduría y Administración».
- **Paleta cerrada del sistema (obligatoria):** `#664300` (café, primario), `#001328` (azul, secundario), blanco y negro. Se permiten tonos derivados de esos cuatro (mezclas y transparencias), ningún otro color. **Nunca** texto café sobre azul ni azul sobre café. Sin verdes ni rojos para estados.
- **Dirección visual del sistema: el estándar de la categoría, ejecutado al nivel de Linear, Stripe Dashboard y Vercel** (elegido por el usuario el 16/10/2026). Su nivel de acabado es la vara: retícula alineada, escala tipográfica real, espaciado consistente, estados claros y movimiento mínimo. Nada de mundo temático ni guiños decorativos.
- Tono: institucional y editorial — sobrio y confiable como una revista académica de la UNAM, cálido y claro, sin burocracia.

## Evidence on Hand

- No hay logotipo de la revista en el repositorio; no inventar uno.
- Las imágenes del sitio público se copian aparte (`public/img`, `public/pdf`) y no se usan en el sistema.
- No hay testimonios, cifras ni casos publicados para usar en el sistema.

## Product Principles

1. Lo pendiente primero: el equipo editorial debe ver de inmediato qué requiere acción.
2. Cada estado se entiende sin color: texto claro, nunca solo un ícono o un tono.
3. Respaldo institucional visible y sobrio: la seriedad de la UNAM sin rigidez de trámite.
4. El autor nunca queda con dudas: qué sigue, cuánto tarda, dónde ver su constancia.

## Accessibility & Inclusion

Contraste suficiente dentro de la paleta (regla de nunca café sobre azul), navegación con teclado, foco visible y estados comunicados con texto.
