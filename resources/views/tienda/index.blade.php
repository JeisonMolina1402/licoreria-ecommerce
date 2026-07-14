@extends('layouts.tienda')

@section('titulo', 'Catálogo Exclusivo')

@section('content')

    <section class="hero-section position-relative vh-100" style="margin-top: -90px;">
        <div id="carruselLicoreria" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="3500">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="{{ asset('images/hero/hero1.jpg') }}" class="d-block w-100 vh-100"
                        style="object-fit: cover; filter: brightness(0.4);" alt="Licores">
                </div>
                <div class="carousel-item">
                    <img src="{{ asset('images/hero/hero2.jpg') }}" class="d-block w-100 vh-100"
                        style="object-fit: cover; filter: brightness(0.4);" alt="Vinos">
                </div>
                <div class="carousel-item">
                    <img src="{{ asset('images/hero/hero3.jpg') }}" class="d-block w-100 vh-100"
                        style="object-fit: cover; filter: brightness(0.4);" alt="Cocteles">
                </div>
            </div>
        </div>

        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center text-center"
            style="z-index: 2;">
            <div class="container px-4">
                <h1 class="display-2 text-uppercase text-white titulo-premium mb-3"
                    style="text-shadow: 2px 2px 8px rgba(0,0,0,0.5);">Sabor, Tradición y Elegancia</h1>
                <p class="fs-4 text-white"
                    style="font-family: 'Lora', serif; font-style: italic; text-shadow: 1px 1px 4px rgba(0,0,0,0.5);">
                    Encuentra las mejores botellas para tus momentos especiales.
                </p>
            </div>
        </div>
    </section>

    <section class="logo-slider-container">
        <div class="logo-slider">
            <div class="logo-track">
                <div class="slide_logo"><img src="{{ asset('images/logos/absolut-vodka.png') }}" alt="absolut vodka"></div>
                <div class="slide_logo"><img src="{{ asset('images/logos/chivas-regal.png') }}" alt="chivas regal"></div>
                <div class="slide_logo"><img src="{{ asset('images/logos/Club.png') }}" alt="Club"></div>
                <div class="slide_logo"><img src="{{ asset('images/logos/corona-extra.png') }}" alt="corona extra"></div>
                <div class="slide_logo"><img src="{{ asset('images/logos/Jack -Daniels.png') }}" alt="Jack Daniels"></div>
                <div class="slide_logo"><img src="{{ asset('images/logos/Norteno.png') }}" alt="Norteño"></div>
                <div class="slide_logo"><img src="{{ asset('images/logos/Pilsener.png') }}" alt="Pilsener"></div>
                <div class="slide_logo"><img src="{{ asset('images/logos/Ron-abuelo.png') }}" alt="ron abuelo"></div>
                <div class="slide_logo"><img src="{{ asset('images/logos/zhumir.png') }}" alt="zhumir"></div>
                <div class="slide_logo"><img src="{{ asset('images/logos/johnnie-walker.png') }}" alt="johnnie walker">
                </div>

            </div>
        </div>
    </section>

    <section class="container py-4 mt-3 mb-2">
        <div class="row g-4 text-center justify-content-center">

            <div class="col-12 col-md-4">
                <div class="beneficio-box h-100">
                    <i class="fa-solid fa-award mb-3" style="font-size: 2.8rem; color: var(--color_primario);"></i>
                    <h5 class="fw-bold text-uppercase mb-2"
                        style="font-family: 'Cinzel', serif; color: var(--color_secundario);">Garantía de Autenticidad</h5>
                    <p class="text-muted small mb-0" style="font-family: 'Lora', serif;">Licores 100% originales, sellados y
                        con registro de importación garantizado.</p>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="beneficio-box h-100">
                    <i class="fa-solid fa-stopwatch mb-3" style="font-size: 2.8rem; color: var(--color_primario);"></i>
                    <h5 class="fw-bold text-uppercase mb-2"
                        style="font-family: 'Cinzel', serif; color: var(--color_secundario);">Reserva Express</h5>
                    <p class="text-muted small mb-0" style="font-family: 'Lora', serif;">Tu pedido estará listo y empacado
                        para retirar en minutos, sin hacer filas.</p>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="beneficio-box h-100">
                    <i class="fa-solid fa-shield-halved mb-3" style="font-size: 2.8rem; color: var(--color_primario);"></i>
                    <h5 class="fw-bold text-uppercase mb-2"
                        style="font-family: 'Cinzel', serif; color: var(--color_secundario);">Pago Seguro</h5>
                    <p class="text-muted small mb-0" style="font-family: 'Lora', serif;">Validación de transferencias
                        directas al instante para tu total tranquilidad.</p>
                </div>
            </div>

        </div>
    </section>

    <div class="container py-2 " id="catalogo">

        <div class="text-center mb-5">
            <h2 class="titulo-premium mb-2 text-uppercase"
                style="font-size: 2.8rem; text-shadow: 1px 1px 2px var(--sombra_dorada);">Nuestro Catálogo</h2>
            <p class="text-muted" style="font-size: 1.1rem;">Selecciona tus botellas y resérvalas en minutos.</p>

            <form action="{{ route('tienda.index') }}#catalogo" method="GET"
                class="d-flex justify-content-center mx-auto mt-4" style="max-width: 650px;">
                <div class="position-relative w-100 me-2">
                    <input class="form-control form-control-lg rounded-pill bg-white shadow-sm" type="search"
                        name="buscar" placeholder="Busca tu licor favorito..." value="{{ request('buscar') }}"
                        style="padding-left: 1.5rem; border: 1px solid #e0e0e0; font-family: 'Lora', serif;">
                </div>
                <button class="btn btn-premium rounded-pill px-5 fw-bold text-uppercase shadow-sm" type="submit"
                    style="letter-spacing: 1px;">Buscar</button>
            </form>
        </div>

        <div class="row">
            <div class="col-lg-3 mb-4 d-none d-lg-block">
                <div class="card border border-light shadow-sm rounded-0 sticky-top" style="top: 110px;">
                    <div class="card-header bg-white fw-bold py-3 fs-5 border-bottom border-light">
                        <span style="color: var(--color_primario);">🏷️</span> Categorías
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('tienda.index') }}#catalogo"
                            class="list-group-item list-group-item-action py-3 {{ !request('categoria') ? 'fw-bold bg-light text-dark' : 'text-muted' }}"
                            style="{{ !request('categoria') ? 'border-left: 4px solid var(--color_primario);' : 'border-left: 4px solid transparent;' }}">
                            Todas las botellas
                        </a>
                        @foreach($categorias as $cat)
    <a href="{{ route('tienda.categoria', $cat->slug) }}#catalogo" 
       class="list-group-item list-group-item-action py-3 {{ request()->is('categoria/' . $cat->slug) ? 'fw-bold bg-light text-dark' : 'text-muted' }}" 
       style="{{ request()->is('categoria/' . $cat->slug) ? 'border-left: 4px solid var(--color_primario);' : 'border-left: 4px solid transparent;' }}">
        {{ $cat->nombre }}
    </a>
