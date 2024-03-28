import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/style.css',
                'resources/css/bootstrap.css',
                'resources/js/app.js',
                'resources/js/bootstrap.js',
                'resources/js/bootstrap.bundle.min.js',
                'resources/js/jquery.min.js',
                'resources/js/custom.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            external: ['laravel-echo'],
        },
    }
});
