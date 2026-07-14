@extends('layouts.tienda')

@section('titulo', 'Mis Pedidos')

@section('content')
<div class="container py-5" style="margin-top: 80px; min-height: 70vh;">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="titulo-premium text-uppercase mb-0">🍷 Mi Historial de Reservas</h2>
                <a href="{{ route('tienda.index') }}" class="btn btn-outline-dark rounded-pill px-4">Volver al Catálogo</a>
            </div>

            @if($tickets->isEmpty())
                <div class="text-center py-5 bg-white shadow-sm rounded-4 border">
                    <i class="fa-solid fa-box-open text-muted mb-3" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mb-3">Aún no has realizado ninguna reserva.</h4>
                    <a href="{{ route('tienda.index') }}" class="btn btn-premium rounded-pill px-5">¡Hacer mi primer pedido!</a>
                </div>
            @else
                <div class="row g-4">
                    @foreach($tickets as $ticket)
                        <div class="col-md-6">
                            <div class="card shadow-sm border-0 h-100 rounded-4 position-relative">
                                
                                <!-- Etiqueta de Estado con colores de Admin -->
                                @php
                                    $claseEstado = match(strtolower($ticket->estado)) {
                                        'pendiente' => 'bg-warning text-dark',
                                        'pagado'    => 'bg-info text-dark',
                                        'entregado' => 'bg-success text-white',
                                        'cancelado' => 'bg-danger text-white',
                                        default     => 'bg-secondary text-white'
                                    };
                                @endphp
                                <span class="position-absolute top-0 end-0 mt-3 me-3 badge rounded-pill {{ $claseEstado }}" style="font-size: 0.85rem; padding: 0.5em 1em; letter-spacing: 0.5px;">
                                    {{ ucfirst($ticket->estado) }}
                                </span>

                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                            <i class="fa-solid fa-file-invoice fs-4 text-secondary"></i>
                                        </div>
                                        <div>
                                            <h5 class="fw-bold mb-0 text-uppercase" style="font-family: 'Courier New', Courier, monospace;">{{ $ticket->codigo_reserva }}</h5>
                                            <!-- Escapamos las letras 'd' y 'e' con \ para evitar que imprima la zona horaria -->
                                            <small class="text-muted">{{ $ticket->created_at->format('d \d\e M Y, H:i') }}</small>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <p class="mb-1 text-muted small text-uppercase fw-bold">Artículos:</p>
                                        <ul class="list-unstyled mb-0 small text-truncate">
                                            @foreach($ticket->detalles as $detalle)
                                                <li>- {{ $detalle->cantidad }}x {{ $detalle->producto->nombre }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-top-0 p-4 pt-0 d-flex justify-content-between align-items-center">
                                    <h5 class="fw-bold mb-0 text-success">${{ number_format($ticket->total, 2) }}</h5>
                                    <a href="{{ route('tienda.exito', $ticket->id) }}" class="btn btn-sm btn-dark rounded-pill px-3">
                                        Ver Comprobante <i class="fa-solid fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</div>
@endsection