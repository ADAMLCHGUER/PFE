// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.jsx', // Changé de app.js à app.jsx
            ],
            refresh: true,
        }),
        react(),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
        extensions: ['.js', '.jsx', '.json'], // Assurez-vous d'inclure les extensions
    },
    optimizeDeps: {
        include: ['react', 'react-dom', 'react-router-dom', 'axios'],
    }
});