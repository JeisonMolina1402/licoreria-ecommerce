<!-- wire:poll.15s actualiza la tabla cada 15 segundos automáticamente buscando nuevos tickets -->
<div wire:poll.10s>
    
    <!-- ========================================== -->
    <!-- MOTOR DE BÚSQUEDA Y FILTRADO EN TIEMPO REAL-->
    <!-- ========================================== -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-2">
                <!-- Búsqueda por Código -->
                <div class="col-md-5">
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="buscar_codigo" placeholder="🔍 Buscar por código de reserva..." autocomplete="off">
                </div>

                <!-- Filtro por Estado -->
                <div class="col-md-4">
                    <select class="form-select" wire:model.live="estado">
                        <option value="">Todos los Estados</option>
                        <option value="pendiente">🟡 Pendientes</option>
                        <option value="pagado">🔵 Pagados</option>
                        <option value="listo">🟣 Listos para retirar</option>
                        <option value="entregado">🟢 Entregados</option>
                        <option value="cancelado">🔴 Cancelados</option>
                    </select>
                </div>

                <!-- Botón Limpiar -->
                <div class="col-md-3">
                    <button type="button" wire:click="limpiar" class="btn btn-outline-secondary w-100">Limpiar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- CABECERA Y BOTÓN NUEVA VENTA               -->
    <!-- ========================================== -->
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h5 class="text-dark mb-0 d-none d-md-block">Lista de Tickets</h5>
        <!-- wire:navigate hace que la transición a la vista de caja sea instantánea -->
        <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-sm fw-bold shadow-sm px-3">
            + Nueva Venta (POS)
        </a>
    </div>

    <!-- ========================================== -->
    <!-- TABLA CENTRAL                              -->
    <!-- ========================================== -->
    <div class="bg-white p-3 rounded-3 shadow-sm mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-nowrap">
                <thead class="table-light">
                    <tr>
                        <th class="fw-bold text-muted">ID</th>
                        <th class="fw-bold">Código de Reserva</th>
                        <th>Usuario</th>
                        <th>Fecha</th>
                        <th class="fw-bold text-success">Total</th>
                        <th>Estado</th>
                        <th></th>
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
                                
                                <!-- Badges -->
                                <td>
                                    @if ($ticket->estado == 'pendiente')
                                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Pendiente</span>
                                    @elseif($ticket->estado == 'pagado')
                                        <span class="badge bg-info text-dark px-3 py-2 rounded-pill">Pagado</span>
                                    @elseif($ticket->estado == 'entregado')
                                        <span class="badge bg-success px-3 py-2 rounded-pill">Entregado</span>
                                    @elseif($ticket->estado == 'listo')
                                        <span class="badge bg-purple text-dark px-3 py-2 rounded-pill" style="background-color: #e0cffc;">Listo</span>
                                    @else
                                        <span class="badge bg-danger px-3 py-2 rounded-pill">Cancelado</span>
                                    @endif
                                </td>

                                <!-- Acciones -->
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <!-- Botón Ver -->
                                        <button class="btn btn-sm btn-outline-dark fw-bold" data-bs-toggle="modal" data-bs-target="#modalDetalle{{ $ticket->id }}">
                                            👁️ Ver
                                        </button>

                                        <!-- Formulario Actualizar Estado -->
                                        <form action="{{ route('tickets.estado', $ticket->id) }}" method="POST" class="d-flex gap-2 mb-0">
                                            @csrf
                                            <select name="estado" class="form-select form-select-sm" style="width: 120px;">
                                                <option value="pendiente" {{ $ticket->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                                <option value="pagado" {{ $ticket->estado == 'pagado' ? 'selected' : '' }}>Pagado</option>
                                                <option value="listo" {{ $ticket->estado == 'listo' ? 'selected' : '' }}>Listo para retirar</option>
                                                <option value="entregado" {{ $ticket->estado == 'entregado' ? 'selected' : '' }}>Entregado</option>
                                                <option value="cancelado" {{ $ticket->estado == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-outline-primary fw-bold">Actualizar</button>
                                        </form>
                                    </div>

                                    <!-- ========================================== -->
                                    <!-- MODAL DINÁMICO                             -->
                                    <!-- ========================================== -->
                                    <div wire:ignore.self class="modal fade" id="modalDetalle{{ $ticket->id }}" tabindex="-1" aria-hidden="true" style="white-space: normal;">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg">
                                                <div class="modal-header bg-dark text-white">
                                                    <h5 class="modal-title">🧾 Detalle de Ticket: {{ $ticket->codigo_reserva }}</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body bg-light">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered bg-white mb-0 text-nowrap">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th scope="col" class="text-uppercase text-secondary" style="font-size: 0.85rem;">Producto</th>
                                                                    <th scope="col" class="text-center text-uppercase text-secondary" style="font-size: 0.85rem;">Cant.</th>
                                                                    <th scope="col" class="text-end text-uppercase text-secondary" style="font-size: 0.85rem;">P. Unit</th>
                                                                    <th scope="col" class="text-end text-uppercase text-secondary" style="font-size: 0.85rem;">Subtotal</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($ticket->detalles as $detalle)
                                                                    <tr>
                                                                        <td class="align-middle">{{ $detalle->producto->nombre ?? 'Producto Eliminado' }}</td>
                                                                        <td class="text-center fw-bold align-middle">{{ $detalle->cantidad }}</td>
                                                                        <td class="text-end text-muted align-middle">${{ number_format($detalle->precio_unitario, 2) }}</td>
                                                                        <td class="text-end fw-bold align-middle">${{ number_format($detalle->cantidad * $detalle->precio_unitario, 2) }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                            <tfoot class="table-group-divider">
                                                                <tr>
                                                                    <td colspan="3" class="text-end fw-bold text-uppercase align-middle">Total a Cobrar:</td>
                                                                    <td class="text-end fw-bolder text-success fs-5">${{ number_format($ticket->total, 2) }}</td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top-0">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
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

    <!-- Paginación -->
    <div class="mt-4">
        {{ $tickets->links() }}
    </div>
</div>