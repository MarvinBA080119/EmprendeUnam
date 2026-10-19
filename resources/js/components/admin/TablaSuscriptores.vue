<script setup>
import { computed } from 'vue';
import Paginacion from '../comun/Paginacion.vue';
import { useListado } from '../comun/useListado';

const props = defineProps({
    urlDatos: { type: String, required: true },
    urlExportar: { type: String, required: true },
    estados: { type: Object, required: true },
    filtros: { type: Object, default: () => ({}) },
});

const { filtros, filas, meta, cargando, error, irAPagina } = useListado(props.urlDatos, {
    q: props.filtros.q ?? '',
    estado: props.filtros.estado ?? '',
});

// El CSV respeta los filtros actuales.
const enlaceCsv = computed(() => {
    const p = new URLSearchParams();
    if (filtros.estado) p.set('estado', filtros.estado);
    if (filtros.q.trim()) p.set('q', filtros.q.trim());
    const consulta = p.toString();
    return consulta ? `${props.urlExportar}?${consulta}` : props.urlExportar;
});
</script>

<template>
    <div>
        <div class="emp-seccion__barra">
            <h2 class="emp-seccion__titulo">Suscriptores</h2>
            <a :href="enlaceCsv" class="btn btn-secondary btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true" focusable="false" class="emp-icono">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5" />
                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z" />
                </svg>
                Exportar CSV
            </a>
        </div>
        <form class="row g-2 align-items-end emp-filtros" role="search" @submit.prevent>
            <div class="col-12 col-md-7">
                <label for="sus-q" class="form-label">Buscar</label>
                <input id="sus-q" v-model="filtros.q" type="search" maxlength="100" class="form-control"
                       placeholder="Nombre o correo" />
            </div>
            <div class="col-12 col-md-5">
                <label for="sus-estado" class="form-label">Estado</label>
                <select id="sus-estado" v-model="filtros.estado" class="form-select">
                    <option value="">Todos</option>
                    <option v-for="(etiqueta, valor) in estados" :key="valor" :value="valor">{{ etiqueta }}</option>
                </select>
            </div>
        </form>

        <div v-if="error" class="alert alert-danger" role="alert">{{ error }}</div>

        <div class="emp-marco-tabla">
            <div class="table-responsive" :aria-busy="cargando">
                <table class="table emp-tabla align-middle">
                    <caption class="visually-hidden">Suscriptores del newsletter</caption>
                    <thead>
                        <tr>
                            <th scope="col">Nombre</th>
                            <th scope="col">Correo</th>
                            <th scope="col">Tema de interés</th>
                            <th scope="col">Alta</th>
                            <th scope="col">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!cargando && filas.length === 0">
                            <td colspan="5" class="emp-vacio">No hay suscriptores con esos criterios.</td>
                        </tr>
                        <tr v-for="s in filas" :key="s.id">
                            <td>{{ s.nombre }}</td>
                            <td class="text-break">{{ s.correo }}</td>
                            <td>{{ s.temas }}</td>
                            <td class="text-nowrap">{{ s.alta }}</td>
                            <td><span class="emp-insignia" :class="s.estadoClase">{{ s.estadoEtiqueta }}</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="emp-marco-tabla__pie">
                <p v-if="cargando" class="emp-cargando" role="status">Cargando…</p>
                <Paginacion :meta="meta" @ir="irAPagina" />
            </div>
        </div>
    </div>
</template>
