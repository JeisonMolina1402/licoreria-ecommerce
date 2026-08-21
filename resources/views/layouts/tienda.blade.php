<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- TOKENS Y RUTAS PARA JAVASCRIPT --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="ruta-leer-notificaciones" content="{{ route('notificaciones.leer') }}">
    <meta name="ruta-check-notificaciones" content="{{ route('notificaciones.check') }}">

    <title>Licorería Premium | @yield('titulo', 'Inicio')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600&family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Cinzel:wght@400;700&family=Lora:wght@400;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/sass/app.scss', 'resources/css/tienda.css', 'resources/js/app.js', 'resources/js/alertas.js', 'resources/js/notificaciones.js'])

    <style>
        .footer-link,
        .footer-contact-list p {
            transition: color 0.3s ease;
        }

        .footer-link:hover,
        .footer-contact-list p i:hover {
            color: #d1b48c !important;
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
    <!-- NAVBAR PRINCIPAL -->
    <nav class="navbar navbar-tienda fixed-top py-2 shadow-sm">
        <div class="container-fluid px-2 px-lg-5 d-flex justify-content-between align-items-center flex-nowrap">

            <div class="d-flex align-items-center" style="flex: 1;">
                <button class="btn border-0 d-lg-none p-1 nav-icon" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#menuMovil">
                    <i class="fa-solid fa-bars fs-4"></i>
                </button>
                <div class="d-none d-lg-flex gap-3">
                    <a href="https://www.facebook.com/jeison.molina.399?locale=es_LA" target="_blank" class="social-icon m-0"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/jeison_molina2001/" target="_blank" class="social-icon m-0"><i class="fa-brands fa-instagram"></i></a>
                    <a href="https://www.tiktok.com/@jeisonm1402" target="_blank" class="social-icon m-0"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="https://wa.me/593981766228" target="_blank" class="social-icon m-0"><i class="fa-brands fa-whatsapp"></i></a>
                </div>
            </div>

            <div class="d-flex justify-content-center" style="flex: 1;">
                <a class="navbar-brand m-0 d-flex align-items-center" href="{{ route('tienda.index') }}">
                    <img src="{{ asset('images/logos/logo.png') }}" alt="Licorería Premium"
                        style="height: clamp(45px, 6vw, 70px);">
                </a>
            </div>

            <!-- SECCIÓN DERECHA -->
            <div class="d-flex justify-content-end align-items-center gap-3 gap-lg-4" style="flex: 1;">
                
                <!-- BLOQUE 1: ÍCONOS PRINCIPALES (Siempre visibles en Móvil y PC) -->
                <div class="d-flex align-items-center gap-3">
                    @auth
                       {{-- A. PANEL / PEDIDOS --}}
                    @can('ver dashboard')
                        <a href="{{ route('home') }}" class="nav-icon p-1 text-decoration-none d-flex align-items-center" title="Ver Panel">
                            <i class="fa-solid fa-chart-column fs-5"></i>
                            <span class="nav-text d-none d-lg-inline text-uppercase ms-2 fw-bold">Administración</span>
                        </a>
                    @else
                        <a href="{{ route('tienda.mis-pedidos') }}" class="nav-icon p-1 text-decoration-none d-flex align-items-center" title="Mis Pedidos">
                            <i class="fa-solid fa-ticket fs-5"></i>
                            <span class="nav-text d-none d-lg-inline text-uppercase ms-2 fw-bold">Pedidos</span>
                        </a>
                    @endcan

                        {{-- B. CAMPANITA --}}
                        <div class="dropdown d-flex align-items-center">
                            <button class="nav-icon border-0 bg-transparent p-1 btn-campana-notificaciones" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="position-relative">
                                    <i class="fa-solid fa-bell fs-5"></i>
                                    @if (isset($unreadCount) && $unreadCount > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.55rem;">{{ $unreadCount }}</span>
                                    @endif
                                </div>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3" style="width: 320px; max-height: 400px; overflow-y: auto;">
                                <li><h6 class="dropdown-header fw-bold border-bottom pb-2">Mis Notificaciones</h6></li>
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
                    @else
                        {{-- INVITADOS: ENTRAR Y REGISTRARSE --}}
                        <a href="{{ route('login') }}" class="nav-icon p-1 text-decoration-none d-flex align-items-center" title="Iniciar Sesión">
                            <i class="fa-regular fa-user fs-5"></i>
                            <span class="nav-text d-none d-lg-inline text-uppercase ms-2 fw-bold">Entrar</span>
                        </a>
                        <a href="{{ route('register') }}" class="nav-icon p-1 text-decoration-none d-flex align-items-center" title="Crear Cuenta">
                            <i class="fa-solid fa-user-plus fs-5"></i>
                            <span class="nav-text d-none d-lg-inline text-uppercase ms-2 fw-bold">Registro</span>
                        </a>
                    @endauth

                    {{-- C. CARRITO (Siempre visible para todos) --}}
                    <button class="nav-icon border-0 bg-transparent p-1 pe-2 d-flex align-items-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#carritoOffcanvas" title="Mi Carrito">
                        <div class="position-relative">
                            <i class="fa-solid fa-cart-shopping fs-5"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="contador-carrito" style="font-size: 0.60rem;">0</span>
                        </div>
                    </button>
                </div>

                <!-- BLOQUE 2: PERFIL DE USUARIO (Separado y visible solo en PC) -->
                @auth
                    <div class="vr d-none d-lg-block mx-1"></div>

                    <div class="dropdown d-none d-lg-flex align-items-center">
                        <button class="nav-icon dropdown-toggle border-0 bg-transparent p-1 fw-bold d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-regular fa-circle-user fs-5"></i>
                            <span class="nav-text text-uppercase ms-2">{{ explode(' ', Auth::user()->name)[0] }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-3">
                            <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="fa-solid fa-gear me-2"></i> Ajustes</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="m-0 p-0 form-cargando">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger fw-bold py-2"><i class="fa-solid fa-right-from-bracket me-2"></i> Cerrar Sesión</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endauth

            </div>
        </div>
    </nav>

    <!-- OFFCANVAS MÓVIL (IZQUIERDA) -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="menuMovil" style="width: 280px; background-color: var(--color_secundario);">
        <div class="offcanvas-header border-bottom border-secondary position-relative justify-content-center py-3">
            <img src="{{ asset('images/logos/logo.png') }}" style="height: 50px; filter: brightness(0) invert(1);">
            <button type="button" class="btn-close btn-close-white position-absolute" style="right: 1.2rem;" data-bs-dismiss="offcanvas"></button>
        </div>

        <div class="offcanvas-body d-flex flex-column pt-0 px-0">

            {{-- CAJA DE PERFIL / INVITADO (Solo visible en celular) --}}
            <div class="d-lg-none bg-dark border-bottom border-secondary p-3 mb-2 shadow-sm">
                @auth
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom border-secondary">
                        <div class="bg-light text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold me-3" style="width: 45px; height: 45px; font-size: 1.3rem;">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <div style="line-height: 1.2;">
                            <strong class="text-white d-block fs-5">{{ explode(' ', Auth::user()->name ?? 'Usuario')[0] }}</strong>
                            <span class="text-white-50 small">{{ Auth::user()->email ?? '' }}</span>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-1">
                        <a href="{{ route('profile.edit') }}" class="btn btn-dark text-start border-0 text-white-50 w-100 px-2 py-2">
                            <i class="fa-solid fa-gear me-2"></i> Ajustes de Perfil
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="m-0 p-0 form-cargando mt-2">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100 text-start py-2 px-2 border-0 fw-bold">
                                <i class="fa-solid fa-right-from-bracket me-2"></i> Cerrar Sesión
                            </button>
                        </form>
                    </div>
                @else
                    <div class="d-flex flex-column gap-3 mt-2 mb-2 px-2">
                        <a href="{{ route('login') }}" class="btn btn-outline-light w-100 text-center py-2 fw-bold"><i class="fa-solid fa-user me-2"></i> Iniciar Sesión</a>
                        <a href="{{ route('register') }}" class="btn btn-outline-light w-100 text-center py-2 fw-bold"><i class="fa-solid fa-user-plus me-2"></i> Crear Cuenta</a>
                    </div>
                @endauth
            </div>

            {{-- CATEGORÍAS (Se eliminó el duplicado) --}}
            @yield('categorias_movil')

            <div class="mt-auto text-center px-4 py-4">
                <p class="text-white-50 mb-3" style="font-family: 'Lora', serif;">Síguenos en nuestras redes</p>
                <div class="d-flex justify-content-center gap-4">
                    <a href="https://www.facebook.com/jeison.molina.399?locale=es_LA" target="_blank" class="text-white social-icon fs-3 m-0"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/jeison_molina2001/" target="_blank" class="text-white social-icon fs-3 m-0"><i class="fa-brands fa-instagram"></i></a>
                    <a href="https://www.tiktok.com/@jeisonm1402" target="_blank" class="text-white social-icon fs-3 m-0"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="https://wa.me/593981766228" target="_blank" class="text-white social-icon fs-3 m-0"><i class="fa-brands fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
    </div>
    <main style="margin-top: 80px; min-height: 80vh;">
        @yield('content')
    </main>

    <footer style="background-color: var(--color_secundario);">
        <section class="footer mt-2 pt-5 px-5">
            <div class="container">
                <div class="row g-4 text-white">
                    <div
                        class="col-lg-3 col-md-6 d-flex flex-column align-items-center justify-content-center justify-content-lg-start mb-4 mb-lg-0 text-center text-lg-start">
                        <a class="text-decoration-none m-0" href="{{ route('tienda.index') }}">
                            <img src="{{ asset('images/logos/logo.png') }}" alt="Licorería Premium Logo Footer"
                                style="height: 100px; filter: brightness(0) invert(1);">
                        </a>
                    </div>

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
                            <a href="{{ route('legal.terminos') }}"
                                class="text-white-50 text-decoration-none small footer-link">Términos y Condiciones</a>
                            <a href="{{ route('legal.privacidad') }}"
                                class="text-white-50 text-decoration-none small footer-link">Política de Privacidad</a>
                        </div>
                    </div>

                    <div
                        class="col-lg-3 col-md-6 d-flex flex-column align-items-center align-items-lg-start text-center text-lg-start mt-4 mt-lg-0">
                        <h5 class="text-white mb-3" style="font-family: 'Cinzel', serif; letter-spacing: 1px;">
                            CONTÁCTANOS</h5>
                        <div class="d-flex flex-column gap-3 text-white-50 small footer-contact-list">
                            <p class="m-0"><i class="fas fa-map-marker-alt me-2 text-white-50"></i> Quito, Oe3K y
                                S46F</p>
                            <p class="m-0"><i class="fas fa-phone me-2 text-white-50"></i> +593 981766228</p>
                            <p class="m-0"><i class="fas fa-envelope me-2 text-white-50"></i>
                                contacto@licoreriapremium.com</p>
                        </div>
                    </div>

                    <div
                        class="col-lg-3 col-md-6 d-flex flex-column align-items-center align-items-lg-start text-center text-lg-start mt-4 mt-lg-0">
                        <h5 class="text-white mb-3" style="font-family: 'Cinzel', serif; letter-spacing: 1px;">
                            SÍGUENOS</h5>
                        <div class="d-flex gap-3 social-icons-footer fs-4">
                            <a href="https://www.facebook.com/jeison.molina.399?locale=es_LA" target="_blank"
                                class="text-white social-icon-footer-link"><i class="fa-brands fa-facebook"></i></a>
                            <a href="https://www.instagram.com/jeison_molina2001/" target="_blank"
                                class="text-white social-icon-footer-link"><i class="fa-brands fa-instagram"></i></a>
                            <a href="https://www.tiktok.com/@jeisonm1402" target="_blank"
                                class="text-white social-icon-footer-link"><i class="fa-brands fa-tiktok"></i></a>
                            <a href="https://wa.me/593981766228" target="_blank"
                                class="text-white social-icon-footer-link"><i class="fa-brands fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>
                <hr class="my-4 text-white opacity-25">
                <div class="row align-items-center py-2 pb-4">
                    <div class="col-12 text-center">
                        <p class="mb-0 text-white-50 small" style="font-family: 'Lora', serif;">© {{ date('Y') }}
                            Jeison Molina - Todos los Derechos Reservados</p>
                    </div>
                </div>
            </div>
        </section>
    </footer>

    <!-- CARRITO OFFCANVAS -->
    <div class="offcanvas offcanvas-end shadow" tabindex="-1" id="carritoOffcanvas" style="width: 400px;">
        <div class="offcanvas-header border-bottom">
            <h5 class="titulo-premium mb-0 d-flex align-items-center gap-2"><i class="fa-solid fa-cart-shopping"
                    style="color: var(--color_secundario);"></i> Mi Carrito</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column p-0">
            <div id="contenedor-productos-carrito" class="flex-grow-1 overflow-auto p-3">
                <div class="text-center text-muted mt-5" id="carrito-vacio">
                    <i class="fa-solid fa-cart-arrow-down mb-3" style="font-size: 3rem; opacity: 0.2;"></i>
                    <h6 class="fw-bold" style="font-family: 'Cinzel', serif;">Tu carrito está vacío</h6>
                    <p class="small">¡Anímate a agregar algunas botellas!</p>
                </div>
            </div>
            <div class="border-top p-3 bg-light">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-uppercase fw-bold text-muted small" style="letter-spacing: 1px;">Total a
                        Pagar</span>
                    <span class="fw-bold fs-4" style="color: var(--color_primario);" id="total-carrito">$0.00</span>
                </div>
                <form action="{{ route('checkout.procesar') }}" method="POST" id="form-checkout"
                    class="form-cargando">
                    @csrf
                    <input type="hidden" name="carrito_datos" id="carrito_datos" value="">
                    <button type="submit" class="btn btn-black w-100 py-3 rounded-3 disabled" id="btn-procesar-pago"
                        style="font-size: 1rem;">Procesar Reserva</button>
                </form>
            </div>
        </div>
    </div>

    @vite(['resources/js/carrito.js'])

</body>

</html>
