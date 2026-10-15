<script setup>
import { watch } from 'vue';
import Paginacion from '../comun/Paginacion.vue';
import { useListado } from '../comun/useListado';
import Resaltado from './Resaltado.vue';

const props = defineProps({
    urlResultados: { type: String, required: true },
    filtros: { type: Object, default: () => ({}) },
    temas: { type: Array, required: true },
    anios: { type: Array, required: true },
});

const { filtros, filas, meta, cargando, error, irAPagina, limpiar } = useListado(props.urlResultados, {
    q: props.filtros.q ?? '',
    tipo: props.filtros.tipo ?? '',
    tema: props.filtros.tema ?? '',
    anio: props.filtros.anio ?? '',
});

// El tema clasifica solo artículos.
watch(() => filtros.tipo, (tipo) => {
    if (tipo === 'curso') filtros.tema = '';
});
</script>

<template>
    <div>
        <form class="form-lux buscador__filtros" role="search" @submit.prevent>
            <div class="row g-3 align-items-end">
                <div class="col-12 col-lg-5">
                    <div class="form-floating">
                        <input id="bus-q" v-model="filtros.q" type="search" maxlength="100" class="form-control"
                               placeholder="Buscar" autocomplete="off" />
                        <label for="bus-q">Buscar</label>
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <div class="form-floating">
                        <select id="bus-tipo" v-model="filtros.tipo" class="form-select">
                            <option value="">Todo</option>
                            <option value="articulo">Artículos</option>
                            <option value="curso">Cursos</option>
                        </select>
                        <label for="bus-tipo">Tipo</label>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="form-floating">
                        <select id="bus-tema" v-model="filtros.tema" class="form-select" :disabled="filtros.tipo === 'curso'">
                            <option value="">Todos</option>
                            <option v-for="t in temas" :key="t.id" :value="String(t.id)">{{ t.nombre }}</option>
                        </select>
                        <label for="bus-tema">Tema (artículos)</label>
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <div class="form-floating">
                        <select id="bus-anio" v-model="filtros.anio" class="form-select">
                            <option value="">Todos</option>
                            <option v-for="a in anios" :key="a" :value="String(a)">{{ a }}</option>
                        </select>
                        <label for="bus-anio">Año</label>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-link px-0 mt-2" @click="limpiar">Limpiar búsqueda</button>
        </form>

        <p v-if="error" class="form-aviso visible form-aviso--error" role="alert">{{ error }}</p>

        <section class="buscador__resultados" :aria-busy="cargando" aria-label="Resultados de la búsqueda">
            <p v-if="!cargando && filas.length === 0" class="tem-vacio">
                No encontramos resultados con esos criterios. Pruebe con otras palabras o quite algún filtro.
            </p>

            <article v-for="r in filas" :key="`${r.tipo}-${r.id}`" class="post">
                <p class="eyebrow mb-0">
                    {{ r.tipo === 'curso' ? 'Curso' : 'Artículo' }}<template v-if="r.tema"> · {{ r.tema }}</template>
                    <template v-if="r.anio"> · {{ r.anio }}</template>
                </p>
                <h2 class="post__title">
                    <a v-if="r.url" :href="r.url" target="_blank" rel="noopener noreferrer"><Resaltado :segmentos="r.titulo" /></a>
                    <Resaltado v-else :segmentos="r.titulo" />
                </h2>
                <p v-if="r.autores.length" class="post__meta"><strong><Resaltado :segmentos="r.autores" /></strong></p>
                <p v-if="r.institucion.length" class="post__meta"><strong><Resaltado :segmentos="r.institucion" /></strong></p>
                <p v-if="r.palabras.length" class="post__excerpt">Palabras clave: <Resaltado :segmentos="r.palabras" /></p>
            </article>

            <p v-if="cargando" class="consultorio__nota" role="status">Buscando…</p>
        </section>

        <Paginacion v-if="meta.total" :meta="meta" @ir="irAPagina" />
    </div>
</template>
