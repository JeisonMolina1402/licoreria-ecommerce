@extends('layouts.app')

@section('titulo_modulo', 'Gestión de Tickets y Pedidos')

@section('content')
    <form action="{{ route('tickets.index') }}" method="GET" class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-5">
                    <input type="text" class="form-control" name="buscar_codigo"
                        placeholder="🔍 Buscar por código de reserva..." value="{{ request('buscar_codigo') }}">
                </div>
                <div class="col-md-4">
                    <select class="form-select" name="estado" onchange="this.form.submit()">
                        <option value="">Todos los Estados</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>🟡 Pendientes
                        </option>
                        <option value="pagado" {{ request('estado') == 'pagado' ? 'selected' : '' }}>🔵 Pagados</option>
                        <option value="entregado" {{ request('estado') == 'entregado' ? 'selected' : '' }}>🟢 Entregados
                        </option>
                        <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>🔴 Cancelados
                        </option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark w-100">Filtrar</button>
                        <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary w-100">Limpiar</a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="d-flex justify-content-between align-items-end mb-3">
        <h5 class="text-dark mb-0 d-none d-md-block">Lista de Tickets</h5>
        <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-sm fw-bold shadow-sm px-3">
            + Nueva Venta (POS)
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <strong>¡Éxito!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="bg-white p-3 rounded-3 shadow-sm mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-nowrap">
                <thead class="table-light">
                    <tr>
                        <th># ID</th>
                        <th>Código Reserva</th>
                        <th>Cliente / Cajero</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado Actual</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($tickets) && count($tickets) > 0)
                        @foreach ($tickets as $ticket)
                            <tr>
                                <td class="fw-bold text-muted">{{ $ticket->id }}</td>
                                <td class="fw-bold">{{ $ticket->codigo_reserva }}</td>
                                <td>{{ $ticket->user->name ?? 'Usuario Desconocido' }}</td>
                                <td>{{ $ticket->created_at->format('d/m/Y h:i A') }}</td>
                                <td class="fw-bold text-success">${{ number_format($ticket->total, 2) }}</td>
                                <td>
                                    @if ($ticket->estado == 'pendiente')
                                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Pendiente</span>
                                    @elseif($ticket->estado == 'pagado')
                                        <span class="badge bg-info text-dark px-3 py-2 rounded-pill">Pagado</span>
                                    @elseif($ticket->estado == 'entregado')
                                        <span class="badge bg-success px-3 py-2 rounded-pill">Entregado</span>
                                    @else
                                        <span class="badge bg-danger px-3 py-2 rounded-pill">Cancelado</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <button class="btn btn-sm btn-outline-dark fw-bold" data-bs-toggle="modal"
                                            data-bs-target="#modalDetalle{{ $ticket->id }}">
                                            👁️ Ver
                                        </button>

                                        <form action="{{ route('tickets.estado', $ticket->id) }}" method="POST"
                                            class="d-flex gap-2 mb-0">
                                            @csrf
                                            <select name="estado" class="form-select form-select-sm" style="width: 120px;">
                                                <option value="pendiente"
                                                    {{ $ticket->estado == 'pendiente' ? 'selected' : '' }}>Pendiente
                                                </option>
                                                <option value="pagado" {{ $ticket->estado == 'pagado' ? 'selected' : '' }}>
                                                    Pagado</option>
                                                <option value="entregado"
                                                    {{ $ticket->estado == 'entregado' ? 'selected' : '' }}>Entregado
                                                </option>
                                                <option value="cancelado"
                                                    {{ $ticket->estado == 'cancelado' ? 'selected' : '' }}>Cancelado
                                                </option>
                                            </select>
                                            <button type="submit"
                                                class="btn btn-sm btn-outline-primary fw-bold">Actualizar</button>
                                        </form>
                                    </div>

                                    <div class="modal fade" id="modalDetalle{{ $ticket->id }}" tabindex="-1"
                                        aria-hidden="true" style="white-space: normal;">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg">
                                                <div class="modal-header bg-dark text-white">
                                                    <h5 class="modal-title">🧾 Detalle de Ticket:
                                                        {{ $ticket->codigo_reserva }}</h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body bg-light">
                                                    <div class="mb-3 text-muted small">
                                                        <strong>Cajero/Cliente:</strong>
                                                        {{ $ticket->user->name ?? 'Desconocido' }} <br>
                                                        <strong>Fecha de emisión:</strong>
                                                        {{ $ticket->created_at->format('d/m/Y h:i A') }}
                                                    </div>

                                                    <div class="table-responsive">
                                                        <table
                                                            class="table table-sm table-bordered bg-white mb-0 text-nowrap">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Producto</th>
                                                                    <th class="text-center">Cant.</th>
                                                                    <th class="text-end">Precio</th>
                                                                    <th class="text-end">Subtotal</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($ticket->detalles as $detalle)
                                                                    <tr>
                                                                        <td>{{ $detalle->producto->nombre ?? 'Producto Eliminado' }}
                                                                        </td>
                                                                        <td class="text-center fw-bold">
                                                                            {{ $detalle->cantidad }}</td>
                                                                        <td class="text-end text-muted">
                                                                            ${{ number_format($detalle->precio_unitario, 2) }}
                                                                        </td>
                                                                        <td class="text-end fw-bold">
                                                                            ${{ number_format($detalle->cantidad * $detalle->precio_unitario, 2) }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <th colspan="3" class="text-end fs-6">Total a Pagar:
                                                                    </th>
                                                                    <th class="text-end fs-6 text-success">
                                                                        ${{ number_format($ticket->total, 2) }}</th>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top-0">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cerrar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <span class="fs-1 d-block mb-2">🧾</span>
                                Aún no hay tickets ni pedidos registrados.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $tickets->links() }}
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/tickets.js'])
@endpush