@endforeach
                    </div>
                </div>
            </div>

        @section('categorias_movil')
            <div class="w-100">
                <h6 class="text-uppercase text-white-50 mb-3 mt-3 px-4 text-start"
                    style="font-family: 'Cinzel', serif; font-size: 0.85rem;">🏷️ Nuestras Categorías</h6>
                <div class="list-group list-group-flush text-start">
                    <a href="{{ route('tienda.index') }}#catalogo"
                        class="list-group-item list-group-item-action bg-transparent text-white border-secondary py-3 px-4 {{ !request('categoria') ? 'fw-bold' : '' }}"
                        style="{{ !request('categoria') ? 'border-left: 3px solid var(--color_primario);' : 'border-left: 3px solid transparent;' }}">
                        Todas las botellas
                    </a>
                    @foreach ($categorias as $cat)
                        <a href="{{ route('tienda.categoria', $cat->slug) }}#catalogo"
                            class="list-group-item list-group-item-action bg-transparent text-white border-secondary py-3 px-4 {{ request()->is('categoria/' . $cat->slug) ? 'fw-bold' : '' }}"
                            style="{{ request()->is('categoria/' . $cat->slug) ? 'border-left: 3px solid var(--color_primario);' : 'border-left: 3px solid transparent;' }}">
                            {{ $cat->nombre }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endsection

        <div class="col-lg-9">
            <div class="row g-3">
                @forelse($productos as $producto)
                    <div class="col-6 col-md-4 col-xl-4">
                        <div class="card h-100 shadow-sm product-card">
                            <div class="card-img-container">
                                @if ($producto->imagen)
                                    <img src="{{ asset($producto->imagen) }}" alt="{{ $producto->nombre }}">
                                @else
                                    <span style="font-size: 3rem; color: #ccc;">🍾</span>
                                @endif
                            </div>

                            <div class="card-body d-flex flex-column text-center p-3">
                                <h5 class="titulo-producto" title="{{ $producto->nombre }}">{{ $producto->nombre }}
                                </h5>
                                <small class="text-muted mb-2">{{ $producto->descripcion ?? '750ml' }}</small>

                                <h4 class="fw-bold mb-3 mt-auto" style="color: var(--color_primario);">
                                    ${{ number_format($producto->precio, 2) }}</h4>

                                <!-- AQUÍ ESTÁ EL BOTÓN MODIFICADO PARA EL CARRITO -->
                                <button class="btn btn-outline-dark w-100 rounded-pill fw-bold btn-agregar"
                                    data-id="{{ $producto->id }}" data-nombre="{{ $producto->nombre }}"
                                    data-precio="{{ $producto->precio }}"
                                    data-imagen="{{ asset($producto->imagen) }}">
                                    + Agregar
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <span style="font-size: 4rem;">🍷</span>
                        <h3 class="text-muted mt-3">No encontramos licores con ese filtro.</h3>
                        <a href="{{ route('tienda.index') }}#catalogo"
                            class="btn btn-premium mt-3 rounded-pill px-4">Ver todo el catálogo</a>
                    </div>
                @endforelse
            </div>

            <div class="d-flex justify-content-center mt-5">
                {{ $productos->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<section class="container py-5 mt-2 border-top">
    <div class="text-center mb-5">
        <h2 class="titulo-premium mb-3 display-5 fw-bold" style="text-shadow: 1px 1px 2px var(--sombra_dorada);">¿Cómo
            realizar tu pedido?</h2>
        <p class="text-muted fs-5" style="font-family: 'Lora', serif; font-style: italic;">
            Compra rápido, seguro y sin complicaciones en 4 sencillos pasos.
        </p>
    </div>

    <div class="row g-4 text-center mt-3">

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="step-card">
                <div class="step-icon-wrapper mx-auto mb-4">
                    <i class="fa-solid fa-user-check" style="font-size: 2.5rem;"></i>
                    <span class="step-number">1</span>
                </div>
                <h5 class="fw-bold text-uppercase mb-2"
                    style="font-family: 'Cinzel', serif; color: var(--color_secundario);">Regístrate</h5>
                <p class="text-muted small px-2">Crea tu cuenta en nuestro sistema con tus datos básicos para comenzar.
                </p>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="step-card">
                <div class="step-icon-wrapper mx-auto mb-4">
                    <i class="fa-solid fa-wine-bottle" style="font-size: 2.5rem;"></i>
                    <span class="step-number">2</span>
                </div>
                <h5 class="fw-bold text-uppercase mb-2"
                    style="font-family: 'Cinzel', serif; color: var(--color_secundario);">Selecciona</h5>
                <p class="text-muted small px-2">Agrega tus licores favoritos al carrito desde nuestro catálogo
                    exclusivo.</p>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="step-card">
                <div class="step-icon-wrapper mx-auto mb-4">
                    <i class="fa-solid fa-money-bill-transfer" style="font-size: 2.5rem;"></i>
                    <span class="step-number">3</span>
                </div>
                <h5 class="fw-bold text-uppercase mb-2"
                    style="font-family: 'Cinzel', serif; color: var(--color_secundario);">Paga (10 min)</h5>
                <p class="text-muted small px-2">Realiza el pago por transferencia. Tienes 10 minutos para asegurar tu
                    reserva.</p>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="step-card">
                <div class="step-icon-wrapper mx-auto mb-4">
                    <i class="fa-solid fa-store" style="font-size: 2.5rem;"></i>
                    <span class="step-number">4</span>
                </div>
                <h5 class="fw-bold text-uppercase mb-2"
                    style="font-family: 'Cinzel', serif; color: var(--color_secundario);">Retira</h5>
                <p class="text-muted small px-2">Acércate a nuestro local con tu código de compra para retirar tus
                    productos.</p>
            </div>
        </div>

    </div>
</section>

<section id="faq" class="container py-5 mt-4 border-top">
    <div class="text-center mb-5">
        <h2 class="titulo-premium mb-2 display-5 fw-bold">Preguntas Frecuentes</h2>
        <p class="text-muted">Despeja tus dudas sobre nuestro sistema de reserva y retiro.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="accordion accordion-flush shadow-sm rounded-3 overflow-hidden border" id="accordionFaq">

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button fw-bold text-dark bg-white py-3" type="button"
                            data-bs-toggle="collapse" data-bs-target="#faq1">
                            ¿Qué pasa si no realizo la transferencia en los 10 minutos?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#accordionFaq">
                        <div class="accordion-body text-muted" style="font-family: 'Lora', serif;">
                            Nuestro sistema automatizado reserva el stock de tus botellas durante un lapso estricto de
                            10 minutos. Si el comprobante no es enviado dentro de ese tiempo, la orden se cancela
                            automáticamente y los productos vuelven a estar disponibles en el catálogo para otros
                            clientes.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold text-dark bg-white py-3" type="button"
                            data-bs-toggle="collapse" data-bs-target="#faq2">
                            ¿Qué requisitos necesito para retirar mi pedido en la tienda?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#accordionFaq">
                        <div class="accordion-body text-muted" style="font-family: 'Lora', serif;">
                            Al acercarte a nuestro local, únicamente debes presentar el **código único de compra**
                            generado por el sistema y tu documento de identidad para verificar que eres mayor de edad.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold text-dark bg-white py-3" type="button"
                            data-bs-toggle="collapse" data-bs-target="#faq3">
                            ¿Puedo solicitar que otra persona retire mi pedido?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#accordionFaq">
                        <div class="accordion-body text-muted" style="font-family: 'Lora', serif;">
                            Sí. Para que un tercero retire tu pedido, deberá presentar una foto del código de compra de
                            la orden, junto con su cédula. Recuerda que no entregamos licores a menores de edad bajo
                            ninguna circunstancia.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<section id="contacto" class="container py-5 mt-4 border-top">
    <div class="text-center mb-5">
        <h2 class="titulo-premium mb-2 text-uppercase"
            style="font-size: 2.8rem; text-shadow: 1px 1px 2px var(--sombra_dorada);">Encuéntranos</h2>
        <p class="text-muted">Estamos listos para brindarte la mejor atención y asesoría para tus eventos.</p>
    </div>

    <div class="row g-4 g-lg-5 align-items-stretch">

        <div class="col-lg-5 d-flex flex-column justify-content-between">

            <div class="mb-4">
                <div class="d-flex mb-4 align-items-start">
                    <a href="https://www.google.com/maps/search/?api=1&query=Quito,+Ecuador" target="_blank"
                        class="text-decoration-none" title="Abrir en Google Maps">
                        <i class="fa-solid fa-location-dot fs-3 me-3 mt-1 icon-contacto"></i>
                    </a>
                    <div>
                        <h5 class="fw-bold mb-1"
                            style="font-family: 'Cinzel', serif; color: var(--color_secundario);">Dirección</h5>
                        <p class="text-muted mb-0">Quito, Conjuntos Paraisos del sur</p>
                    </div>
                </div>

                <div class="d-flex mb-4 align-items-start">
                    <a href="https://wa.me/593981766228" target="_blank" class="text-decoration-none"
                        title="Escribir al WhatsApp">
                        <i class="fa-solid fa-phone fs-3 me-3 mt-1 icon-contacto"></i>
                    </a>
                    <div>
                        <h5 class="fw-bold mb-1"
                            style="font-family: 'Cinzel', serif; color: var(--color_secundario);">Teléfono</h5>
                        <a href="https://wa.me/593981766228" target="_blank" class="text-muted text-decoration-none"
                            style="transition: color 0.3s;" onmouseover="this.style.color='var(--color_primario)'"
                            onmouseout="this.style.color='#6c757d'">+593 98 176 6228</a>
                    </div>
                </div>

                <div class="d-flex mb-2 align-items-start">
                    <a href="mailto:ventas@licoreria.com" class="text-decoration-none"
                        title="Enviar un correo electrónico">
                        <i class="fa-solid fa-envelope fs-3 me-3 mt-1 icon-contacto"></i>
                    </a>
                    <div>
                        <h5 class="fw-bold mb-1"
                            style="font-family: 'Cinzel', serif; color: var(--color_secundario);">Correo Electrónico
                        </h5>
                        <a href="mailto:ventas@licoreria.com" class="text-muted text-decoration-none"
                            style="transition: color 0.3s;" onmouseover="this.style.color='var(--color_primario)'"
                            onmouseout="this.style.color='#6c757d'">ventas@licoreria.com</a>
                    </div>
                </div>
            </div>

            <div class="rounded-4 overflow-hidden shadow-sm w-100 mt-auto"
                style="height: 400px; border: 1px solid #eaeaea;">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d127640.89201083984!2d-78.58348123282216!3d-0.2104523282877395!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x91d59a4002422c9f%3A0x44b991e158ef5572!2sQuito!5e0!3m2!1ses!2sec!4v1700000000000!5m2!1ses!2sec"
                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-lg rounded-4 h-100 p-4 p-md-5"
                style="background-color: var(--color_secundario);">
                <h3 class="text-white mb-4" style="font-family: 'Cinzel', serif;">Envíanos un Mensaje</h3>

                <form id="formContactoWa">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label small text-uppercase fw-bold"
                                style="color: var(--color_primario); letter-spacing: 1px;">Nombre Completo</label>
                            <input type="text" class="form-control bg-transparent text-white" id="wa_nombre"
                                required style="border: 1px solid rgba(255,255,255,0.2);">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-uppercase fw-bold"
                                style="color: var(--color_primario); letter-spacing: 1px;">Correo</label>
                            <input type="email" class="form-control bg-transparent text-white" id="wa_correo"
                                required style="border: 1px solid rgba(255,255,255,0.2);">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small text-uppercase fw-bold"
                                style="color: var(--color_primario); letter-spacing: 1px;">Teléfono</label>
                            <input type="text" class="form-control bg-transparent text-white" id="wa_telefono"
                                required style="border: 1px solid rgba(255,255,255,0.2);">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small text-uppercase fw-bold"
                                style="color: var(--color_primario); letter-spacing: 1px;">Asunto</label>
                            <select class="form-select bg-transparent text-white" id="wa_asunto" required
                                style="border: 1px solid rgba(255,255,255,0.2);">
                                <option value="" class="text-dark" disabled selected>Selecciona una opción
                                </option>
                                <option value="Cotización para Eventos" class="text-dark">Cotización para Eventos
                                </option>
                                <option value="Consulta de Stock" class="text-dark">Consulta de Stock de Licores
                                </option>
                                <option value="Problemas con mi pedido" class="text-dark">Problemas con mi pedido
                                </option>
                                <option value="Otros" class="text-dark">Otros</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small text-uppercase fw-bold"
                                style="color: var(--color_primario); letter-spacing: 1px;">Mensaje</label>
                            <textarea class="form-control bg-transparent text-white" id="wa_mensaje" rows="4" required
                                style="border: 1px solid rgba(255,255,255,0.2);"></textarea>
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit"
                                class="btn btn-premium w-100 py-3 fw-bold text-uppercase rounded-3 shadow-sm"
                                style="letter-spacing: 1px;">
                                Enviar Mensaje Ahora
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
