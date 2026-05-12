import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            /**
             * NOTE: The input files here (resources/css/app.css, resources/js/app.js)
             * are NOT currently referenced in any Blade template.
             * Dashboard assets use plain CSS/JS in public/css and public/js.
             * This Vite pipeline exists for future public-page bundling
             * or if you decide to migrate dashboard assets here.
             */
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});