/**
 * Límite de peso de los archivos, revisado en el navegador antes de enviar:
 * todo <input type=file data-max-kb="…"> rechaza al momento un archivo más
 * pesado con una notificación de error, sin salir de la página. El servidor
 * vuelve a validar (y si el envío supera post_max_size, regresa a la misma
 * vista con ?aviso=archivo-pesado; ver App\Support\AvisoArchivo).
 */
import { avisar } from './avisos';

export const peso = (bytes) => (bytes >= 1048576
    ? `${(bytes / 1048576).toFixed(1).replace(/\.0$/, '')} MB`
    : `${Math.max(1, Math.round(bytes / 1024))} KB`);

const limite = (campo) => Number(campo.dataset.maxKb || 0) * 1024;

/** true si el archivo cabe en el límite del campo; si no, avisa y devuelve false. */
export function dentroDelLimite(campo, archivo) {
    const maximo = limite(campo);
    if (!maximo || !archivo || archivo.size <= maximo) return true;
    avisar(`«${archivo.name}» pesa ${peso(archivo.size)} y el máximo para este campo es ${peso(maximo)}. Elige un archivo más ligero.`, 'error');
    return false;
}

const excedido = (campo) => Array.from(campo.files ?? []).find((archivo) => archivo.size > limite(campo));

export function vigilarArchivos() {
    // Fase de captura: corre antes que los demás manejadores del campo, que
    // así ven el campo vacío y no muestran una vista previa del archivo rechazado.
    document.addEventListener('change', (evento) => {
        const campo = evento.target;
        if (!(campo instanceof HTMLInputElement) || campo.type !== 'file' || !limite(campo)) return;
        const archivo = excedido(campo);
        if (archivo) {
            dentroDelLimite(campo, archivo);
            campo.value = '';
        }
    }, true);

    document.addEventListener('submit', (evento) => {
        const campo = Array.from(evento.target.querySelectorAll?.('input[type=file][data-max-kb]') ?? []).find(excedido);
        if (campo) {
            evento.preventDefault();
            evento.stopImmediatePropagation();
            dentroDelLimite(campo, excedido(campo));
            campo.focus();
        }
    }, true);

    // El aviso de un envío demasiado pesado llega en la URL: se quita para que
    // no reaparezca al recargar.
    const url = new URL(window.location.href);
    if (url.searchParams.has('aviso')) {
        url.searchParams.delete('aviso');
        window.history.replaceState(window.history.state, '', url);
    }
}
