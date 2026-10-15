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
        <form class="row g-2 align-items-end mb-3" role="search" @submit.prevent>
            <div class="col-12 col-md-5">
                <label for="sus-q" class="form-label">Buscar</label>
                <input id="sus-q" v-model="filtros.q" type="search" maxlength="100" class="form-control"
                       placeholder="Nombre o correo" />
            </div>
            <div class="col-8 col-md-4">
                <label for="sus-estado" class="form-label">Estado</label>
                <select id="sus-estado" v-model="filtros.estado" class="form-select">
                    <option value="">Todos</option>
                    <option v-for="(etiqueta, valor) in estados" :key="valor" :value="valor">{{ etiqueta }}</option>
                </select>
            </div>
            <div class="col-4 col-md-3">
                <a :href="enlaceCsv" class="btn btn-secondary w-100">Exportar CSV</a>
            </div>
        </form>

        <div v-if="error" class="alert alert-danger" role="alert">{{ error }}</div>

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
                        <td colspan="5">No hay suscriptores con esos criterios.</td>
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
        <p v-if="cargando" class="small" role="status">Cargando…</p>
        <Paginacion :meta="meta" @ir="irAPagina" />
    </div>
</template>
