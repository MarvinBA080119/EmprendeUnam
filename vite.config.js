import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Sistema (autores y administrador)
                'resources/scss/app.scss',
                'resources/js/app.js',
                // Sitio público (diseño propio del sitio)
                'resources/js/sitio.js',
                'resources/js/newsletter.js',
                'resources/css/sitio/premium_sections.css',
                'resources/css/sitio/institucional_suizo.css',
                'resources/css/sitio/secciones_vivas.css',
                'resources/css/sitio/formularios_media.css',
                'resources/js/sitio/premium_ui.js',
                'resources/js/sitio/interacciones.js',
                'resources/js/sitio/reproductor.js',
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                // Bootstrap 5.3 todavía usa @import y funciones globales de Sass.
                silenceDeprecations: ['import', 'global-builtin', 'color-functions', 'if-function'],
                quietDeps: true,
            },
        },
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
