<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- TOKEN CSRF: Vital para la seguridad. Protege las peticiones POST/AJAX de tu frontend contra ataques de falsificación --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Panel Administrativo - Licorería</title>

    {{-- Precarga de fuentes para mejorar la velocidad de renderizado en el cliente --}}
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- VITE: Motor de construcción. Compila e inyecta el CSS (SASS) y JavaScript de la aplicación --}}
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    {{-- Estilos encapsulados para la interactividad de los menús (Transiciones y estado Hover) --}}
    <style>
        .sidebar .nav-link,
        .offcanvas .nav-link {
            transition: all 0.3s ease;
            color: #adb5bd !important;
            margin-bottom: 5px;
            border-radius: 8px;
        }

        .sidebar .nav-link:hover,
        .offcanvas .nav-link:hover {
            background-color: #495057 !important;
            color: #ffffff !important;
            transform: translateX(5px);
        }

        /* CLASE DINÁMICA: Se aplicará mediante Blade cuando el usuario esté en la ruta actual */
        .nav-link.active-menu {
            background-color: #6c757d !important;
            color: #ffffff !important;
            font-weight: bold;
        }

        /* CLASE DINÁMICA: Se aplicará mediante Blade cuando el usuario esté en la ruta actual */
        .nav-link.active-menu {
            background-color: #6c757d !important;
            color: #ffffff !important;
            font-weight: bold;
        }

        /* NUEVO: SCROLLBAR PERSONALIZADO PARA EL MENÚ ESTÁTICO */
        .menu-estatico::-webkit-scrollbar {
            width: 6px;
        }

        .menu-estatico::-webkit-scrollbar-thumb {
            background-color: #6c757d;
            border-radius: 8px;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container-fluid p-0">
        <div class="row g-0">

            <!-- ========================================== -->
            <!-- MENÚ MÓVIL (NAVBAR SUPERIOR RESPONSIVO)    -->
            <!-- Visible solo en pantallas pequeñas (d-md-none) -->
            <!-- ========================================== -->
            <div class="col-12 d-md-none bg-dark p-3 d-flex justify-content-between align-items-center">
                <img src="{{ asset('images/logos/logo.png') }}" alt="Licorería Admin"
                    style="height: 60px; filter: brightness(0) invert(1);">

                <!-- Botón disparador del panel lateral Offcanvas de Bootstrap -->
                <button class="btn btn-outline-light btn-sm" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#menuMovil">
                    ☰ Menú
                </button>
            </div>

            <!-- ========================================== -->
            <!-- SIDEBAR ESCRITORIO (MENÚ LATERAL FIJO)     -->
            <!-- Oculto en móviles, visible de md en adelante (d-none d-md-block) -->
            <!-- ========================================== -->
            <nav class="col-md-2 d-none d-md-block bg-dark sidebar" style="min-height: 100vh;">

                <!-- AQUÍ ESTÁ LA MAGIA: top: 0, height: 100vh y overflow-y: auto -->
                <div class="position-sticky pt-4 menu-estatico"
                    style="top: 0; height: 100vh; overflow-y: auto; overflow-x: hidden;">

                    <div class="text-center mb-4 px-3 mt-3">
                        <a href="{{ route('home') }}">
                            <img src="{{ asset('images/logos/logo.png') }}" alt="Licorería Panel" class="img-fluid"
                                style="max-height: 100px; filter: brightness(0) invert(1);">
                        </a>
                    </div>

                    <!-- Agregamos pb-5 (Padding Bottom) para que los últimos enlaces no queden pegados al borde inferior de la pantalla -->
                    <ul class="nav flex-column text-white px-2 pb-5">

                        {{-- ENRUTAMIENTO DINÁMICO (RouteIs) --}}
                        {{-- request()->routeIs() evalúa la URL actual. Si coincide con la ruta, imprime 'active-menu' --}}
                        <li class="nav-item mb-2">
                            <a class="nav-link {{ request()->routeIs('home') || request()->routeIs('dashboard') ? 'active-menu' : 'text-white' }}"
                                href="{{ route('home') }}">
                                📊 Dashboard
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link {{ request()->routeIs('inventario') ? 'active-menu' : 'text-white' }}"
                                href="{{ route('inventario') }}">
                                📦 Inventario
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link {{ request()->routeIs('tickets.index') ? 'active-menu' : 'text-white' }}"
                                href="{{ route('tickets.index') }}">
                                🎟️ Tickets
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link {{ request()->routeIs('reportes.index') ? 'active-menu' : 'text-white' }}"
                                href="{{ route('reportes.index') }}">
                                📄 Reportes
                            </a>
                        </li>

                        {{-- NUEVO ENLACE: MÓDULO DE USUARIOS --}}
                        <li class="nav-item mb-2">
                            <a class="nav-link {{ request()->routeIs('usuarios.*') ? 'active-menu' : 'text-white' }}"
                                href="{{ route('usuarios.index') }}">
                                👥 Usuarios
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- ========================================== -->
            <!-- OFFCANVAS (MENÚ DESPLEGABLE PARA MÓVILES)  -->
            <!-- Manipulado por Bootstrap JS desde el botón móvil superior -->
            <!-- ========================================== -->
            <div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="menuMovil">
                <div class="offcanvas-header border-bottom border-secondary">
                    <h5 class="offcanvas-title d-flex align-items-center">
                        <img src="{{ asset('images/logos/logo.png') }}" alt="Licorería Menu"
                            style="height: 60px; filter: brightness(0) invert(1);">
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body px-2">
                    <ul class="nav flex-column text-white">
                        <!-- Redundancia de los mismos enlaces y validaciones para la interfaz táctil -->
                        <li class="nav-item mb-2"><a
                                class="nav-link {{ request()->routeIs('home') || request()->routeIs('dashboard') ? 'active-menu' : 'text-white' }}"
                                href="{{ route('home') }}">📊 Dashboard</a></li>
                        <li class="nav-item mb-2"><a
                                class="nav-link {{ request()->routeIs('inventario') ? 'active-menu' : 'text-white' }}"
                                href="{{ route('inventario') }}">📦 Inventario</a></li>
                        <li class="nav-item mb-2"><a
                                class="nav-link {{ request()->routeIs('tickets.index') ? 'active-menu' : 'text-white' }}"
                                href="{{ route('tickets.index') }}">🎟️ Tickets</a></li>
                        <li class="nav-item mb-2">
                            <a class="nav-link {{ request()->routeIs('reportes.index') ? 'active-menu' : 'text-white' }}"
                                href="{{ route('reportes.index') }}">
                                📄 Reportes
                            </a>
                        </li>

                        {{-- NUEVO ENLACE MÓVIL: MÓDULO DE USUARIOS --}}
                        <li class="nav-item mb-2">
                            <a class="nav-link {{ request()->routeIs('usuarios.*') ? 'active-menu' : 'text-white' }}"
                                href="{{ route('usuarios.index') }}">
                                👥 Usuarios
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- CONTENIDO PRINCIPAL (MAIN)                 -->
            <!-- Ocupa las 10 columnas restantes del Grid en pantallas grandes -->
            <!-- ========================================== -->
            <main class="col-md-10 bg-light p-4" style="min-height: 100vh;">

                <!-- BARRA SUPERIOR (HEADER INTERNO DEL PANEL) -->
                <div
                    class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-3 mb-4 border-bottom">

                    <!-- TÍTULO Y SUBTÍTULO DINÁMICO -->
                    <div>
                        <h2 class="h3 text-dark mb-0 fw-bold">
                            @yield('titulo_modulo', 'Panel de Control')
                        </h2>
                        <small class="text-muted">@yield('subtitulo_modulo')</small>
                    </div>

                    <div class="d-flex align-items-center">

                        <!-- INYECCIÓN DE BOTONES ESPECÍFICOS DEL MÓDULO (Ej: + Nuevo Usuario, Exportar) -->
                        @yield('acciones_modulo')

                        {{-- Enlace de retorno al catálogo público --}}
                        <a href="{{ route('tienda.index') }}" class="btn btn-outline-dark btn-sm me-3 rounded-pill">
                            <i class="fa-solid fa-store me-1"></i> Ver Tienda Pública
                        </a>

                        {{-- MENÚ DROPDOWN DEL PERFIL DE USUARIO --}}
                        <div class="dropdown">
                            <!-- ... (El resto del botón de perfil y logout se queda exactamente igual) ... -->
                            <button
                                class="btn btn-light dropdown-toggle d-flex align-items-center border-0 bg-transparent"
                                type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <span
                                    class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                    style="width: 35px; height: 35px;">👤</span>
                                <strong
                                    class="d-none d-md-block text-dark">{{ Auth::user()->name ?? 'Usuario' }}</strong>
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2"
                                aria-labelledby="dropdownMenuButton">
                                <li><a class="dropdown-item text-muted" href="{{ route('profile.edit') }}">⚙️ Ajustes
                                        de Perfil</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger fw-bold">🚪 Cerrar
                                            Sesión</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- ÁREA DE RENDERIZADO DE LAS VISTAS HIJAS    -->
                <!-- ========================================== -->
                {{-- POLIMORFISMO ESTRUCTURAL: Soporta tanto componentes de Laravel Breeze ($slot) como herencia tradicional de Blade (@yield) --}}
                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endisset

            </main>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- STACK DE SCRIPTS (RENDIMIENTO)             -->
    <!-- ========================================== -->
    {{-- Permite que vistas hijas (como Inventario) empujen (push) sus propios scripts JS aquí, evitando cargar código innecesario en vistas que no lo necesitan --}}
    @stack('scripts')
</body>

</html>
