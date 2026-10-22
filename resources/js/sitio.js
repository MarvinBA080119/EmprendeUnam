/*
 * Sitio público de la Revista EmprendeUNAM (diseño propio del sitio, distinto
 * del sistema). Bootstrap, iconos y tipografías se sirven desde npm, sin CDN.
 */
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import * as bootstrap from 'bootstrap';

// Barlow Condensed (titulares) + Barlow (lectura), como en el sitio original.
import '@fontsource/barlow-condensed/latin-400.css';
import '@fontsource/barlow-condensed/latin-500.css';
import '@fontsource/barlow-condensed/latin-600.css';
import '@fontsource/barlow-condensed/latin-700.css';
import '@fontsource/barlow-condensed/latin-400-italic.css';
import '@fontsource/barlow-condensed/latin-500-italic.css';
import '@fontsource/barlow-condensed/latin-600-italic.css';
import '@fontsource/barlow/latin-400.css';
import '@fontsource/barlow/latin-500.css';
import '@fontsource/barlow/latin-600.css';
import '@fontsource/barlow/latin-700.css';
import '@fontsource/barlow/latin-400-italic.css';

import '../css/sitio/styles.css';
import '../css/sitio/integracion.css';
import './sitio/main.js';
import { iniciarEnvios } from './sitio/envios.js';
import { montarIslas } from './islas';

window.bootstrap = bootstrap;

iniciarEnvios();

montarIslas({
    buscador: () => import('./components/publico/Buscador.vue'),
});
