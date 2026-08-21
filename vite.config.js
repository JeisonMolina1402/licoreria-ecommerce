import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Estilos (CSS/SASS)
                'resources/css/app.css', // Breeze
                'resources/sass/app.scss',
                'resources/css/tienda.css',
                
                // Scripts Base / Globales
                'resources/js/app.js',
                'resources/js/alertas.js', 
                'resources/js/notificaciones.js',
                
                // Scripts de la Tienda (Frontend)
                'resources/js/carrito.js',
                'resources/js/exito.js',
                
                // Scripts del Panel Administrativo (Backend)
                'resources/js/inventario.js',
                'resources/js/pos.js',
                'resources/js/reportes.js',
                'resources/js/usuarios.js',
                'resources/js/permisos.js',

                // Scripts de Roles y Permisos
                'resources/js/roles.js',
            ],
            refresh: true,
        }),
    ],
});