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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css" integrity="sha512-QeR2VH+lsBE5LSAe1Q5EnTBbe7XTBubt8dG93Y7gidSgdMCr8nVqKcfKAMyN96SV8KDbZVTDXChatu5G2KQGzg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    

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

    @livewireStyles
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
                        {{-- SPATIE: Solo muestra esto si tiene el permiso exacto --}}
                        @can('ver auditoria')
                            <li class="nav-item mb-2">
                                <a class="nav-link {{ request()->routeIs('auditoria.index') ? 'active-menu' : 'text-white' }}"
                                    href="{{ route('auditoria.index') }}">
                                    🛡️ Auditoría
                                </a>
                            </li>
                        @endcan
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
                        @can('gestionar reportes')
                            <li class="nav-item mb-2">
                                <a class="nav-link {{ request()->routeIs('reportes.index') ? 'active-menu' : 'text-white' }}"
                                    href="{{ route('reportes.index') }}">
                                    📄 Reportes
                                </a>
                            </li>
                        @endcan


                        {{-- MENÚ AGRUPADO DE CONFIGURACIÓN / SEGURIDAD --}}
                        @can('gestionar usuarios') {{-- o 'gestionar roles y permisos' --}}
                            <li class="nav-item mb-2">
                                <a class="nav-link d-flex justify-content-between align-items-center"
                                    data-bs-toggle="collapse" href="#collapseSeguridad" role="button" aria-expanded="false"
                                    aria-controls="collapseSeguridad">
                                    <span><i class="fa-solid fa-gears me-2"></i> Configuración</span>
                                    <i class="fa-solid fa-chevron-down small"></i>
                                </a>
                                <div class="collapse {{ request()->routeIs('usuarios.*') || request()->routeIs('roles.*') || request()->routeIs('permisos.*') ? 'show' : '' }}"
                                    id="collapseSeguridad">
                                    <ul class="nav flex-column ms-3 mt-1 pb-1" style="border-left: 2px solid #495057;">

                                        @can('gestionar usuarios')
                                            <li class="nav-item mb-1">
                                                <a class="nav-link py-1 {{ request()->routeIs('usuarios.*') ? 'text-white fw-bold' : 'text-muted' }}"
                                                    href="{{ route('usuarios.index') }}">
                                                    <i class="fa-solid fa-users me-2 small"></i> Usuarios
                                                </a>
                                            </li>
                                        @endcan

                                        @can('gestionar roles y permisos')
                                            <li class="nav-item mb-1">
                                                <a class="nav-link py-1 {{ request()->routeIs('roles.*') ? 'text-white fw-bold' : 'text-muted' }}"
                                                    href="{{ route('roles.index') }}">
                                                    <i class="fa-solid fa-user-shield me-2 small"></i> Roles
                                                </a>
                                            </li>
                                            <li class="nav-item mb-1">
                                                <a class="nav-link py-1 {{ request()->routeIs('permisos.*') ? 'text-white fw-bold' : 'text-muted' }}"
                                                    href="{{ route('permisos.index') }}">
                                                    <i class="fa-solid fa-key me-2 small"></i> Permisos
                                                </a>
                                            </li>
                                        @endcan

                                    </ul>
                                </div>
                            </li>
                        @endcan
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
                        @can('ver auditoria')
                            <li class="nav-item mb-2">
                                <a class="nav-link {{ request()->routeIs('auditoria.index') ? 'active-menu' : 'text-white' }}"
                                    href="{{ route('auditoria.index') }}">
                                    🛡️ Auditoría
                                </a>
                            </li>
                        @endcan
                        <li class="nav-item mb-2"><a
                                class="nav-link {{ request()->routeIs('inventario') ? 'active-menu' : 'text-white' }}"
                                href="{{ route('inventario') }}">📦 Inventario</a></li>
                        <li class="nav-item mb-2"><a
                                class="nav-link {{ request()->routeIs('tickets.index') ? 'active-menu' : 'text-white' }}"
                                href="{{ route('tickets.index') }}">🎟️ Tickets</a></li>
                        @can('ver reportes')
                            <li class="nav-item mb-2">
                                <a class="nav-link {{ request()->routeIs('reportes.index') ? 'active-menu' : 'text-white' }}"
                                    href="{{ route('reportes.index') }}">
                                    📄 Reportes
                                </a>
                            </li>
                        @endcan

                        {{-- MENÚ AGRUPADO DE CONFIGURACIÓN / SEGURIDAD --}}
                        @can('gestionar usuarios') {{-- o 'gestionar roles y permisos' --}}
                            <li class="nav-item mb-2">
                                <a class="nav-link d-flex justify-content-between align-items-center"
                                    data-bs-toggle="collapse" href="#collapseSeguridad" role="button"
                                    aria-expanded="false" aria-controls="collapseSeguridad">
                                    <span><i class="fa-solid fa-gears me-2"></i> Configuración</span>
                                    <i class="fa-solid fa-chevron-down small"></i>
                                </a>
                                <div class="collapse {{ request()->routeIs('usuarios.*') || request()->routeIs('roles.*') || request()->routeIs('permisos.*') ? 'show' : '' }}"
                                    id="collapseSeguridad">
                                    <ul class="nav flex-column ms-3 mt-1 pb-1" style="border-left: 2px solid #495057;">

                                        @can('gestionar usuarios')
                                            <li class="nav-item mb-1">
                                                <a class="nav-link py-1 {{ request()->routeIs('usuarios.*') ? 'text-white fw-bold' : 'text-muted' }}"
                                                    href="{{ route('usuarios.index') }}">
                                                    <i class="fa-solid fa-users me-2 small"></i> Usuarios
                                                </a>
                                            </li>
                                        @endcan

                                        @can('gestionar roles y permisos')
                                            <li class="nav-item mb-1">
                                                <a class="nav-link py-1 {{ request()->routeIs('roles.*') ? 'text-white fw-bold' : 'text-muted' }}"
                                                    href="{{ route('roles.index') }}">
                                                    <i class="fa-solid fa-user-shield me-2 small"></i> Roles
                                                </a>
                                            </li>
                                            <li class="nav-item mb-1">
                                                <a class="nav-link py-1 {{ request()->routeIs('permisos.*') ? 'text-white fw-bold' : 'text-muted' }}"
                                                    href="{{ route('permisos.index') }}">
                                                    <i class="fa-solid fa-key me-2 small"></i> Permisos
                                                </a>
                                            </li>
                                        @endcan

                                    </ul>
                                </div>
                            </li>
                        @endcan
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

                        <!-- ========================================== -->
                        <!-- CAMPANITA DE NOTIFICACIONES (NUEVO)        -->
                        <!-- ========================================== -->
                        <div class="dropdown me-3">
                            <button class="btn btn-light position-relative border-0 bg-transparent" type="button"
                                id="bellDropdownAdmin" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-bell fs-5 text-dark"></i>
                                @if (isset($unreadCount) && $unreadCount > 0)
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                        style="font-size: 0.6rem;">
                                        {{ $unreadCount }}
                                    </span>
                                @endif
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2"
                                aria-labelledby="bellDropdownAdmin"
                                style="width: 320px; max-height: 400px; overflow-y: auto;">
                                <li>
                                    <h6 class="dropdown-header fw-bold border-bottom pb-2">Notificaciones</h6>
                                </li>

                                @if (isset($notifications) && $notifications->count() > 0)
                                    @foreach ($notifications as $notificacion)
                                        <li>
                                            <a class="dropdown-item d-flex align-items-start py-3 border-bottom {{ is_null($notificacion->read_at) ? 'bg-light' : '' }}"
                                                href="{{ $notificacion->data['url'] }}">
                                                <div class="me-3 mt-1">
                                                    <i class="{{ $notificacion->data['icono'] }} fs-5"></i>
                                                </div>
                                                <div style="white-space: normal;">
                                                    <strong
                                                        class="d-block mb-1 {{ is_null($notificacion->read_at) ? 'text-dark' : 'text-muted' }}">{{ $notificacion->data['titulo'] }}</strong>
                                                    <span
                                                        class="small text-muted d-block">{{ $notificacion->data['mensaje'] }}</span>
                                                    <small class="text-secondary"
                                                        style="font-size: 0.7rem;">{{ $notificacion->created_at->diffForHumans() }}</small>
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

                        {{-- MENÚ DROPDOWN DEL PERFIL DE USUARIO --}}
                        <div class="dropdown">
                            <!-- ... (El resto del botón de perfil y logout se queda exactamente igual) ... -->
                            <button
                                class="btn btn-light dropdown-toggle d-flex align-items-center border-0 bg-transparent"
                                type="button" id="dropdownMenuButton" data-bs-toggle="dropdown"
                                aria-expanded="false">
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
                                    <form action="{{ route('logout') }}" method="POST"
                                        class="m-0 p-0 form-cargando">
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
    @livewireScripts

    <!-- ========================================== -->
    <!-- SCRIPT DE NOTIFICACIONES EN TIEMPO REAL    -->
    <!-- ========================================== -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Seleccionamos ambos tipos de campana por si acaso
            const campanaAdmin = document.getElementById('bellDropdownAdmin');
            const campanaTienda = document.getElementById('bellDropdownTienda');
            const campanaActiva = campanaAdmin || campanaTienda;

            if (campanaActiva) {
                // 1. USAR EL EVENTO OFICIAL DE BOOTSTRAP (Se dispara justo cuando el menú se abre)
                // Esto garantiza al 100% que la petición se envíe sin importar la interfaz
                const dropdownElement = campanaActiva.closest('.dropdown');

                if (dropdownElement) {
                    dropdownElement.addEventListener('shown.bs.dropdown', function() {
                        let badge = campanaActiva.querySelector('.bg-danger');
                        if (badge) {
                            badge.remove(); // Borra el globito rojo visualmente al abrir

                            // Petición silenciosa a la base de datos
                            fetch('{{ route('notificaciones.leer') }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').content,
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                }
                            });
                        }
                    });
                }

                // 2. CONSULTA EN TIEMPO REAL (Cada 15 segundos) SIN RECARGAR
                setInterval(() => {
                    fetch('{{ route('notificaciones.check') }}')
                        .then(response => response.json())
                        .then(data => {
                            let badge = campanaActiva.querySelector('.bg-danger');
                            let currentCount = badge ? parseInt(badge.innerText) : 0;

                            // Si hay MÁS notificaciones nuevas de las que vemos en pantalla...
                            if (data.count > currentCount) {

                                // A. Alerta visual flotante (SweetAlert)
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        toast: true,
                                        position: 'top-end',
                                        icon: 'info',
                                        title: '¡Tienes una nueva notificación!',
                                        showConfirmButton: false,
                                        timer: 4000
                                    });
                                }

                                // B. Actualizar o crear el número rojo (Badge)
                                if (badge) {
                                    badge.innerText = data.count;
                                } else {
                                    campanaActiva.innerHTML +=
                                        `<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">${data.count}</span>`;
                                }

                                // C. Reconstruir la lista desplegable dinámicamente
                                let listaDropdown = campanaActiva.nextElementSibling;
                                let html =
                                    '<li><h6 class="dropdown-header fw-bold border-bottom pb-2">Notificaciones</h6></li>';

                                if (data.notificaciones.length > 0) {
                                    data.notificaciones.forEach(notif => {
                                        let isUnread = notif.read_at === null;
                                        let bgClass = isUnread ? 'bg-light' : '';
                                        let textClass = isUnread ? 'text-dark' : 'text-muted';

                                        html += `
                                            <li>
                                                <a class="dropdown-item d-flex align-items-start py-3 border-bottom ${bgClass}" href="${notif.data.url}">
                                                    <div class="me-3 mt-1">
                                                        <i class="${notif.data.icono} fs-5"></i>
                                                    </div>
                                                    <div style="white-space: normal;">
                                                        <strong class="d-block mb-1 ${textClass}">${notif.data.titulo}</strong>
                                                        <span class="small text-muted d-block">${notif.data.mensaje}</span>
                                                        <small class="text-secondary" style="font-size: 0.7rem;">${notif.tiempo}</small>
                                                    </div>
                                                </a>
                                            </li>
                                        `;
                                    });
                                } else {
                                    html +=
                                        '<li><span class="dropdown-item text-center text-muted py-4 small">No tienes notificaciones nuevas</span></li>';
                                }

                                listaDropdown.innerHTML = html;
                            }
                        });
                }, 5000); // 15 segundos
            }
        });
    </script>



    <!-- ========================================== -->
    <!-- ALERTAS GLOBALES (SUCCESS, ERROR, VALIDACION) -->
    <!-- ========================================== -->

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div id="alertas-globales" data-success="{{ session('success') }}" data-error="{{ session('error') }}"
        data-validacion="{{ $errors->any() ? 'true' : 'false' }}" class="d-none">
    </div>

    @vite(['resources/js/alertas.js'])


</body>

</html>
