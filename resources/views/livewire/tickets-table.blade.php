<div wire:poll.10s>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <!-- align-items-end es clave aquí para que el botón se alinee abajo con los inputs -->
            <div class="row g-2 align-items-end">

                <!-- Buscador -->
                <div class="col-md-3">
                    <label class="form-label text-muted mb-1" style="font-size: 0.75rem; font-weight: 600;">Buscar
                        Ticket</label>
                    <div class="input-group input-group-sm shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><i
                                class="fa-solid fa-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0 ps-0 text-muted"
                            wire:model.live.debounce.300ms="buscar_codigo" placeholder="Código de reserva..."
                            autocomplete="off">
                    </div>
                </div>

                <!-- Estado -->
                <div class="col-md-3">
                    <label class="form-label text-muted mb-1"
                        style="font-size: 0.75rem; font-weight: 600;">Estado</label>
                    <select class="form-select form-select-sm text-muted shadow-sm" wire:model.live="estado">
                        <option value="">Todos los Estados</option>
                        <option value="pendiente">🟡 Pendientes</option>
                        <option value="pagado">🔵 Pagados</option>
                        <option value="listo">🟣 Listos para retirar</option>
                        <option value="entregado">🟢 Entregados</option>
                        <option value="cancelado">🔴 Cancelados</option>
                    </select>
                </div>

                <!-- Desde Fecha -->
                <div class="col">
                    <label class="form-label text-muted mb-1"
                        style="font-size: 0.75rem; font-weight: 600;">Desde</label>
                    <input type="date" class="form-control form-control-sm text-muted shadow-sm"
                        wire:model.live="fecha_inicio" title="Desde Fecha">
                </div>

                <!-- Hasta Fecha -->
                <div class="col">
                    <label class="form-label text-muted mb-1"
                        style="font-size: 0.75rem; font-weight: 600;">Hasta</label>
                    <input type="date" class="form-control form-control-sm text-muted shadow-sm"
                        wire:model.live="fecha_fin" title="Hasta Fecha">
                </div>

                <!-- Botón Limpiar (Estilo Auditoría) -->
                <div class="col-auto">
                    <button type="button" wire:click="limpiar" class="btn btn-outline-danger btn-sm shadow-sm"
                        title="Limpiar Filtros">
                        <span wire:loading.remove wire:target="limpiar"><i class="fa-solid fa-eraser"></i></span>
                        <span wire:loading wire:target="limpiar" class="spinner-border spinner-border-sm" role="status"
                            aria-hidden="true"></span>
                    </button>
                </div>

            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-end mb-3">
        <h5 class="text-dark mb-0 d-none d-md-block">Lista de Tickets</h5>
        <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-sm fw-bold shadow-sm px-3">
            + Nueva Venta (POS)
        </a>
    </div>

    <div class="bg-white p-3 rounded-3 shadow-sm mb-4 transition-all" wire:loading.class="opacity-50"
        wire:target="buscar_codigo, estado, limpiar" id="tabla-tickets">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-nowrap">
                <thead class="table-light">
                    <tr>
                        <th class="fw-bold text-muted">ID</th>
                        <th class="fw-bold">Código de Reserva</th>
                        <th>Usuario</th>
                        <th>Fecha</th>
                        <th class="fw-bold text-success">Total</th>
                        <th class="text-center">Comprobante</th>
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

                                <td class="text-center">
                                    @if ($ticket->comprobante_whatsapp)
                                        <button class="btn btn-sm btn-outline-success fw-bold shadow-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalVerComprobante{{ $ticket->id }}">
                                            <i class="fa-brands fa-whatsapp"></i> Ver
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-light border text-muted fw-bold"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalSubirComprobante{{ $ticket->id }}">
                                            <i class="fas fa-upload"></i> Subir
                                        </button>
                                    @endif
                                </td>

                                <td>
                                    @if ($ticket->estado == 'pendiente')
                                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Pendiente</span>
                                    @elseif($ticket->estado == 'pagado')
                                        <span class="badge bg-info text-dark px-3 py-2 rounded-pill">Pagado</span>
                                    @elseif($ticket->estado == 'entregado')
                                        <span class="badge bg-success px-3 py-2 rounded-pill">Entregado</span>
                                    @elseif($ticket->estado == 'listo')
                                        <span class="badge bg-purple text-dark px-3 py-2 rounded-pill"
                                            style="background-color: #e0cffc;">Listo</span>
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
                                            class="d-flex gap-2 mb-0 form-cargando">
                                            @csrf
                                            <select name="estado" class="form-select form-select-sm"
                                                style="width: 120px;">
                                                <option value="pendiente"
                                                    {{ $ticket->estado == 'pendiente' ? 'selected' : '' }}>Pendiente
                                                </option>
                                                <option value="pagado"
                                                    {{ $ticket->estado == 'pagado' ? 'selected' : '' }}>Pagado</option>
                                                <option value="listo"
                                                    {{ $ticket->estado == 'listo' ? 'selected' : '' }}>Listo para
                                                    retirar</option>
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

                                    <div wire:ignore.self class="modal fade" id="modalDetalle{{ $ticket->id }}"
                                        tabindex="-1" aria-hidden="true" style="white-space: normal;">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg">
                                                <div class="modal-header bg-dark text-white">
                                                    <h5 class="modal-title">🧾 Detalle de Ticket:
                                                        {{ $ticket->codigo_reserva }}</h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body bg-light">
                                                    <div class="table-responsive">
                                                        <table
                                                            class="table table-sm table-bordered bg-white mb-0 text-nowrap">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th scope="col"
                                                                        class="text-uppercase text-secondary"
                                                                        style="font-size: 0.85rem;">Producto</th>
                                                                    <th scope="col"
                                                                        class="text-center text-uppercase text-secondary"
                                                                        style="font-size: 0.85rem;">Cant.</th>
                                                                    <th scope="col"
                                                                        class="text-end text-uppercase text-secondary"
                                                                        style="font-size: 0.85rem;">P. Unit</th>
                                                                    <th scope="col"
                                                                        class="text-end text-uppercase text-secondary"
                                                                        style="font-size: 0.85rem;">Subtotal</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($ticket->detalles as $detalle)
                                                                    <tr>
                                                                        <td class="align-middle">
                                                                            {{ $detalle->producto->nombre ?? 'Producto Eliminado' }}
                                                                        </td>
                                                                        <td class="text-center fw-bold align-middle">
                                                                            {{ $detalle->cantidad }}</td>
                                                                        <td class="text-end text-muted align-middle">
                                                                            ${{ number_format($detalle->precio_unitario, 2) }}
                                                                        </td>
                                                                        <td class="text-end fw-bold align-middle">
                                                                            ${{ number_format($detalle->cantidad * $detalle->precio_unitario, 2) }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                            <tfoot class="table-group-divider">
                                                                <tr>
                                                                    <td colspan="3"
                                                                        class="text-end fw-bold text-uppercase align-middle">
                                                                        Total a Cobrar:</td>
                                                                    <td class="text-end fw-bolder text-success fs-5">
                                                                        ${{ number_format($ticket->total, 2) }}</td>
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

                                    <div wire:ignore.self class="modal fade"
                                        id="modalSubirComprobante{{ $ticket->id }}" tabindex="-1"
                                        aria-hidden="true" style="white-space: normal;">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <form action="{{ route('tickets.comprobante', $ticket->id) }}"
                                                method="POST" enctype="multipart/form-data"
                                                class="modal-content border-0 shadow-lg form-cargando">
                                                @csrf
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title fw-bold"><i class="fas fa-upload me-2"></i>
                                                        Adjuntar Comprobante</h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3 text-start">
                                                        <label class="form-label fw-bold">Ticket:
                                                            {{ $ticket->codigo_reserva }}</label>

                                                        <input type="file"
                                                            class="form-control @error('comprobante') is-invalid @enderror"
                                                            name="comprobante" accept="image/*" required>

                                                        @error('comprobante')
                                                            <div class="invalid-feedback fw-bold">{{ $message }}
                                                            </div>
                                                        @enderror

                                                        <small class="text-muted d-block mt-2">Sube la captura de
                                                            pantalla de la transferencia (WhatsApp, Banco, etc.)</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top-0">
                                                    <button type="button" class="btn btn-light"
                                                        data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary fw-bold">Guardar
                                                        Comprobante</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <div wire:ignore.self class="modal fade"
                                        id="modalVerComprobante{{ $ticket->id }}" tabindex="-1"
                                        aria-hidden="true" style="white-space: normal;">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg">
                                                <div class="modal-header bg-success text-white">
                                                    <h5 class="modal-title fw-bold"><i
                                                            class="fa-brands fa-whatsapp me-2"></i> Comprobante:
                                                        {{ $ticket->codigo_reserva }}</h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-center bg-light">
                                                    @if ($ticket->comprobante_whatsapp)
                                                        <img src="{{ asset($ticket->comprobante_whatsapp) }}"
                                                            alt="Comprobante"
                                                            class="img-fluid rounded shadow-sm border"
                                                            style="max-height: 60vh; object-fit: contain;">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <span class="fs-1 d-block mb-2">🧾</span>
                                Aún no hay tickets ni pedidos registrados.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <!-- FOOTER: PAGINACIÓN CENTRADA -->
    <div class="mt-4 pt-3 border-top overflow-auto paginacion-apilada" style="scrollbar-width: thin;">

        <!-- ESTILO PARA DOMAR LA PAGINACIÓN DE LARAVEL -->
        <style>
            /* 1. Ocultamos y destruimos los botones gigantes de celular para siempre */
            .paginacion-apilada nav > .d-sm-none {
                display: none !important;
            }

            /* 2. Forzamos a que la versión de PC se apile y se centre en celular */
            .paginacion-apilada nav > .d-none.d-sm-flex {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                gap: 10px;
                width: 100%;
            }

            /* 3. Centrar el texto en celular */
            .paginacion-apilada nav p {
                margin-bottom: 0 !important;
                text-align: center !important;
            }

            /* 🔥 4. MODO SÚPER COMPACTO PARA CELULARES 🔥 */
            @media (max-width: 575px) {
                /* Oculta todos los números sueltos y puntos suspensivos */
                .paginacion-apilada .pagination .page-item:not(.active):not(:first-child):not(:last-child) {
                    display: none !important;
                }
                /* Hace que los 3 botones que quedan sean anchos y cómodos para el dedo */
                .paginacion-apilada .pagination .page-link {
                    padding: 0.5rem 1.2rem !important;
                    font-weight: bold;
                    border-radius: 6px;
                }
                .paginacion-apilada .pagination {
                    gap: 5px;
                }
            }

            /* 5. En PC, restauramos la alineación a la izquierda */
            @media (min-width: 992px) {
                .paginacion-apilada nav > .d-none.d-sm-flex {
                    align-items: flex-start !important;
                }
                .paginacion-apilada nav p {
                    text-align: left !important;
                }
            }
        </style>

        {{ $tickets->links(data: ['scrollTo' => '#tabla-tickets']) }}

    </div>
</div>
