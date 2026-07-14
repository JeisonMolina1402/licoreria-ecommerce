@extends('layouts.tienda')

@section('titulo', 'Reserva Exitosa')

@section('content')
    <div class="container py-5 mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <div class="text-center mb-4">
    <i class="fa-solid fa-circle-check text-success mb-3" style="font-size: 4rem;"></i>
    <h2 class="titulo-premium text-uppercase fw-bold">¡Reserva Confirmada!</h2>
    <p class="text-muted">Tu pedido ha sido registrado correctamente.</p>
</div>

<!-- NUEVO: Alerta de Instrucciones de Pago -->
<div class="alert alert-warning border-warning shadow-sm mb-4 mx-auto text-start" style="max-width: 600px; background-color: #fff9e6;">
    <h5 class="alert-heading fw-bold mb-3" style="color: #b07d00;">
        <i class="fa-solid fa-triangle-exclamation me-2"></i> ¡Último paso para asegurar tu compra!
    </h5>
    <p class="mb-3">Tu pedido está reservado, pero <strong>se cancelará automáticamente en 10 minutos</strong> si no recibimos el comprobante de pago.</p>
    
    <div class="bg-white p-3 border rounded shadow-sm mb-3">
        <p class="mb-2 fw-bold text-dark"><i class="fa-solid fa-building-columns me-2"></i> Datos para transferencia directa:</p>
        <ul class="mb-0 small text-dark list-unstyled ms-4" style="font-family: monospace; font-size: 0.9rem;">
            <li class="mb-1"><strong>Banco:</strong> Pichincha</li>
            <li class="mb-1"><strong>Tipo:</strong> Cuenta de Ahorros</li>
            <li class="mb-1"><strong>Número:</strong> 2200113344 <!-- Cambia este número --></li>
            <li class="mb-1"><strong>Titular:</strong> Licorería Web Store <!-- Cambia el titular --></li>
            <li><strong>Cédula/RUC:</strong> 1700000000001 <!-- Cambia el RUC --></li>
        </ul>
    </div>
    <p class="mb-0 small text-muted text-center">📲 Envía el comprobante al WhatsApp para liberar tu pedido.</p>
</div>

<!-- El Ticket de Reserva -->
<div class="mx-auto bg-white p-4 p-md-5 mb-4 shadow position-relative" id="ticket-reserva" style="max-width: 600px; border: 1px solid #eaeaea;">
    <!-- Borde decorativo superior e inferior -->
    <div class="position-absolute top-0 start-0 w-100" style="height: 10px; background: repeating-linear-gradient(45deg, #f39c12, #f39c12 10px, #ffffff 10px, #ffffff 20px);"></div>
    <div class="position-absolute bottom-0 start-0 w-100" style="height: 10px; background: repeating-linear-gradient(45deg, #f39c12, #f39c12 10px, #ffffff 10px, #ffffff 20px);"></div>

    <div class="text-center mb-4">
        <span style="font-size: 2.5rem;">🍷</span>
        <h3 class="fw-bold mb-0 mt-2" style="font-family: 'Cinzel', serif;">LICORERÍA WEB STORE</h3>
        <p class="text-muted small text-uppercase" style="letter-spacing: 2px;">Comprobante de Reserva</p>
    </div>

    <div class="d-flex justify-content-between border-bottom pb-3 mb-3 small" style="font-family: 'Courier New', Courier, monospace;">
        <div>
            <span class="text-muted d-block">Código de retiro:</span>
            <strong class="fs-5">{{ $ticket->codigo_reserva }}</strong>
        </div>
        <div class="text-end">
            <span class="text-muted d-block">Fecha:</span>
            <strong>{{ $ticket->created_at->format('d/m/Y H:i') }}</strong>
        </div>
    </div>

    <div class="mb-4 small" style="font-family: 'Courier New', Courier, monospace;">
        <span class="text-muted d-block">Cliente:</span>
        <strong class="text-uppercase">{{ $ticket->user->name ?? 'Cliente' }}</strong>
    </div>

    <div class="mb-4">
        <h6 class="text-muted small text-uppercase mb-3" style="letter-spacing: 1px;">Detalle de Compra</h6>
        @foreach($ticket->detalles as $detalle)
            <div class="d-flex justify-content-between mb-2 small" style="font-family: 'Courier New', Courier, monospace;">
                <span>{{ $detalle->cantidad }}x {{ $detalle->producto->nombre }}</span>
                <strong>${{ number_format($detalle->subtotal, 2) }}</strong>
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-between border-top border-dark pt-3 mb-4">
        <strong class="text-uppercase fs-5">Total a Pagar</strong>
        <strong class="fs-5" style="color: var(--color_primario);">${{ number_format($ticket->total, 2) }}</strong>
    </div>

    <!-- NUEVO: Advertencia dentro del ticket -->
    <div class="text-center mt-4 pt-3 text-muted border-top border-dashed" style="font-family: 'Courier New', Courier, monospace; font-size: 0.8rem; border-top-style: dashed;">
        <p class="mb-1 fw-bold text-danger" style="font-size: 0.9rem;">⚠️ VÁLIDO POR 10 MINUTOS</p>
        <p class="mb-1">Envía el comprobante de transferencia al WhatsApp:<br><strong class="text-dark">+593 98 176 6228</strong></p>
        <p class="mb-0 mt-3">Presenta este comprobante al retirar tu pedido.<br><strong>¡Gracias por tu preferencia!</strong></p>
    </div>
</div>

<!-- Los Botones de Acción -->
<div class="mt-4 d-flex justify-content-center flex-wrap gap-3">
    <button id="btn-descargar" data-codigo="{{ $ticket->codigo_reserva }}" class="btn btn-dark px-4 py-2 rounded-pill shadow-sm">
        <i class="fa-solid fa-download me-2"></i> Descargar Ticket
    </button>
    
    <!-- NUEVO: Botón directo a WhatsApp -->
    <a href="https://wa.me/593981766228?text=Hola,%20adjunto%20el%20comprobante%20de%20pago%20para%20mi%20reserva%20*{{ $ticket->codigo_reserva }}*" target="_blank" class="btn btn-success px-4 py-2 rounded-pill shadow-sm fw-bold">
        <i class="fa-brands fa-whatsapp me-2 fs-5 align-middle"></i> Enviar Comprobante
    </a>
    
    <a href="{{ route('tienda.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill shadow-sm">Volver al Catálogo</a>
</div>

            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    @vite(['resources/js/exito.js'])

@endsection
