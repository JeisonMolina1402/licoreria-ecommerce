<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Panel Administrativo - Licorería</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

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

        .nav-link.active-menu {
            background-color: #6c757d !important;
            color: #ffffff !important;
            font-weight: bold;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container-fluid p-0">
        <div class="row g-0">

            <div class="col-12 d-md-none bg-dark p-3 d-flex justify-content-between align-items-center">
                <h5 class="text-white mb-0">🍷 Licorería Admin</h5>
                <button class="btn btn-outline-light btn-sm" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#menuMovil">
                    ☰ Menú
                </button>
            </div>

            <nav class="col-md-2 d-none d-md-block bg-dark sidebar" style="min-height: 100vh;">
                <div class="position-sticky pt-4">
                    <h5 class="text-white text-center mb-4">🍷 Licorería</h5>
                    <ul class="nav flex-column text-white px-2">
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
                        <li class="nav-item mb-2"><a class="nav-link text-white" href="#">📄 Reportes</a></li>
                        <li class="nav-item mb-2"><a class="nav-link text-white" href="#">👥 Usuarios</a></li>
                    </ul>
                </div>
            </nav>

            <div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="menuMovil">
                <div class="offcanvas-header border-bottom border-secondary">
                    <h5 class="offcanvas-title">🍷 Licorería</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body px-2">
                    <ul class="nav flex-column text-white">
                        <li class="nav-item mb-2"><a
                                class="nav-link {{ request()->routeIs('home') || request()->routeIs('dashboard') ? 'active-menu' : 'text-white' }}"
                                href="{{ route('home') }}">📊 Dashboard</a></li>
                        <li class="nav-item mb-2"><a
                                class="nav-link {{ request()->routeIs('inventario') ? 'active-menu' : 'text-white' }}"
                                href="{{ route('inventario') }}">📦 Inventario</a></li>
                        <li class="nav-item mb-2"><a
                                class="nav-link {{ request()->routeIs('tickets.index') ? 'active-menu' : 'text-white' }}"
                                href="{{ route('tickets.index') }}">🎟️ Tickets</a></li>
                        <li class="nav-item mb-2"><a class="nav-link text-white" href="#">📄 Reportes</a></li>
                        <li class="nav-item mb-2"><a class="nav-link text-white" href="#">👥 Usuarios</a></li>
                    </ul>
                </div>
            </div>

            <main class="col-md-10 bg-light p-4" style="min-height: 100vh;">

                <div
                    class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-4 border-bottom">
                    <h2 class="h3 text-dark mb-0">
                        @yield('titulo_modulo', 'Panel de Control')
                    </h2>
                    <div class="d-flex align-items-center">

                        <a href="{{ route('tienda.index') }}" class="btn btn-outline-dark btn-sm me-3 rounded-pill">
                            <i class="fa-solid fa-store me-1"></i> Ver Tienda Pública
                        </a>

                        <div class="dropdown">
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
                                <li>
                                    <a class="dropdown-item text-muted" href="{{ route('profile.edit') }}">
                                        ⚙️ Ajustes de Perfil
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger fw-bold">
                                            🚪 Cerrar Sesión
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endisset

            </main>
        </div>
    </div>  
    @stack('scripts')
</body>

</html>
