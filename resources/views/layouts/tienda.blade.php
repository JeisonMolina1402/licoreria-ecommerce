<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- TOKEN CSRF: Vital para que AJAX/Fetch funcione en la tienda --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- TÍTULO DINÁMICO --}}
    <title>Licorería Premium | @yield('titulo', 'Inicio')</title>

    {{-- OPTIMIZACIÓN WEB --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600&family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Cinzel:wght@400;700&family=Lora:wght@400;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- VITE: CSS global, CSS específico de la tienda y JS base --}}
    @vite(['resources/sass/app.scss', 'resources/css/tienda.css', 'resources/js/app.js'])

    {{-- Custom styles for footer hover states --}}
    <style>
        .footer-link,
        .footer-contact-list p {
            transition: color 0.3s ease;
        }

        .footer-link:hover,
        .footer-contact-list p i:hover {
            color: #d1b48c !important;
            /* Un color dorado premium para hover */
        }

        .social-icon-footer-link {
            transition: transform 0.3s ease;
        }

        .social-icon-footer-link:hover {
            transform: scale(1.1);
        }
    </style>

</head>

<body>

    <!-- ========================================== -->
    <!-- NAVBAR PRINCIPAL (PÚBLICO) -->
    <!-- ========================================== -->
    <nav class="navbar navbar-tienda fixed-top py-2 shadow-sm">
        <div class="container-fluid px-3 px-lg-5 d-flex justify-content-between align-items-center">

            <!-- SECCIÓN IZQUIERDA -->
            <div class="d-flex align-items-center" style="flex: 1;">
                <button class="btn border-0 d-lg-none p-0 nav-icon" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#menuMovil">
                    <i class="fa-solid fa-bars fs-3"></i>
                </button>
                <div class="d-none d-lg-flex gap-3">
                    <a href="#" class="social-icon m-0"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="social-icon m-0"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="social-icon m-0"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="#" class="social-icon m-0"><i class="fa-brands fa-whatsapp"></i></a>
                </div>
            </div>

            <!-- SECCIÓN CENTRAL: Nuevo Logotipo Premium de la Marca -->
            <div class="d-flex justify-content-center" style="flex: 1;">
                <a class="navbar-brand m-0 d-flex align-items-center" href="{{ route('tienda.index') }}">
                    <img src="{{ asset('images/logos/logo.png') }}" alt="Licorería Premium Logo"
                        style="height: 70px; margin-right: 15px;">
                </a>
            </div>

            <!-- SECCIÓN DERECHA -->
            <div class="d-flex justify-content-end align-items-center gap-2 gap-lg-4" style="flex: 1;">
                @auth
                    {{-- SPATIE: Si el usuario tiene el rol 'admin' O 'vendedor' --}}
                    @hasanyrole('admin|vendedor')
                        <a href="{{ route('home') }}" class="btn btn-outline-dark btn-sm rounded-pill px-2 px-md-3"
                            title="Ver Panel">
                            <i class="fa-solid fa-chart-column"></i>
                            <span class="d-none d-md-inline ms-1">Panel Administrativo</span>
                        </a>
                    @endhasanyrole

                    {{-- SPATIE: Si el usuario NO ES 'admin' ni 'vendedor' (es decir, es solo un cliente) --}}
                    @unlessrole('admin|vendedor')
                        <a href="{{ route('tienda.mis-pedidos') }}"
                            class="btn btn-outline-dark btn-sm rounded-pill px-2 px-md-3" title="Ver mis Pedidos">
                            <i class="fa-solid fa-ticket"></i>
                            <span class="d-none d-md-inline ms-1">Mis Pedidos</span>
                        </a>
                    @endunlessrole

                    <!-- ... Aquí sigue tu campanita de notificaciones sin cambios ... -->

                    <!-- ========================================== -->
                    <!-- CAMPANITA DE NOTIFICACIONES (NUEVO)        -->
                    <!-- ========================================== -->
                    <div class="dropdown ms-2 me-2">
                        <button class="nav-icon border-0 position-relative bg-transparent text-dark p-0" type="button"
                            id="bellDropdownTienda" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-bell fs-5"></i>
                            @if (isset($unreadCount) && $unreadCount > 0)
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                    style="font-size: 0.6rem;">
                                    {{ $unreadCount }}
                                </span>
                            @endif
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2"
                            aria-labelledby="bellDropdownTienda" style="width: 320px; max-height: 400px; overflow-y: auto;">
                            <li>
                                <h6 class="dropdown-header fw-bold border-bottom pb-2">Mis Notificaciones</h6>
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
                                <li><span class="dropdown-item text-center text-muted py-4 small">No tienes notificaciones
                                        nuevas</span></li>
                            @endif
                        </ul>
                    </div>


                    <form action="{{ route('logout') }}" method="POST" class="m-0 p-0 form-cargando">
                        @csrf
                        <button type="submit" class="nav-icon border-0 bg-transparent text-danger p-0"
                            title="Cerrar Sesión">
                            <i class="fa-solid fa-right-from-bracket fs-5"></i>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="nav-icon text-dark text-decoration-none" title="Iniciar Sesión">
                        <i class="fa-regular fa-user fs-5"></i>
                    </a>
                    <a href="{{ route('register') }}" class="nav-icon d-none d-sm-block text-dark text-decoration-none"
                        title="Crear Cuenta">
                        <i class="fa-solid fa-user-plus fs-5"></i>
                    </a>
                @endauth
                <button class="nav-icon border-0 position-relative bg-transparent text-dark p-0" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#carritoOffcanvas" title="Mi Carrito">
                    <i class="fa-solid fa-cart-shopping fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                        id="contador-carrito" style="font-size: 0.65rem;">
                        0
                    </span>
                </button>
            </div>
        </div>
    </nav>

    <!-- ========================================== -->
    <!-- MENU OFFCANVAS MÓVIL (IZQUIERDA) -->
    <!-- ========================================== -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="menuMovil"
        style="width: 280px; background-color: var(--color_secundario);">
        <div class="offcanvas-header border-bottom border-secondary position-relative justify-content-center py-3">
            {{-- 🔥 CAMBIO: Logo más grande (50px) y centrado. El filtro lo vuelve blanco --}}
            <img src="{{ asset('images/logos/logo.png') }}" alt="Licorería Premium"
                style="height: 50px; filter: brightness(0) invert(1);">

            {{-- La "X" se posiciona de forma absoluta a la derecha para no dañar el centrado del logo --}}
            <button type="button" class="btn-close btn-close-white position-absolute" style="right: 1.2rem;"
                data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column pt-2 px-0">
            @yield('categorias_movil')
            <div class="mt-4 text-center px-4">
                <p class="text-white-50 mb-3" style="font-family: 'Lora', serif;">Síguenos en nuestras redes</p>
                <div class="d-flex justify-content-center gap-4">
                    <a href="#" class="text-white social-icon fs-3 m-0"><i
                            class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="text-white social-icon fs-3 m-0"><i
                            class="fa-brands fa-instagram"></i></a>
                </div>
            </div>
            @guest
                <div class="px-4 mt-5 mb-3">
                    <a href="{{ route('register') }}"
                        class="btn btn-premium rounded-pill w-100 text-uppercase fw-bold text-center py-2 d-block text-decoration-none">
                        Crear una Cuenta
                    </a>
                </div>
            @endguest
        </div>
    </div>

    <!-- ... Contenedor Principal ... -->
    <main style="margin-top: 80px; min-height: 80vh;">

        @yield('content')
    </main>

    <!-- ... Footer Unfinished ... -->
    <footer style="background-color: var(--color_secundario);">
        <section class="footer mt-2 pt-5 px-5">
            <div class="container">
                <div class="row g-4 text-white">

                    <!-- Columna 1: Logotipo (Blanco monocromático) -->
                    <div
                        class="col-lg-3 col-md-6 d-flex flex-column align-items-center justify-content-center justify-content-lg-start mb-4 mb-lg-0 text-center text-lg-start">
                        <a class="text-decoration-none m-0" href="{{ route('tienda.index') }}">
                            {{-- 🔥 CAMBIO: height aumentado a 130px y aplicación del filtro para volverlo 100% blanco --}}
                            <img src="{{ asset('images/logos/logo.png') }}" alt="Licorería Premium Logo Footer"
                                style="height: 100px; filter: brightness(0) invert(1);">
                        </a>
                    </div>

                    <!-- Columna 2: Enlaces Principales -->
                    <div
                        class="col-lg-3 col-md-6 d-flex flex-column align-items-center align-items-lg-start text-center text-lg-start mt-4 mt-md-0">
                        <h5 class="text-white mb-3" style="font-family: 'Cinzel', serif; letter-spacing: 1px;">ENLACES
                            PRINCIPALES</h5>
                        <div class="d-flex flex-column gap-2 footer-links-list">
                            <a href="{{ route('tienda.index') }}"
                                class="text-white-50 text-decoration-none small footer-link">Inicio</a>
                            <a href="#" class="text-white-50 text-decoration-none small footer-link">Nuestro
                                Catálogo</a>
                            <a href="{{ route('tienda.mis-pedidos') }}"
                                class="text-white-50 text-decoration-none small footer-link">Ver Mis Pedidos</a>
                            <a href="#" class="text-white-50 text-decoration-none small footer-link">Términos y
                                Condiciones</a>
                            <a href="#" class="text-white-50 text-decoration-none small footer-link">Política de
                                Envíos</a>
                        </div>
                    </div>

                    <!-- Columna 3: Información de Contacto (Localizado en Quito) -->
                    <div
                        class="col-lg-3 col-md-6 d-flex flex-column align-items-center align-items-lg-start text-center text-lg-start mt-4 mt-lg-0">
                        <h5 class="text-white mb-3" style="font-family: 'Cinzel', serif; letter-spacing: 1px;">
                            CONTÁCTANOS</h5>
                        <div class="d-flex flex-column gap-3 text-white-50 small footer-contact-list">
                            {{-- Localizado a Quito Oe3K y S46F --}}
                            <p class="m-0"><i class="fas fa-map-marker-alt me-2 text-white-50"></i> Quito, Oe3K y
                                S46F</p>
                            {{-- Ejemplo localized +593 --}}
                            <p class="m-0"><i class="fas fa-phone me-2 text-white-50"></i> +593 981766228</p>
                            {{-- Email plausible para licorería --}}
                            <p class="m-0"><i class="fas fa-envelope me-2 text-white-50"></i>
                                contacto@licoreriapremium.com</p>
                        </div>
                    </div>

                    <!-- Columna 4: Redes Sociales (Íconos fs-4) -->
                    <div
                        class="col-lg-3 col-md-6 d-flex flex-column align-items-center align-items-lg-start text-center text-lg-start mt-4 mt-lg-0">
                        <h5 class="text-white mb-3" style="font-family: 'Cinzel', serif; letter-spacing: 1px;">
                            SÍGUENOS</h5>
                        <div class="d-flex gap-3 social-icons-footer fs-4">
                            <a href="#" class="text-white social-icon-footer-link"><i
                                    class="fa-brands fa-facebook"></i></a>
                            <a href="#" class="text-white social-icon-footer-link"><i
                                    class="fa-brands fa-instagram"></i></a>
                            <a href="#" class="text-white social-icon-footer-link"><i
                                    class="fa-brands fa-tiktok"></i></a>
                            <a href="#" class="text-white social-icon-footer-link"><i
                                    class="fa-brands fa-whatsapp"></i></a>
                        </div>
                    </div>

                </div>
                <hr class="my-4 text-white opacity-25">
                <div class="row align-items-center py-2 pb-4">
                    <div class="col-12 text-center">
                        <p class="mb-0 text-white-50 small" style="font-family: 'Lora', serif;">
                            © {{ date('Y') }} Jeison Molina - Todos los Derechos Reservados
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </footer>
    <!-- ========================================== -->
    <!-- PANEL DEL CARRITO DE COMPRAS (OFFCANVAS)   -->
    <!-- Este componente interactúa 100% con carrito.js -->
    <!-- ========================================== -->
    <div class="offcanvas offcanvas-end shadow" tabindex="-1" id="carritoOffcanvas"
        aria-labelledby="carritoOffcanvasLabel" style="width: 400px;">
        <div class="offcanvas-header border-bottom">
            <h5 id="carritoOffcanvasLabel" class="titulo-premium mb-0 d-flex align-items-center gap-2">
                <i class="fa-solid fa-cart-shopping" style="color: var(--color_primario);"></i> Mi Carrito
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
        </div>

        <div class="offcanvas-body d-flex flex-column p-0">

            {{-- DOM OBJETIVO: Aquí carrito.js inyecta dinámicamente todo el código HTML de las botellas agregadas --}}
            <div id="contenedor-productos-carrito" class="flex-grow-1 overflow-auto p-3">

                {{-- ESTADO VACÍO (Empty State) --}}
                <div class="text-center text-muted mt-5" id="carrito-vacio">
                    <i class="fa-solid fa-cart-arrow-down mb-3" style="font-size: 3rem; opacity: 0.2;"></i>
                    <h6 class="fw-bold" style="font-family: 'Cinzel', serif;">Tu carrito está vacío</h6>
                    <p class="small">¡Anímate a agregar algunas botellas!</p>
                </div>
            </div>

            <!-- Resumen de Checkout y Formulario de Envío -->
            <div class="border-top p-3 bg-light">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-uppercase fw-bold text-muted small" style="letter-spacing: 1px;">Total a
                        Pagar</span>

                    {{-- TOTAL OBJETIVO: carrito.js sobrescribe este valor dinámicamente --}}
                    <span class="fw-bold fs-4" style="color: var(--color_primario);" id="total-carrito">$0.00</span>
                </div>

                {{-- FORMULARIO DE CHECKOUT --}}
                <form action="{{ route('checkout.procesar') }}" method="POST" id="form-checkout"
                    class="form-cargando">
                    @csrf
                    <input type="hidden" name="carrito_datos" id="carrito_datos" value="">
                    <button type="submit" class="btn btn-black w-100 py-3 rounded-3 disabled" id="btn-procesar-pago"
                        style="font-size: 1rem;">
                        Procesar Reserva
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- CARGA TARDÍA JS --}}
    @vite(['resources/js/app.js', 'resources/js/carrito.js'])



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
</body>

</html>
