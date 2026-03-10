import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";
import legacy from '@vitejs/plugin-legacy';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/player.js',
                'resources/js/admin.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
        legacy({
            targets: ['defaults', 'Chrome >= 49', 'Safari >= 10', 'not dead'],
            additionalLegacyPolyfills: ['regenerator-runtime/runtime'],
        }),
    ],
    build: {
        target: 'es2015',
    },
    server: {
        cors: true,
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
