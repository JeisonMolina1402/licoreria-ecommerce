@extends('layouts.app')

@section('content')
    <div class="container-fluid p-4 bg-light" style="min-height: 100vh;">

        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <div>
                <h2 class="h3 text-dark mb-0 fw-bold">📊 Panel de Rendimiento</h2>
                <small class="text-muted">Resumen financiero y operativo de tu tienda</small>
            </div>
            <<form id="formExportarPdf" action="{{ route('reportes.pdf') }}" method="POST" target="_blank" class="m-0">
                @csrf
                <input type="hidden" name="fecha_inicio" value="{{ $fechaInicio }}">
                <input type="hidden" name="fecha_fin" value="{{ $fechaFin }}">
                
                <input type="hidden" name="grafico_barras_base64" id="inputGraficoBarras">
                <input type="hidden" name="grafico_dona_base64" id="inputGraficoDona">
                
                <button type="button" onclick="exportarConGraficos()" class="btn btn-danger shadow-sm">
                    <i class="fa-solid fa-file-pdf me-2"></i> Exportar Reporte
                </button>
            </form>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <form action="{{ route('reportes.index') }}" method="GET" class="row align-items-end g-3">
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold text-uppercase"><i
                                class="fa-regular fa-calendar me-1"></i> Desde</label>
                        <input type="date" name="fecha_inicio" class="form-control form-control-lg bg-light"
                            value="{{ $fechaInicio }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold text-uppercase"><i
                                class="fa-regular fa-calendar-check me-1"></i> Hasta</label>
                        <input type="date" name="fecha_fin" class="form-control form-control-lg bg-light"
                            value="{{ $fechaFin }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm">
                            <i class="fa-solid fa-filter me-2"></i> Filtrar Datos
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-4 mb-4">

            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm h-100 rounded-4" style="border-left: 5px solid #0d6efd !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-uppercase text-muted small fw-bold mb-1">Ingresos (Ventas)</div>
                                <div class="h3 mb-0 fw-bold text-dark">${{ number_format($ventasTotales, 2) }}</div>
                            </div>
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-flex align-items-center justify-content-center"
                                style="width: 60px; height: 60px;">
                                <i class="fa-solid fa-cash-register fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm h-100 rounded-4" style="border-left: 5px solid #198754 !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-uppercase text-muted small fw-bold mb-1">Ganancia Neta</div>
                                <div class="h3 mb-0 fw-bold text-success">${{ number_format($gananciaNeta, 2) }}</div>
                            </div>
                            <div class="bg-success bg-opacity-10 rounded-circle p-3 d-flex align-items-center justify-content-center"
                                style="width: 60px; height: 60px;">
                                <i class="fa-solid fa-sack-dollar fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm h-100 rounded-4" style="border-left: 5px solid #ffc107 !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-uppercase text-muted small fw-bold mb-1">Tickets Generados</div>
                                <div class="h3 mb-0 fw-bold text-dark">{{ $totalTickets }}</div>
                                <small class="text-success fw-bold">{{ $ticketsEntregados }} Entregados</small>
                            </div>
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3 d-flex align-items-center justify-content-center"
                                style="width: 60px; height: 60px;">
                                <i class="fa-solid fa-receipt fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm h-100 rounded-4" style="border-left: 5px solid #6f42c1 !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-uppercase text-muted small fw-bold mb-1">Nuevos Clientes</div>
                                <div class="h3 mb-0 fw-bold text-dark">{{ $nuevosUsuarios }}</div>
                            </div>
                            <div class="bg-purple bg-opacity-10 rounded-circle p-3 d-flex align-items-center justify-content-center"
                                style="width: 60px; height: 60px;">
                                <i class="fa-solid fa-users fa-2x" style="color: #6f42c1;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fila 2: Tablas y Gráficos -->
        <div class="row g-4">

            <!-- Lista de Productos Paginada -->
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 h-100 rounded-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="m-0 fw-bold text-primary"><i class="fa-solid fa-trophy text-warning me-2"></i> Ranking de
                            Productos Vendidos</h6>
                        <span class="badge bg-light text-dark border">Página {{ $productosTop->currentPage() }} de
                            {{ $productosTop->lastPage() }}</span>
                    </div>
                    <div class="card-body p-0 d-flex flex-column">
                        <div class="table-responsive flex-grow-1">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4 py-3 text-uppercase text-muted small">Posición</th>
                                        <th class="py-3 text-uppercase text-muted small">Producto</th>
                                        <th class="py-3 text-uppercase text-muted small text-center">Unidades</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($productosTop as $index => $producto)
                                        <tr>
                                            <td class="px-4 py-3 fw-bold text-muted">
                                                #{{ ($productosTop->currentPage() - 1) * $productosTop->perPage() + $loop->iteration }}
                                            </td>
                                            <td class="py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-light rounded p-2 me-3 text-center" style="width: 40px;">
                                                        🍷</div>
                                                    <strong>{{ $producto->nombre }}</strong>
                                                </div>
                                            </td>
                                            <td class="py-3 text-center">
                                                @if (($producto->total_vendido ?? 0) > 0)
                                                    <span class="badge bg-success rounded-pill px-3 py-2"
                                                        style="font-size: 0.9rem;">
                                                        {{ $producto->total_vendido }} unid.
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger rounded-pill px-3 py-2"
                                                        style="font-size: 0.9rem;">
                                                        0 unid.
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-5 text-muted">
                                                <i class="fa-solid fa-box-open fs-2 mb-2 d-block"></i>
                                                No hay productos en el inventario.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <!-- Botones de Paginación -->
                        <div class="border-top p-3 bg-light rounded-bottom-4">
                            {{ $productosTop->links() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Gráfico y Estado -->
            <div class="col-lg-5 d-flex flex-column gap-4">

                <!-- Gráfico de Dona: Ventas por Categoría -->
                <div class="card shadow-sm border-0 rounded-4 flex-grow-1">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="m-0 fw-bold text-dark"><i class="fa-solid fa-chart-pie text-success me-2"></i> Ventas
                            por Categoría</h6>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center"
                        style="position: relative; min-height: 350px;">
                        @if ($totalTickets > 0)
                            <canvas id="graficoCategorias" data-nombres="{{ $nombresCategorias }}"
                                data-cantidades="{{ $cantidadesCategorias }}">
                            </canvas>
                        @else
                            <div class="text-center text-muted">
                                <i class="fa-solid fa-chart-simple fs-1 mb-2"></i>
                                <p>No hay datos suficientes.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Resumen de Efectividad -->
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark mb-4 border-bottom pb-2">Estado de Reservas</h6>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-bold text-success">Entregados (Completados)</span>
                                <span
                                    class="fw-bold">{{ $totalTickets > 0 ? round(($ticketsEntregados / $totalTickets) * 100) : 0 }}%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success"
                                    style="width: {{ $totalTickets > 0 ? ($ticketsEntregados / $totalTickets) * 100 : 0 }}%;">
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-bold text-danger">Cancelados / Vencidos</span>
                                <span
                                    class="fw-bold">{{ $totalTickets > 0 ? round(($ticketsCancelados / $totalTickets) * 100) : 0 }}%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-danger"
                                    style="width: {{ $totalTickets > 0 ? ($ticketsCancelados / $totalTickets) * 100 : 0 }}%;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Gráfico de Barras Dinámico (Diario o Mensual) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-dark"><i class="fa-solid fa-chart-column text-primary me-2"></i> {{ $tituloGraficoBarras }}</h6>
                    <span class="badge bg-light text-muted border">Ventas Brutas vs Ganancia Neta</span>
                </div>
                <div class="card-body" style="position: relative; height: 350px;">
                    <canvas id="graficoBarras"
                        data-etiquetas="{{ $nombresBarras }}"
                        data-ventas="{{ $datosVentasBarras }}"
                        data-ganancias="{{ $datosGananciasBarras }}"
                        data-gastos="{{ $datosGastosBarras }}">
                    </canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

<!-- Gráfico de Ventas por Categoría -->
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/js/reportes.js'])
@endpush
