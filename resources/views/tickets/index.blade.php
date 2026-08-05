@extends('layouts.app')

<!-- Inyección a la plantilla maestra -->
@section('titulo_modulo', 'Gestión de Tickets y Pedidos')
@section('subtitulo_modulo', 'Administra el estado de las ventas y reservas de los clientes')

@section('content')

    <!-- ========================================== -->
    <!-- WIDGET DE CONTROL DE CAJA INTEGRADO        -->
    <!-- ========================================== -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">

            @if (session('success'))
                <div class="alert alert-success fw-bold mb-3">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger fw-bold mb-3">{{ $errors->first() }}</div>
            @endif

            @if (!$turnoAbierto)
                <!-- CASO A: NO HAY TURNO ABIERTO (Muestra formulario de apertura) -->
                <div class="col-md-7">
                    <form action="{{ route('caja.abrir') }}" method="POST"
                        class="d-flex justify-content-md-end align-items-center">
                        @csrf
                        <div class="alert alert-secondary py-2 mb-0 me-3">
                            <strong>Fondo Base Asignado:</strong> $20.00
                        </div>
                        <!-- Enviamos el valor fijo de forma oculta -->
                        <input type="hidden" name="monto_inicial" value="20.00">

                        <button type="submit" class="btn btn-success text-nowrap shadow-sm">
                            <i class="fas fa-box-open me-1"></i> Iniciar Turno
                        </button>
                    </form>
                </div>
            @else
                <!-- CASO B: TURNO ABIERTO (Muestra tarjetas estilo Dashboard y botón de cierre) -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="text-dark fw-bold mb-0"><i class="fas fa-cash-register text-success me-2"></i> Estado de
                            Caja Actual</h5>
                        <small class="text-muted">Turno abierto el:
                            {{ $turnoAbierto->fecha_apertura->format('d/m/Y h:i A') }}</small>
                    </div>
                    <form action="{{ route('caja.cerrar') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm"
                            onclick="return confirm('¿Estás seguro de cerrar tu turno de caja?')">
                            <i class="fas fa-lock me-1"></i> Cerrar Caja
                        </button>
                    </form>
                </div>

                <!-- Tarjetas de Métricas en Vivo (Estilo Dashboard) -->
                <div class="row row-cols-1 row-cols-md-5 g-3 text-start">

                    <!-- 1. Fondo Inicial -->
                    <div class="col">
                        <div class="card border-0 shadow-sm rounded-4 h-100"
                            style="border-left: 5px solid #6c757d !important;">
                            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small text-uppercase fw-bold">Fondo Inicial</span>
                                    <h4 class="text-dark mb-0 mt-1">${{ number_format($turnoAbierto->monto_inicial, 2) }}
                                    </h4>
                                </div>
                                <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="fas fa-wallet"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Ventas POS Efectivo -->
                    <div class="col">
                        <div class="card border-0 shadow-sm rounded-4 h-100"
                            style="border-left: 5px solid #0d6efd !important;">
                            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small text-uppercase fw-bold">POS (Efectivo)</span>
                                    <h4 class="text-dark mb-0 mt-1">+ ${{ number_format($turnoAbierto->total_efectivo, 2) }}
                                    </h4>
                                </div>
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="fas fa-cash-register"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Transferencias Web -->
                    <div class="col">
                        <div class="card border-0 shadow-sm rounded-4 h-100"
                            style="border-left: 5px solid #0dcaf0 !important;">
                            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small text-uppercase fw-bold">Web (Transferencias)</span>
                                    <h4 class="text-dark mb-0 mt-1">+
                                        ${{ number_format($turnoAbierto->total_transferencias, 2) }}</h4>
                                </div>
                                <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. TOTAL VENTAS DIARIAS (Nuevo) -->
                    <div class="col">
                        <div class="card border-0 shadow-sm rounded-4 h-100 bg-light"
                            style="border-left: 5px solid #ffc107 !important;">
                            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small text-uppercase fw-bold">Ventas del Turno</span>
                                    <h4 class="text-dark mb-0 mt-1 fw-bold">
                                        ${{ number_format($turnoAbierto->total_efectivo + $turnoAbierto->total_transferencias, 2) }}
                                    </h4>
                                </div>
                                <div class="bg-warning bg-opacity-25 text-warning rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 5. Total Físico Esperado en Gaveta -->
                    <div class="col">
                        <div class="card border-0 shadow-sm rounded-4 h-100"
                            style="border-left: 5px solid #198754 !important;">
                            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-success small text-uppercase fw-bold">Físico en Caja</span>
                                    <h4 class="text-success mb-0 mt-1 fw-bold">
                                        ${{ number_format($turnoAbierto->monto_inicial + $turnoAbierto->total_efectivo, 2) }}
                                    </h4>
                                </div>
                                <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            @endif

        </div>
    </div>
    <!-- ========================================== -->

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
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>🟡 Pendientes
                        </option>
                        <option value="pagado" {{ request('estado') == 'pagado' ? 'selected' : '' }}>🔵 Pagados</option>

                        <option value="listo" {{ request('estado') == 'listo' ? 'selected' : '' }}>🟣 Listos para retirar
                        </option>

                        <option value="entregado" {{ request('estado') == 'entregado' ? 'selected' : '' }}>🟢 Entregados
                        </option>
                        <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>🔴 Cancelados
                        </option>
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
                                            <select name="estado" class="form-select form-select-sm"
                                                style="width: 120px;">
                                                <option value="pendiente"
                                                    {{ $ticket->estado == 'pendiente' ? 'selected' : '' }}>Pendiente
                                                </option>
                                                <option value="pagado"
                                                    {{ $ticket->estado == 'pagado' ? 'selected' : '' }}>Pagado</option>
                                                <option value="listo" {{ $ticket->estado == 'listo' ? 'selected' : '' }}>
                                                    Listo para retirar</option>
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
                                                    <h5 class="modal-title">🧾 Detalle de Ticket:
                                                        {{ $ticket->codigo_reserva }}</h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"></button>
                                                </div>

                                                <!-- Cuerpo del Modal (Tabla de Detalles) -->
                                                <div class="modal-body bg-light">
                                                    <!-- Info del Cliente y Fecha -->

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
                                                                {{-- RECORRIDO DE RELACIONES: Accede a la relación 'detalles' --}}
                                                                @foreach ($ticket->detalles as $detalle)
                                                                    <tr>
                                                                        {{-- 🛡️ PROTECCIÓN DE PRODUCTO ELIMINADO --}}
                                                                        <td class="align-middle">
                                                                            {{ $detalle->producto->nombre ?? 'Producto Eliminado' }}
                                                                        </td>
                                                                        <td class="text-center fw-bold align-middle">
                                                                            {{ $detalle->cantidad }}
                                                                        </td>
                                                                        <td class="text-end text-muted align-middle">
                                                                            ${{ number_format($detalle->precio_unitario, 2) }}
                                                                        </td>
                                                                        {{-- CÁLCULO AL VUELO: Multiplica cantidad por precio --}}
                                                                        <td class="text-end fw-bold align-middle">
                                                                            ${{ number_format($detalle->cantidad * $detalle->precio_unitario, 2) }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                            <tfoot class="table-group-divider">
                                                                <!-- Fila del Total a Pagar -->
                                                                <tr>
                                                                    <td colspan="3"
                                                                        class="text-end fw-bold text-uppercase align-middle">
                                                                        Total a Cobrar:
                                                                    </td>
                                                                    <td class="text-end fw-bolder text-success fs-5">
                                                                        ${{ number_format($ticket->total, 2) }}
                                                                    </td>
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
