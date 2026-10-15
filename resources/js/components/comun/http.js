/**
 * Peticiones JSON a las rutas de la aplicación con la misma sesión y el
 * token CSRF (cabecera X-CSRF-TOKEN, leída de <meta name="csrf-token">).
 */
export async function peticionJson(url, { metodo = 'GET', datos = null } = {}) {
    const token = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const respuesta = await fetch(url, {
        method: metodo,
        credentials: 'same-origin',
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...(metodo !== 'GET' ? { 'X-CSRF-TOKEN': token, 'Content-Type': 'application/json' } : {}),
        },
        body: datos ? JSON.stringify(datos) : undefined,
    });

    let cuerpo = null;
    try {
        cuerpo = await respuesta.json();
    } catch {
        // respuesta sin JSON
    }

    if (!respuesta.ok) {
        const mensaje =
            cuerpo?.errors ? Object.values(cuerpo.errors).flat()[0]
            : respuesta.status === 419 ? 'Tu sesión expiró. Recarga la página.'
            : respuesta.status === 403 ? 'No tienes permiso para realizar esta acción.'
            : cuerpo?.message || 'Ocurrió un error. Intenta de nuevo.';
        const error = new Error(mensaje);
        error.estado = respuesta.status;
        throw error;
    }

    return cuerpo;
}
