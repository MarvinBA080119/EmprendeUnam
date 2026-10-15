import { onMounted, reactive, ref, watch } from 'vue';
import { peticionJson } from './http';

/**
 * Lista paginada con filtros para las tablas del administrador.
 * Pide JSON a `urlDatos` ({ datos, meta }) y mantiene los filtros en la URL
 * de la página para que recargar o compartir el enlace conserve la vista.
 */
export function useListado(urlDatos, filtrosIniciales = {}, camposTexto = ['q']) {
    const filtros = reactive({ ...filtrosIniciales });
    const filas = ref([]);
    const meta = ref({ pagina: 1, ultima: 1, total: 0, desde: null, hasta: null });
    const cargando = ref(false);
    const error = ref('');
    const pagina = ref(Number(new URLSearchParams(window.location.search).get('page')) || 1);

    let temporizador = null;
    let ultimaPeticion = 0;

    function parametros() {
        const p = new URLSearchParams();
        for (const [clave, valor] of Object.entries(filtros)) {
            if (valor !== null && valor !== undefined && String(valor).trim() !== '') {
                p.set(clave, String(valor).trim());
            }
        }
        if (pagina.value > 1) p.set('page', String(pagina.value));
        return p;
    }

    async function cargar() {
        const id = ++ultimaPeticion;
        const p = parametros();
        cargando.value = true;
        error.value = '';
        try {
            const respuesta = await peticionJson(`${urlDatos}?${p}`);
            if (id !== ultimaPeticion) return; // llegó una respuesta más nueva
            filas.value = respuesta.datos;
            meta.value = respuesta.meta;
            const consulta = p.toString();
            window.history.replaceState(null, '', consulta ? `?${consulta}` : window.location.pathname);
        } catch (e) {
            if (id === ultimaPeticion) error.value = e.message;
        } finally {
            if (id === ultimaPeticion) cargando.value = false;
        }
    }

    function irAPagina(numero) {
        pagina.value = numero;
        cargar();
    }

    function limpiar() {
        for (const clave of Object.keys(filtros)) filtros[clave] = '';
    }

    // Los campos de texto esperan a que se deje de escribir; los demás filtran de inmediato.
    watch(
        () => ({ ...filtros }),
        (nuevos, anteriores) => {
            const soloTexto = Object.keys(nuevos).every(
                (k) => nuevos[k] === anteriores[k] || camposTexto.includes(k),
            );
            clearTimeout(temporizador);
            pagina.value = 1;
            temporizador = setTimeout(cargar, soloTexto ? 350 : 0);
        },
    );

    onMounted(cargar);

    return { filtros, filas, meta, cargando, error, irAPagina, limpiar, recargar: cargar };
}
