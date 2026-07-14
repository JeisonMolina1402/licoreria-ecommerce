@extends('layouts.tienda')

@section('titulo', 'Reserva Exitosa')

@section('content')
    <div class="container py-5 mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <div class="text-center mb-4">
                    <i class="fa-solid fa-circle-check text-success" style="font-size: 5rem;"></i>
                    <h2 class="mt-3" style="font-family: 'Cinzel', serif;">¡Reserva Confirmada!</h2>
                    <p class="text-muted">Tu pedido ha sido procesado correctamente.</p>
                </div>

                <div class="card shadow border-0 mb-4" id="ticket-comprobante" style="background-color: #fff;">
                    <div
                        style="height: 10px; background: repeating-linear-gradient(45deg, var(--color_primario), var(--color_primario) 10px, transparent 10px, transparent 20px);">
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <span style="font-size: 2.5rem;">🍷</span>
                            <h4 class="fw-bold mt-2" style="font-family: 'Cinzel', serif;">LICORERÍA WEB STORE</h4>
                            <p class="text-muted small mb-0">Comprobante de Reserva</p>
                        </div>

                        <hr class="border-secondary opacity-25" style="border-style: dashed;">

                        <div class="row mb-3 small">
                            <div class="col-6">
                                <span class="text-muted d-block">Código de retiro:</span>
                                <strong class="fs-5">{{ $ticket->codigo_reserva }}</strong>
                            </div>
                            <div class="col-6 text-end">
                                <span class="text-muted d-block">Fecha:</span>
                                <strong>{{ $ticket->created_at->format('d/m/Y H:i') }}</strong>
                            </div>
                        </div>

                        <div class="mb-4 small">
                            <span class="text-muted d-block">Cliente:</span>
                            <strong>{{ $ticket->user->name }}</strong>
                        </div>

                        <h6 class="text-uppercase text-muted fw-bold small mb-3">Detalle de Compra</h6>

                        <table class="table table-sm border-white mb-4">
                            <tbody>
                                @foreach ($ticket->detalles as $detalle)
                                    <tr>
                                        <td class="text-start text-muted">{{ $detalle->cantidad }}x
                                            {{ $detalle->producto->nombre ?? 'Producto' }}</td>
                                        <td class="text-end fw-bold">
                                            ${{ number_format($detalle->cantidad * $detalle->precio_unitario, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="border-top border-dark">
                                    <th class="text-start pt-3 fs-5">TOTAL A PAGAR</th>
                                    <th class="text-end pt-3 fs-5" style="color: var(--color_primario);">
                                        ${{ number_format($ticket->total, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="text-center mt-4 pt-3" style="border-top: 1px dashed #ccc;">
                            <p class="small text-muted mb-0">Por favor, presenta este comprobante al retirar tu pedido.</p>
                            <p class="small fw-bold mt-1">¡Gracias por tu compra!</p>
                        </div>
                    </div>

                    <div
                        style="height: 10px; background: repeating-linear-gradient(-45deg, var(--color_primario), var(--color_primario) 10px, transparent 10px, transparent 20px);">
                    </div>
                </div>

                <div class="d-flex gap-3 justify-content-center">
                    <button id="btn-descargar" data-codigo="{{ $ticket->codigo_reserva }}"
                        class="btn btn-dark px-4 py-2 rounded-pill shadow-sm">
                        <i class="fa-solid fa-download me-2"></i> Descargar Ticket
                    </button>
                    <a href="{{ route('tienda.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill">
                        Volver al Catálogo
                    </a>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    @vite(['resources/js/exito.js'])

@endsection
