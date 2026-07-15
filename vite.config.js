import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Estilos (CSS/SASS)
                'resources/sass/app.scss',
                'resources/css/tienda.css',

                //Breeze
                'resources/css/app.css',
                
                // Scripts Base
                'resources/js/app.js',
                
                // Scripts de la Tienda (Frontend)
                'resources/js/carrito.js',
                'resources/js/exito.js',
                
                // Scripts del Panel Administrativo (Backend)
                'resources/js/inventario.js',
                'resources/js/pos.js',
                'resources/js/tickets.js',
                'resources/js/reportes.js',
            ],
            refresh: true,
        }),
    ],
});