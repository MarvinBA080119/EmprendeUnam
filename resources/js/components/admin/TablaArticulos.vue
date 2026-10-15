<script setup>
import Paginacion from '../comun/Paginacion.vue';
import { useListado } from '../comun/useListado';

const props = defineProps({
    urlDatos: { type: String, required: true },
    temas: { type: Array, required: true },
    estatus: { type: Array, required: true },
    filtros: { type: Object, default: () => ({}) },
});

const { filtros, filas, meta, cargando, error, irAPagina, limpiar } = useListado(props.urlDatos, {
    q: props.filtros.q ?? '',
    estatus: props.filtros.estatus ?? '',
    tema: props.filtros.tema ?? '',
    desde: props.filtros.desde ?? '',
    hasta: props.filtros.hasta ?? '',
});
</script>

<template>
    <div>
        <form class="row g-2 align-items-end mb-3" role="search" @submit.prevent>
            <div class="col-12 col-lg-4">
                <label for="art-q" class="form-label">Buscar</label>
                <input id="art-q" v-model="filtros.q" type="search" maxlength="200" class="form-control"
                       placeholder="Título, autor o palabra clave" />
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <label for="art-estatus" class="form-label">Estatus</label>
                <select id="art-estatus" v-model="filtros.estatus" class="form-select">
                    <option value="">Todos</option>
                    <option v-for="e in estatus" :key="e.valor" :value="e.valor">{{ e.etiqueta }}</option>
                </select>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <label for="art-tema" class="form-label">Tema</label>
                <select id="art-tema" v-model="filtros.tema" class="form-select">
                    <option value="">Todos</option>
                    <option v-for="t in temas" :key="t.id" :value="String(t.id)">{{ t.nombre }}</option>
                </select>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <label for="art-desde" class="form-label">Enviado desde</label>
                <input id="art-desde" v-model="filtros.desde" type="date" class="form-control" />
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <label for="art-hasta" class="form-label">Hasta</label>
                <input id="art-hasta" v-model="filtros.hasta" type="date" class="form-control" />
            </div>
            <div class="col-12">
                <button type="button" class="btn btn-link px-0" @click="limpiar">Limpiar filtros</button>
            </div>
        </form>

        <div v-if="error" class="alert alert-danger" role="alert">{{ error }}</div>

        <div class="table-responsive" :aria-busy="cargando">
            <table class="table emp-tabla align-middle">
                <caption class="visually-hidden">Artículos de todos los autores</caption>
                <thead>
                    <tr>
                        <th scope="col">Título</th>
                        <th scope="col">Autor principal</th>
                        <th scope="col">Tema</th>
                        <th scope="col">Enviado</th>
                        <th scope="col">Estatus</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!cargando && filas.length === 0">
                        <td colspan="5">No hay artículos con esos criterios.</td>
                    </tr>
                    <tr v-for="a in filas" :key="a.id">
                        <td><a :href="a.url">{{ a.titulo }}</a></td>
                        <td>{{ a.autor }}</td>
                        <td>{{ a.tema }}</td>
                        <td class="text-nowrap">{{ a.fecha }}</td>
                        <td><span class="emp-insignia" :class="a.estatus.clase">{{ a.estatus.etiqueta }}</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p v-if="cargando" class="small" role="status">Cargando…</p>
        <Paginacion :meta="meta" @ir="irAPagina" />
    </div>
</template>
