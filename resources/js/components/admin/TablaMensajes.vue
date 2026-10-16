<script setup>
import Paginacion from '../comun/Paginacion.vue';
import { useListado } from '../comun/useListado';

const props = defineProps({
    urlDatos: { type: String, required: true },
    estatus: { type: Array, required: true },
    filtros: { type: Object, default: () => ({}) },
});

const { filtros, filas, meta, cargando, error, irAPagina } = useListado(props.urlDatos, {
    q: props.filtros.q ?? '',
    estatus: props.filtros.estatus ?? '',
});
</script>

<template>
    <div>
        <form class="row g-2 align-items-end emp-filtros" role="search" @submit.prevent>
            <div class="col-12 col-md-6">
                <label for="men-q" class="form-label">Buscar</label>
                <input id="men-q" v-model="filtros.q" type="search" maxlength="100" class="form-control"
                       placeholder="Remitente, correo o asunto" />
            </div>
            <div class="col-12 col-md-4">
                <label for="men-estatus" class="form-label">Estatus</label>
                <select id="men-estatus" v-model="filtros.estatus" class="form-select">
                    <option value="">Todos</option>
                    <option v-for="e in estatus" :key="e.valor" :value="e.valor">{{ e.etiqueta }}</option>
                </select>
            </div>
        </form>

        <div v-if="error" class="alert alert-danger" role="alert">{{ error }}</div>

        <div class="emp-marco-tabla">
            <div class="table-responsive" :aria-busy="cargando">
                <table class="table emp-tabla align-middle">
                    <caption class="visually-hidden">Mensajes del consultorio</caption>
                    <thead>
                        <tr>
                            <th scope="col">Asunto</th>
                            <th scope="col">Remitente</th>
                            <th scope="col">Recibido</th>
                            <th scope="col">Estatus</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!cargando && filas.length === 0">
                            <td colspan="4" class="emp-vacio">No hay mensajes con esos criterios.</td>
                        </tr>
                        <tr v-for="m in filas" :key="m.id">
                            <td><a :href="m.url">{{ m.asunto }}</a></td>
                            <td>
                                {{ m.remitente }}
                                <div class="small text-body-secondary text-break">{{ m.correo }}<template v-if="m.lugar"> · {{ m.lugar }}</template></div>
                            </td>
                            <td class="text-nowrap">{{ m.fecha }}</td>
                            <td><span class="emp-insignia" :class="m.estatus.clase">{{ m.estatus.etiqueta }}</span></td>
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
