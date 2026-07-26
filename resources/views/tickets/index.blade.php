@extends('layouts.app')

<!-- Inyección a la plantilla maestra -->
@section('titulo_modulo', 'Gestión de Tickets y Pedidos')
@section('subtitulo_modulo', 'Administra el estado de las ventas y reservas de los clientes')

@section('content')
    <!-- ========================================== -->
    <!-- MOTOR DE BÚSQUEDA Y FILTRADO               -->
    <!-- ========================================== -->
    <form action="{{ route('tickets.index') }}" method="GET" class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-2">
                
                <!-- Búsqueda por Código -->
                <div class="col-md-5">
                    {{-- RETENCIÓN DE ESTADO: request('buscar_codigo') mantiene el texto escrito en el input después de recargar la página --}}
                    <input type="text" class="form-control" name="buscar_codigo"
                        placeholder="🔍 Buscar por código de reserva..." value="{{ request('buscar_codigo') }}">
                </div>
                
                <!-- Filtro por Estado -->
                <div class="col-md-4">
                    {{-- EVENTO NATIVO: onchange dispara el submit del formulario automáticamente al seleccionar una opción --}}
                    <select class="form-select" name="estado" onchange="this.form.submit()">
                        <option value="">Todos los Estados</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>🟡 Pendientes</option>
                        <option value="pagado" {{ request('estado') == 'pagado' ? 'selected' : '' }}>🔵 Pagados</option>
                        <option value="entregado" {{ request('estado') == 'entregado' ? 'selected' : '' }}>🟢 Entregados</option>
                        <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>🔴 Cancelados</option>
                    </select>
                </div>
                
                <!-- Botones de Acción -->
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark w-100">Filtrar</button>
                        {{-- Botón Limpiar: Simplemente redirige a la ruta base sin parámetros GET --}}
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

    <!-- ========================================== -->
    <!-- MANEJO DE ALERTAS GLOBALES (Error Bags)    -->
    <!-- ========================================== -->
    @if (session('success'))
        <!-- Alerta de éxito (ej. al actualizar un estado correctamente) -->
    @endif

    @if ($errors->any())
        <!-- Alerta de validación (ej. intentar poner un estado inválido) -->
    @endif

    <!-- ========================================== -->
    <!-- TABLA CENTRAL Y TIEMPO REAL                -->
    <!-- ========================================== -->
    <div class="bg-white p-3 rounded-3 shadow-sm mb-4">
        
        {{-- 🎯 NODO OBJETIVO JS: Esta clase 'table-responsive' es la que tickets.js extrae y reemplaza cada 30 segundos --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-nowrap">
                <thead class="table-light">
                    <!-- Cabeceras -->
                </thead>
                <tbody>
                    @if (isset($tickets) && count($tickets) > 0)
                        @foreach ($tickets as $ticket)
                            <tr>
                                <!-- Datos Básicos -->
                                <td class="fw-bold text-muted">{{ $ticket->id }}</td>
                                <td class="fw-bold">{{ $ticket->codigo_reserva }}</td>
                                
                                {{-- 🛡️ NULL COALESCING: Si el usuario que hizo la compra fue eliminado de la BD, no arroja error fatal --}}
                                <td>{{ $ticket->user->name ?? 'Usuario Desconocido' }}</td>
                                
                                <td>{{ $ticket->created_at->format('d/m/Y h:i A') }}</td>
                                <td class="fw-bold text-success">${{ number_format($ticket->total, 2) }}</td>
                                
                                <!-- Renderizado Dinámico de Badges -->
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
                                
                                <!-- ========================================== -->
                                <!-- ACCIONES (MODAL Y ACTUALIZACIÓN)           -->
                                <!-- ========================================== -->
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        
                                        {{-- BOTÓN DISPARADOR: Apunta a un ID único concatenando $ticket->id --}}
                                        <button class="btn btn-sm btn-outline-dark fw-bold" data-bs-toggle="modal"
                                            data-bs-target="#modalDetalle{{ $ticket->id }}">
                                            👁️ Ver
                                        </button>

                                        {{-- 🛡️ FORMULARIO SEGURO DE ESTADO: Evita mutaciones por GET. Usa POST y @csrf --}}
                                        <form action="{{ route('tickets.estado', $ticket->id) }}" method="POST"
                                            class="d-flex gap-2 mb-0">
                                            @csrf
                                            <select name="estado" class="form-select form-select-sm" style="width: 120px;">
                                                <option value="pendiente" {{ $ticket->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                                <option value="pagado" {{ $ticket->estado == 'pagado' ? 'selected' : '' }}>Pagado</option>
                                                <option value="entregado" {{ $ticket->estado == 'entregado' ? 'selected' : '' }}>Entregado</option>
                                                <option value="cancelado" {{ $ticket->estado == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-outline-primary fw-bold">Actualizar</button>
                                        </form>
                                    </div>

                                    <!-- ========================================== -->
                                    <!-- MODAL DINÁMICO (AISLADO EN EL DOM)         -->
                                    <!-- ========================================== -->
                                    {{-- El ID debe ser idéntico al data-bs-target del botón superior --}}
                                    <div class="modal fade" id="modalDetalle{{ $ticket->id }}" tabindex="-1"
                                        aria-hidden="true" style="white-space: normal;">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg">
                                                <!-- Header del Modal -->
                                                <div class="modal-header bg-dark text-white">
                                                    <h5 class="modal-title">🧾 Detalle de Ticket: {{ $ticket->codigo_reserva }}</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                
                                                <!-- Cuerpo del Modal (Tabla de Detalles) -->
                                                <div class="modal-body bg-light">
                                                    <!-- Info del Cliente y Fecha -->
                                                    
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered bg-white mb-0 text-nowrap">
                                                            <thead class="table-light">
                                                                <!-- ... cabeceras ... -->
                                                            </thead>
                                                            <tbody>
                                                                {{-- RECORRIDO DE RELACIONES: Accede a la relación 'detalles' definida en el Modelo Ticket --}}
                                                                @foreach ($ticket->detalles as $detalle)
                                                                    <tr>
                                                                        {{-- 🛡️ PROTECCIÓN DE PRODUCTO ELIMINADO --}}
                                                                        <td>{{ $detalle->producto->nombre ?? 'Producto Eliminado' }}</td>
                                                                        <td class="text-center fw-bold">{{ $detalle->cantidad }}</td>
                                                                        <td class="text-end text-muted">${{ number_format($detalle->precio_unitario, 2) }}</td>
                                                                        
                                                                        {{-- CÁLCULO AL VUELO: Multiplica cantidad por precio para el subtotal exacto histórico --}}
                                                                        <td class="text-end fw-bold">
                                                                            ${{ number_format($detalle->cantidad * $detalle->precio_unitario, 2) }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                            <tfoot>
                                                                <!-- Total Pagar -->
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
                        <!-- EMPTY STATE -->
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

    <!-- PAGINACIÓN -->
    <div class="mt-4">
        {{ $tickets->links() }}
    </div>
@endsection

<!-- ========================================== -->
<!-- SCRIPT DE POLLING (TIEMPO REAL)            -->
<!-- ========================================== -->
@push('scripts')
    @vite(['resources/js/tickets.js'])
@endpush