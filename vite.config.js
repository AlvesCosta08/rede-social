import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '~': path.resolve(__dirname, 'resources'),
            '@': path.resolve(__dirname, 'resources'),
        },
    },
    build: {
        outDir: 'public/build',
        manifest: true,
        rollupOptions: {
            input: ['resources/css/app.css', 'resources/js/app.js']
        }
    },
    // ⭐ Para subdiretório
    base: '/sistemas/conexao-igreja/build/',
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'localhost',
        },
    },
});