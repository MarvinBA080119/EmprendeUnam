<script setup>
import { ref } from 'vue';
import Paginacion from '../comun/Paginacion.vue';
import { peticionJson } from '../comun/http';
import { useListado } from '../comun/useListado';

const props = defineProps({
    urlDatos: { type: String, required: true },
    filtros: { type: Object, default: () => ({}) },
});

const { filtros, filas, meta, cargando, error, irAPagina } = useListado(props.urlDatos, {
    q: props.filtros.q ?? '',
    estatus: props.filtros.estatus ?? '',
});

const aviso = ref('');
const activando = ref(null);

async function activar(autor) {
    if (!window.confirm(`¿Activar la cuenta de ${autor.nombre}? Se le avisará por correo.`)) return;
    activando.value = autor.id;
    aviso.value = '';
    error.value = '';
    try {
        const respuesta = await peticionJson(autor.urlActivar, { metodo: 'POST' });
        autor.activo = true;
        aviso.value = respuesta.mensaje;
    } catch (e) {
        error.value = e.message;
    } finally {
        activando.value = null;
    }
}
</script>

<template>
    <div>
        <form class="row g-2 align-items-end mb-3" role="search" @submit.prevent>
            <div class="col-12 col-md-6">
                <label for="aut-q" class="form-label">Buscar</label>
                <input id="aut-q" v-model="filtros.q" type="search" maxlength="100" class="form-control"
                       placeholder="Nombre, correo o usuario" />
            </div>
            <div class="col-12 col-md-4">
                <label for="aut-estatus" class="form-label">Estatus de la cuenta</label>
                <select id="aut-estatus" v-model="filtros.estatus" class="form-select">
                    <option value="">Todas</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="activa">Activa</option>
                </select>
            </div>
        </form>

        <div v-if="aviso" class="alert alert-success" role="status">{{ aviso }}</div>
        <div v-if="error" class="alert alert-danger" role="alert">{{ error }}</div>

        <div class="table-responsive" :aria-busy="cargando">
            <table class="table emp-tabla align-middle">
                <caption class="visually-hidden">Autores registrados</caption>
                <thead>
                    <tr>
                        <th scope="col">Nombre</th>
                        <th scope="col">Correo</th>
                        <th scope="col">Institución</th>
                        <th scope="col">Registro</th>
                        <th scope="col">Estatus</th>
                        <th scope="col"><span class="visually-hidden">Acciones</span></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!cargando && filas.length === 0">
                        <td colspan="6">No hay autores con esos criterios.</td>
                    </tr>
                    <tr v-for="a in filas" :key="a.id">
                        <td>
                            {{ a.nombre }}
                            <div class="small text-body-secondary">{{ a.usuario }}</div>
                        </td>
                        <td class="text-break">{{ a.correo }}</td>
                        <td>
                            {{ a.institucion }}
                            <div v-if="a.grado" class="small text-body-secondary">{{ a.grado }}</div>
                        </td>
                        <td>{{ a.registro }}</td>
                        <td>
                            <span v-if="a.activo" class="emp-insignia emp-insignia-aprobado">Activa</span>
                            <span v-else class="emp-insignia emp-insignia-revision">Pendiente</span>
                        </td>
                        <td class="text-end">
                            <button v-if="!a.activo" type="button" class="btn btn-primary btn-sm"
                                    :disabled="activando === a.id" @click="activar(a)">
                                {{ activando === a.id ? 'Activando…' : 'Activar' }}
                                <span class="visually-hidden"> la cuenta de {{ a.nombre }}</span>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p v-if="cargando" class="small" role="status">Cargando…</p>
        <Paginacion :meta="meta" @ir="irAPagina" />
    </div>
</template>
