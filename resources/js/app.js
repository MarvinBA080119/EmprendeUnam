import 'bootstrap';
import { montarIslas } from './islas';

// Tipografías instaladas localmente (sin CDN).
import '@fontsource/inter/latin-400.css';
import '@fontsource/inter/latin-600.css';
import '@fontsource/inter/latin-700.css';
import '@fontsource/playfair-display/latin-400.css';
import '@fontsource/playfair-display/latin-700.css';

// Sistema (autores y administrador).
montarIslas({
    'formulario-articulo': () => import('./components/autor/FormularioArticulo.vue'),
    'tabla-articulos': () => import('./components/admin/TablaArticulos.vue'),
    'tabla-autores': () => import('./components/admin/TablaAutores.vue'),
    'tabla-mensajes': () => import('./components/admin/TablaMensajes.vue'),
    'tabla-suscriptores': () => import('./components/admin/TablaSuscriptores.vue'),
});
