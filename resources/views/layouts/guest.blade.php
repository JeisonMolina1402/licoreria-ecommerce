<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <!-- FontAwesome para los íconos (Ojito de contraseña) -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    
    <body class="font-sans text-gray-900 antialiased">
        <div class="flex min-h-screen bg-white">
            
            {{-- PANEL IZQUIERDO: Imagen fotográfica --}}
            <div class="hidden md:block md:w-1/2 lg:w-2/3 bg-cover bg-center bg-no-repeat relative"
                 style="background-image: url('{{ asset('images/logos/fondo-login.png') }}');">
                <div class="absolute inset-0 bg-black/40"></div>
            </div>

            {{-- PANEL DERECHO: Fondo gris muy claro para que la sombra sea visible --}}
            <div class="w-full md:w-1/2 lg:w-1/3 flex flex-col justify-center items-center p-4 sm:p-12 bg-gray-50 z-10">
                
                {{-- TARJETA FLOTANTE CON SOMBRA SUAVE (Estilo LinkedIn/Apple) --}}
                <div class="w-full max-w-sm bg-white px-8 py-10 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-gray-100">
                    
                    {{-- TU LOGO OFICIAL --}}
                    <div class="flex justify-center mb-8">
                        <a href="/">
                            <img src="{{ asset('images/logos/logo.png') }}" 
                                 alt="Logo Licorería" 
                                 class="h-20 object-contain">
                        </a>
                    </div>

                    {{-- Aquí se inyecta el Login, Registro o Verificación --}}
                    {{ $slot }}

                </div>
                
            </div>
        </div>
        {{-- Motor de SweetAlert2 --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </body>
</html>
    </body>
</html>