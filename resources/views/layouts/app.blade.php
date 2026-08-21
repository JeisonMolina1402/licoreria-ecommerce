<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- TOKENS Y RUTAS PARA JAVASCRIPT --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="ruta-leer-notificaciones" content="{{ route('notificaciones.leer') }}">
    <meta name="ruta-check-notificaciones" content="{{ route('notificaciones.check') }}">

    <title>Panel Administrativo - Licorería</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css"
        integrity="sha512-QeR2VH+lsBE5LSAe1Q5EnTBbe7XTBubt8dG93Y7gidSgdMCr8nVqKcfKAMyN96SV8KDbZVTDXChatu5G2KQGzg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- VITE: CSS y JS unificados --}}
    {{-- VITE: CSS y JS unificados --}}
    @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/js/alertas.js', 'resources/js/notificaciones.js'])

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

        .menu-estatico::-webkit-scrollbar {
            width: 6px;
        }

        .menu-estatico::-webkit-scrollbar-thumb {
            background-color: #6c757d;
            border-radius: 8px;
        }

        .topbar-icon {
            color: #212529 !important;
            transition: all 0.3s ease-in-out;
        }

        .topbar-icon:hover {
            color: var(--color_primario, #f39c12) !important;
            transform: scale(1.08) translateY(-2px);
        }
    </style>

    @livewireStyles
</head>

<body class="bg-light">
    <div class="container-fluid p-0">
        <div class="row g-0">

            <!-- NAVBAR SUPERIOR RESPONSIVO (MÓVIL) -->
            <div
                class="col-12 d-md-none bg-dark p-3 d-flex justify-content-between align-items-center sticky-top shadow-sm">
                <img src="{{ asset('images/logos/logo.png') }}" alt="Licorería Admin"
                    style="height: 40px; filter: brightness(0) invert(1);">

                <!-- Reduje el gap de 4 a 3 para que los 3 íconos entren perfectos -->
                <div class="d-flex align-items-center gap-3">

                    <!-- BOTÓN TIENDA MÓVIL (Solo ícono) -->
                    <a href="{{ route('tienda.index') }}"
                        class="text-white text-decoration-none d-flex align-items-center" title="Ver Tienda Pública">
                        <i class="fa-solid fa-store fs-4"></i>
                    </a>

                    <!-- CAMPANA MÓVIL -->
                    <div class="dropdown">
                        <button
                            class="btn btn-link text-white position-relative border-0 p-0 text-decoration-none d-flex align-items-center btn-campana-notificaciones"
                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-bell fs-4"></i>
                            @if (isset($unreadCount) && $unreadCount > 0)
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                    style="font-size: 0.55rem;">{{ $unreadCount }}</span>
                            @endif
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3"
                            style="width: 320px; max-height: 400px; overflow-y: auto;">
                            <li>
                                <h6 class="dropdown-header fw-bold border-bottom pb-2">Notificaciones</h6>
                            </li>
                            @if (isset($notifications) && $notifications->count() > 0)
                                @foreach ($notifications as $notif)
                                    <li>
                                        <a class="dropdown-item d-flex align-items-start py-3 border-bottom {{ is_null($notif->read_at) ? 'bg-light' : '' }}"
                                            href="{{ $notif->data['url'] ?? '#' }}">
                                            <div class="me-3 mt-1"><i
                                                    class="{{ $notif->data['icono'] ?? 'fa-solid fa-bell' }} fs-5"></i>
                                            </div>
                                            <div style="white-space: normal;">
                                                <strong
                                                    class="d-block mb-1 {{ is_null($notif->read_at) ? 'text-dark' : 'text-muted' }}">{{ $notif->data['titulo'] ?? 'Notificación' }}</strong>
                                                <span
                                                    class="small text-muted d-block">{{ $notif->data['mensaje'] ?? '' }}</span>
                                                <small class="text-secondary"
                                                    style="font-size: 0.7rem;">{{ $notif->created_at->diffForHumans() }}</small>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            @else
                                <li><span class="dropdown-item text-center text-muted py-4 small">No tienes
                                        notificaciones nuevas</span></li>
                            @endif
                        </ul>
                    </div>

                    <!-- BOTÓN MENÚ HAMBURGUESA -->
                    <button class="btn btn-outline-light btn-sm ms-1" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#menuMovil">
                        <i class="fa-solid fa-bars me-1"></i> Menú
                    </button>
                </div>
            </div>

            <!-- SIDEBAR ESCRITORIO (PC) -->
            <nav class="col-md-2 d-none d-md-block bg-dark sidebar" style="min-height: 100vh;">
                <div class="position-sticky pt-4 menu-estatico"
                    style="top: 0; height: 100vh; overflow-y: auto; overflow-x: hidden;">
                    <div class="text-center mb-4 px-3 mt-3">
                        <a href="{{ route('home') }}"><img src="{{ asset('images/logos/logo.png') }}"
                                class="img-fluid" style="max-height: 100px; filter: brightness(0) invert(1);"></a>
                    </div>

                    <ul class="nav flex-column text-white px-2 pb-5">
                        <li class="nav-item mb-2"><a class="nav-link {{ request()->routeIs('home') || request()->routeIs('dashboard') ? 'active-menu' : 'text-white' }}" href="{{ route('home') }}"><i class="fa-solid fa-chart-pie me-2"></i> Dashboard</a></li>
                        
                        @can('ver auditoria')
                            <li class="nav-item mb-2"><a class="nav-link {{ request()->routeIs('auditoria.index') ? 'active-menu' : 'text-white' }}" href="{{ route('auditoria.index') }}"><i class="fa-solid fa-shield-halved me-2"></i> Auditoría</a></li>
                        @endcan
                        
                        @can('gestionar inventario')
                            <li class="nav-item mb-2"><a class="nav-link {{ request()->routeIs('inventario') ? 'active-menu' : 'text-white' }}" href="{{ route('inventario') }}"><i class="fa-solid fa-boxes-stacked me-2"></i> Inventario</a></li>
                        @endcan
                        
                        @can('gestionar tickets')
                            <li class="nav-item mb-2"><a class="nav-link {{ request()->routeIs('tickets.index') ? 'active-menu' : 'text-white' }}" href="{{ route('tickets.index') }}"><i class="fa-solid fa-receipt me-2"></i> Tickets</a></li>
                        @endcan
                        
                        @can('gestionar reportes')
                            <li class="nav-item mb-2"><a class="nav-link {{ request()->routeIs('reportes.index') ? 'active-menu' : 'text-white' }}" href="{{ route('reportes.index') }}"><i class="fa-solid fa-file-invoice me-2"></i> Reportes</a></li>
                        @endcan

                        {{-- Usamos @canany para que el menú principal aparezca si tiene al menos uno de los dos permisos --}}
                        @canany(['gestionar usuarios', 'gestionar roles y permisos'])
                            <li class="nav-item mb-2 mt-2">
                                <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#collapseSeguridad" role="button" aria-expanded="false" aria-controls="collapseSeguridad">
                                    <span><i class="fa-solid fa-gears me-2"></i> Configuración</span><i class="fa-solid fa-chevron-down small"></i>
                                </a>
                                <div class="collapse {{ request()->routeIs('usuarios.*') || request()->routeIs('roles.*') || request()->routeIs('permisos.*') ? 'show' : '' }}" id="collapseSeguridad">
                                    <ul class="nav flex-column ms-3 mt-1 pb-1" style="border-left: 2px solid #495057;">
                                        @can('gestionar usuarios')
                                            <li class="nav-item mb-1"><a class="nav-link py-1 {{ request()->routeIs('usuarios.*') ? 'text-white fw-bold' : 'text-muted' }}" href="{{ route('usuarios.index') }}"><i class="fa-solid fa-users me-2 small"></i> Usuarios</a></li>
                                        @endcan
                                        @can('gestionar roles y permisos')
                                            <li class="nav-item mb-1"><a class="nav-link py-1 {{ request()->routeIs('roles.*') ? 'text-white fw-bold' : 'text-muted' }}" href="{{ route('roles.index') }}"><i class="fa-solid fa-user-shield me-2 small"></i> Roles</a></li>
                                            <li class="nav-item mb-1"><a class="nav-link py-1 {{ request()->routeIs('permisos.*') ? 'text-white fw-bold' : 'text-muted' }}" href="{{ route('permisos.index') }}"><i class="fa-solid fa-key me-2 small"></i> Permisos</a></li>
                                        @endcan
                                    </ul>
                                </div>
                            </li>
                        @endcanany
                    </ul>
                </div>
            </nav>

            <!-- OFFCANVAS (MÓVIL LATERAL) -->
            <div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="menuMovil">
                <div
                    class="offcanvas-header border-bottom border-secondary position-relative justify-content-center py-3">
                    <img src="{{ asset('images/logos/logo.png') }}" alt="Licorería Menu"
                        style="height: 50px; filter: brightness(0) invert(1);">
                    <button type="button" class="btn-close btn-close-white position-absolute" style="right: 1.2rem;"
                        data-bs-dismiss="offcanvas"></button>
                </div>

                <div class="offcanvas-body d-flex flex-column px-0 pt-0">
                    <div class="bg-dark border-bottom border-secondary p-3 mb-3 shadow-sm">
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom border-secondary">
                            <div class="bg-light text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold me-3"
                                style="width: 45px; height: 45px; font-size: 1.3rem;">
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                            </div>
                            <div style="line-height: 1.2;">
                                <strong
                                    class="text-white d-block fs-5">{{ explode(' ', Auth::user()->name ?? 'Usuario')[0] }}</strong>
                                <span
                                    class="text-white-50 small">{{ Auth::user()->email ?? 'admin@licoreria.com' }}</span>
                            </div>
                        </div>

                        <div class="d-flex flex-column gap-1">
                            <a href="{{ route('profile.edit') }}" class="btn btn-dark text-start border-0 text-white-50 w-100 px-2 py-2"><i class="fa-solid fa-gear me-2"></i> Ajustes de Perfil</a>
                            <form action="{{ route('logout') }}" method="POST" class="m-0 p-0 form-cargando mt-2">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger w-100 text-start py-2 px-2 border-0 fw-bold"><i class="fa-solid fa-right-from-bracket me-2"></i> Cerrar Sesión</button>
                            </form>
                        </div>
                    </div>

                    <h6 class="text-white-50 px-3 mb-2 mt-1"
                        style="font-family: 'Cinzel', serif; font-size: 0.85rem; letter-spacing: 1px;"><i
                            class="fa-solid fa-layer-group me-2"></i> MÓDULOS</h6>

                    <ul class="nav flex-column text-white px-2 pb-5">
                        <li class="nav-item mb-2"><a class="nav-link {{ request()->routeIs('home') || request()->routeIs('dashboard') ? 'active-menu' : 'text-white' }}" href="{{ route('home') }}"><i class="fa-solid fa-chart-pie me-2"></i> Dashboard</a></li>
                        
                        @can('ver auditoria')
                            <li class="nav-item mb-2"><a class="nav-link {{ request()->routeIs('auditoria.index') ? 'active-menu' : 'text-white' }}" href="{{ route('auditoria.index') }}"><i class="fa-solid fa-shield-halved me-2"></i> Auditoría</a></li>
                        @endcan
                        
                        @can('gestionar inventario')
                            <li class="nav-item mb-2"><a class="nav-link {{ request()->routeIs('inventario') ? 'active-menu' : 'text-white' }}" href="{{ route('inventario') }}"><i class="fa-solid fa-boxes-stacked me-2"></i> Inventario</a></li>
                        @endcan
                        
                        @can('gestionar tickets')
                            <li class="nav-item mb-2"><a class="nav-link {{ request()->routeIs('tickets.index') ? 'active-menu' : 'text-white' }}" href="{{ route('tickets.index') }}"><i class="fa-solid fa-receipt me-2"></i> Tickets</a></li>
                        @endcan
                        
                        @can('gestionar reportes')
                            <li class="nav-item mb-2"><a class="nav-link {{ request()->routeIs('reportes.index') ? 'active-menu' : 'text-white' }}" href="{{ route('reportes.index') }}"><i class="fa-solid fa-file-invoice me-2"></i> Reportes</a></li>
                        @endcan

                        @canany(['gestionar usuarios', 'gestionar roles y permisos'])
                            <li class="nav-item mb-2 mt-2">
                                <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#collapseSeguridadMovil" role="button" aria-expanded="false" aria-controls="collapseSeguridadMovil">
                                    <span><i class="fa-solid fa-gears me-2"></i> Configuración</span><i class="fa-solid fa-chevron-down small"></i>
                                </a>
                                <div class="collapse {{ request()->routeIs('usuarios.*') || request()->routeIs('roles.*') || request()->routeIs('permisos.*') ? 'show' : '' }}" id="collapseSeguridadMovil">
                                    <ul class="nav flex-column ms-3 mt-1 pb-1" style="border-left: 2px solid #495057;">
                                        @can('gestionar usuarios')
                                            <li class="nav-item mb-1"><a class="nav-link py-1 {{ request()->routeIs('usuarios.*') ? 'text-white fw-bold' : 'text-muted' }}" href="{{ route('usuarios.index') }}"><i class="fa-solid fa-users me-2 small"></i> Usuarios</a></li>
                                        @endcan
                                        @can('gestionar roles y permisos')
                                            <li class="nav-item mb-1"><a class="nav-link py-1 {{ request()->routeIs('roles.*') ? 'text-white fw-bold' : 'text-muted' }}" href="{{ route('roles.index') }}"><i class="fa-solid fa-user-shield me-2 small"></i> Roles</a></li>
                                            <li class="nav-item mb-1"><a class="nav-link py-1 {{ request()->routeIs('permisos.*') ? 'text-white fw-bold' : 'text-muted' }}" href="{{ route('permisos.index') }}"><i class="fa-solid fa-key me-2 small"></i> Permisos</a></li>
                                        @endcan
                                    </ul>
                                </div>
                            </li>
                        @endcanany
                    </ul>
                </div>
            </div>

            <!-- CONTENIDO PRINCIPAL (MAIN) -->
            <main class="col-md-10 bg-light p-4" style="min-height: 100vh;">

                <!-- HEADER INTERNO PC -->
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center pb-3 mb-4 border-bottom gap-3">
                    
                    <!-- EL SECRETO ESTÁ AQUÍ: Le agregamos flex-grow-1 y quitamos cualquier límite de ancho -->
                    <div class="flex-grow-1">
                        <!-- Añadimos 'text-nowrap' opcional si queremos forzar 1 sola línea, o simplemente le quitamos clases restrictivas -->
                        <h2 class="h4 h3-md text-dark mb-1 fw-bold">@yield('titulo_modulo', 'Panel de Control')</h2>
                        <p class="text-muted small mb-0">@yield('subtitulo_modulo')</p>
                    </div>

                    <!-- Botones de la derecha (Tienda, Campana, Perfil) -->
                    <div class="d-none d-md-flex align-items-center flex-wrap gap-4 justify-content-end flex-shrink-0">
                        @yield('acciones_modulo')

                        <a href="{{ route('tienda.index') }}" class="text-decoration-none d-flex align-items-center topbar-icon" title="Ver Tienda Pública">
                            <i class="fa-solid fa-store fs-5"></i>
                            <span class="ms-2 fw-bold text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.5px;">Tienda</span>
                        </a>

                        <!-- CAMPANA PC -->
                        <div class="dropdown">
                            <button class="btn btn-link position-relative border-0 p-0 text-decoration-none d-flex align-items-center topbar-icon btn-campana-notificaciones" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-bell fs-5"></i>
                                @if (isset($unreadCount) && $unreadCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.55rem;">{{ $unreadCount }}</span>
                                @endif
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3" style="width: 320px; max-height: 400px; overflow-y: auto;">
                                <li>
                                    <h6 class="dropdown-header fw-bold border-bottom pb-2">Notificaciones</h6>
                                </li>
                                @if (isset($notifications) && $notifications->count() > 0)
                                    @foreach ($notifications as $notif)
                                        <li>
                                            <a class="dropdown-item d-flex align-items-start py-3 border-bottom {{ is_null($notif->read_at) ? 'bg-light' : '' }}" href="{{ $notif->data['url'] ?? '#' }}">
                                                <div class="me-3 mt-1"><i class="{{ $notif->data['icono'] ?? 'fa-solid fa-bell' }} fs-5"></i></div>
                                                <div style="white-space: normal;">
                                                    <strong class="d-block mb-1 {{ is_null($notif->read_at) ? 'text-dark' : 'text-muted' }}">{{ $notif->data['titulo'] ?? 'Notificación' }}</strong>
                                                    <span class="small text-muted d-block">{{ $notif->data['mensaje'] ?? '' }}</span>
                                                    <small class="text-secondary" style="font-size: 0.7rem;">{{ $notif->created_at->diffForHumans() }}</small>
                                                </div>
                                            </a>
                                        </li>
                                    @endforeach
                                @else
                                    <li><span class="dropdown-item text-center text-muted py-4 small">No tienes notificaciones nuevas</span></li>
                                @endif
                            </ul>
                        </div>

                        <!-- PERFIL PC -->
                        <div class="dropdown">
                            <button class="btn btn-link dropdown-toggle d-flex align-items-center border-0 p-0 text-decoration-none topbar-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-regular fa-circle-user fs-5"></i>
                                <strong class="ms-2 fw-bold text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.5px;">{{ explode(' ', Auth::user()->name)[0] ?? 'Usuario' }}</strong>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3">
                                <li><a class="dropdown-item text-muted py-2" href="{{ route('profile.edit') }}"><i class="fa-solid fa-gear me-2"></i> Ajustes</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="m-0 p-0 form-cargando">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger fw-bold py-2"><i class="fa-solid fa-right-from-bracket me-2"></i> Cerrar Sesión</button>
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
    @livewireScripts

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <div id="alertas-globales" data-success="{{ session('success') }}" data-error="{{ session('error') }}"
        data-validacion="{{ $errors->any() ? 'true' : 'false' }}" class="d-none"></div>

</body>

</html>
