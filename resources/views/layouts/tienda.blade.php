<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Licorería Premium | @yield('titulo', 'Inicio')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/sass/app.scss', 'resources/css/tienda.css', 'resources/js/app.js'])
</head>

<body>

    <nav class="navbar navbar-tienda fixed-top py-2 shadow-sm">
        <div class="container-fluid px-3 px-lg-5 d-flex justify-content-between align-items-center">

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

            <div class="d-flex justify-content-center" style="flex: 1;">
                <a class="navbar-brand m-0 d-flex align-items-center" href="{{ route('tienda.index') }}">
                    <span style="font-size: 2rem; margin-right: 8px;">🍷</span>
                    <div class="d-flex flex-column text-dark text-start">
                        <span class="fw-bold"
                            style="font-family: 'Cinzel', serif; font-size: 1.1rem; line-height: 1;">LICORERÍA</span>
                        <span
                            style="font-size: 0.6rem; letter-spacing: 2px; font-weight: 600; font-family: 'Lora', serif;">WEB
                            STORE</span>
                    </div>
                </a>
            </div>

            <div class="d-flex justify-content-end align-items-center gap-3 gap-lg-4" style="flex: 1;">
                @auth
                    @if(in_array(Auth::user()->rol, ['admin', 'vendedor']))
                        <a href="{{ url('/home') }}" class="nav-icon text-dark text-decoration-none" title="Mi Panel">
                            <i class="fa-solid fa-chart-line fs-5"></i>
                        </a>
                    @endif

                    <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                        @csrf
                        <button type="submit" class="nav-icon border-0 bg-transparent text-danger p-0" title="Cerrar Sesión">
                            <i class="fa-solid fa-right-from-bracket fs-5"></i>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="nav-icon text-dark text-decoration-none" title="Iniciar Sesión">
                        <i class="fa-regular fa-user fs-5"></i>
                    </a>
                    <a href="{{ route('register') }}" class="nav-icon d-none d-sm-block text-dark text-decoration-none" title="Crear Cuenta">
                        <i class="fa-solid fa-user-plus fs-5"></i>
                    </a>
                @endauth

                <button class="nav-icon border-0 position-relative bg-transparent text-dark p-0" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#carritoOffcanvas" title="Mi Carrito">
                    <i class="fa-solid fa-cart-shopping fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                        id="contador-carrito" style="font-size: 0.65rem;">
                        0
                    </span>
                </button>
            </div>

        </div>
    </nav>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="menuMovil"
        style="width: 280px; background-color: var(--color_secundario);">
        <div class="offcanvas-header border-bottom border-secondary">
            <h5 class="offcanvas-title text-white" style="font-family: 'Cinzel', serif;">🍷 LICORERÍA</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>

        <div class="offcanvas-body d-flex flex-column pt-2 px-0">

            @yield('categorias_movil')

            <div class="mt-4 text-center px-4">
                <p class="text-white-50 mb-3" style="font-family: 'Lora', serif;">Síguenos en nuestras redes</p>
                <div class="d-flex justify-content-center gap-4">
                    <a href="#" class="text-white social-icon fs-3 m-0"><i
                            class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="text-white social-icon fs-3 m-0"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="text-white social-icon fs-3 m-0"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="#" class="text-white social-icon fs-3 m-0"><i class="fa-brands fa-whatsapp"></i></a>
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

    <main style="margin-top: 80px; min-height: 80vh;">
        @yield('content')
    </main>

    <footer style="background-color: var(--color_secundario);">
        <section class="footer mt-2 pt-5 px-5">
            <div class="container">
                <div class="row g-4">

                    <div
                        class="col-lg-3 col-md-6 d-flex align-items-center justify-content-center justify-content-lg-start">
                        <div class="text-center text-lg-start footer-logo">
                            <span style="font-size: 3rem;">🍷</span>
                            <h4 class="text-white mt-2 mb-0" style="font-family: 'Cinzel', serif;">LICORERÍA</h4>
                            <p class="text-white-50 mb-0"
                                style="letter-spacing: 2px; font-size: 0.8rem; font-family: 'Lora', serif;">WEB STORE
                            </p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <h5 class="text-uppercase mb-3"
                            style="color: var(--color_primario); font-family: 'Cinzel', serif;">Enlaces</h5>
                        <ul class="list-unstyled footer-links">
                            <li class="mb-2"><a href="{{ route('tienda.index') }}"
                                    class="text-white text-decoration-none">Inicio</a></li>
                            <li class="mb-2"><a href="#catalogo" class="text-white text-decoration-none">Catálogo
                                    de Licores</a></li>
                            <li class="mb-2"><a href="#" class="text-white text-decoration-none">Política de
                                    Privacidad</a></li>
                            <li class="mb-2"><a href="#" class="text-white text-decoration-none">Términos y
                                    Condiciones</a></li>
                        </ul>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <h5 class="text-uppercase mb-3"
                            style="color: var(--color_primario); font-family: 'Cinzel', serif;">Contáctanos</h5>
                        <div class="footer-contact-info text-white">
                            <p class="mb-2"><strong>Dirección:</strong> Quito, Conjuntos Paraisos del sur</p>
                            <p class="mb-2"><strong>Teléfono:</strong> +593 98 123 4567</p>
                            <p class="mb-2"><strong>Correo:</strong> ventas@licoreria.com</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 text-center text-md-start">
                        <h5 class="text-uppercase mb-3"
                            style="color: var(--color_primario); font-family: 'Cinzel', serif;">Síguenos</h5>
                        <div class="d-flex justify-content-center justify-content-lg-start gap-3 mt-3">
                            <a href="#" class="text-white social-icon"><i
                                    class="fa-brands fa-facebook-f fs-4"></i></a>
                            <a href="#" class="text-white social-icon"><i
                                    class="fa-brands fa-instagram fs-4"></i></a>
                            <a href="#" class="text-white social-icon"><i
                                    class="fa-brands fa-tiktok fs-4"></i></a>
                            <a href="#" class="text-white social-icon"><i
                                    class="fa-brands fa-whatsapp fs-4"></i></a>
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

    <div class="offcanvas offcanvas-end shadow" tabindex="-1" id="carritoOffcanvas"
        aria-labelledby="carritoOffcanvasLabel" style="width: 400px;">
        <div class="offcanvas-header border-bottom">
            <h5 id="carritoOffcanvasLabel" class="titulo-premium mb-0 d-flex align-items-center gap-2">
                <i class="fa-solid fa-cart-shopping" style="color: var(--color_primario);"></i> Mi Carrito
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
        </div>

        <div class="offcanvas-body d-flex flex-column p-0">
            <div id="contenedor-productos-carrito" class="flex-grow-1 overflow-auto p-3">

                <div class="text-center text-muted mt-5" id="carrito-vacio">
                    <i class="fa-solid fa-cart-arrow-down mb-3" style="font-size: 3rem; opacity: 0.2;"></i>
                    <h6 class="fw-bold" style="font-family: 'Cinzel', serif;">Tu carrito está vacío</h6>
                    <p class="small">¡Anímate a agregar algunas botellas!</p>
                </div>

            </div>

            <!-- Resumen y Formulario de Pago (Reemplaza el div anterior por este) -->
            <div class="border-top p-3 bg-light">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-uppercase fw-bold text-muted small" style="letter-spacing: 1px;">Total a
                        Pagar</span>
                    <span class="fw-bold fs-4" style="color: var(--color_primario);" id="total-carrito">$0.00</span>
                </div>

                <!-- Formulario oculto que enviará los datos a Laravel -->
                <form action="{{ route('checkout.procesar') }}" method="POST" id="form-checkout">
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

    @vite(['resources/js/app.js', 'resources/js/carrito.js'])

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
