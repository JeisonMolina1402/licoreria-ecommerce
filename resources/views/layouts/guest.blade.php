<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        {{-- TOKEN CSRF: Directiva de seguridad inyectada por Laravel para validar que el formulario de Login/Registro provenga de esta app y no de un atacante (Cross-Site Request Forgery) --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- TÍTULO DINÁMICO: Busca el nombre de la app en el archivo .env (APP_NAME), si no lo encuentra, usa 'Laravel' por defecto --}}
        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Preconexión a servidores de fuentes para optimizar el tiempo de carga (Performance) -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        {{-- VITE ASSET BUNDLING: Carga los estilos de Tailwind y los scripts base específicos para la autenticación --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    
    <!-- Clases utilitarias de Tailwind CSS para la tipografía base y el suavizado de fuentes (antialiased) -->
    <body class="font-sans text-gray-900 antialiased">
        
        <!-- CONTENEDOR PRINCIPAL: Usa Flexbox (flex) para centrar vertical y horizontalmente (sm:justify-center items-center) ocupando el 100% del alto de la pantalla (min-h-screen) -->
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            
            <!-- BLOQUE DEL LOGOTIPO -->
            <div>
                <a href="/">
                    {{-- COMPONENTE ANÓNIMO DE BLADE: La etiqueta <x- ...> no es HTML estándar. Le indica a Laravel que busque el archivo resources/views/components/application-logo.blade.php y lo inyecte aquí --}}
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <!-- TARJETA DEL FORMULARIO (CARD) -->
            <!-- Se adapta a dispositivos móviles (w-full) pero tiene un ancho máximo en pantallas grandes (sm:max-w-md), agregando sombra (shadow-md) y bordes redondeados (sm:rounded-lg) -->
            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                
                {{-- ARQUITECTURA BASADA EN COMPONENTES (SLOT) --}}
                {{-- A diferencia del @yield que usamos en app.blade.php, Breeze usa la variable $slot. Todo el código de auth/login.blade.php o auth/register.blade.php se inyectará matemáticamente en esta línea --}}
                {{ $slot }}
                
            </div>
        </div>
    </body>
</html>