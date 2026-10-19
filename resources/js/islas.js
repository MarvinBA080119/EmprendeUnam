import { createApp, defineAsyncComponent } from 'vue';

/*
 * "Islas" Vue: Blade arma la página y cada parte interactiva se monta en un
 * <div id="..."> con sus datos en el atributo data-props (JSON).
 *   <div id="lista-temas" data-props='@json([...])'></div>
 */
export function montarIslas(islas) {
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
}
