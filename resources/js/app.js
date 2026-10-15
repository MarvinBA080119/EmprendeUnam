import 'bootstrap';
import { createApp, defineAsyncComponent } from 'vue';

// Tipografías instaladas localmente (sin CDN).
import '@fontsource/inter/latin-400.css';
import '@fontsource/inter/latin-600.css';
import '@fontsource/inter/latin-700.css';
import '@fontsource/playfair-display/latin-400.css';
import '@fontsource/playfair-display/latin-700.css';

/*
 * "Islas" Vue: Blade arma la página y cada parte interactiva se monta en un
 * <div id="..."> con sus datos en el atributo data-props (JSON).
 *   <div id="lista-temas" data-props='@json([...])'></div>
 */
const islas = {
    'lista-temas': () => import('./components/publico/ListaTemas.vue'),
    'formulario-articulo': () => import('./components/autor/FormularioArticulo.vue'),
    'tabla-articulos': () => import('./components/admin/TablaArticulos.vue'),
    'tabla-autores': () => import('./components/admin/TablaAutores.vue'),
    'tabla-mensajes': () => import('./components/admin/TablaMensajes.vue'),
    'tabla-suscriptores': () => import('./components/admin/TablaSuscriptores.vue'),
};

for (const [id, cargar] of Object.entries(islas)) {
    const el = document.getElementById(id);
    if (!el) {
        continue;
    }
    let props = {};
    try {
        props = el.dataset.props ? JSON.parse(el.dataset.props) : {};
    } catch (error) {
        console.error(`Props inválidas en #${id}`, error);
    }
    createApp(defineAsyncComponent(cargar), props).mount(el);
}
