@extends('layouts.app')

<!-- Enviamos el título, subtítulo y el botón rojo a la plantilla maestra -->
@section('titulo_modulo', 'Panel de Rendimiento')
@section('subtitulo_modulo', 'Resumen financiero y operativo de tu tienda')

@section('content')
    <div class="container-fluid bg-light p-4" style="min-height: 100vh;">

        <!--exportar pdf       -->

        <div class="d-flex justify-content-between align-items-end mb-3">
            <h5 class="text-dark mb-0 d-none d-md-block">Datos de Rendimiento</h5>
            <button type="button" class="btn btn-danger btn-sm fw-bold shadow-sm px-3" onclick="exportarConGraficos()">
                <i class="fa-solid fa-file-pdf me-1"></i> Exportar Reporte
            </button>
        </div>

        <!-- FORMULARIO OCULTO PARA EL PDF  -->

        <form id="formExportarPdf" action="{{ route('reportes.pdf') }}" method="POST" class="d-none">
            @csrf
            <input type="hidden" name="fecha_inicio" value="{{ request('fecha_inicio', $fechaInicio) }}">
            <input type="hidden" name="fecha_fin" value="{{ request('fecha_fin', $fechaFin) }}">

            <input type="hidden" name="ranking_productos" value="{{ $rankingProductos }}">
            <input type="hidden" name="ranking_categorias" value="{{ $rankingCategorias }}">
            <input type="hidden" name="modo_agrupacion" value="{{ $modoAgrupacion }}"> <!-- NUEVO -->

            <input type="hidden" name="grafico_barras_base64" id="grafico_barras_base64">
            <input type="hidden" name="grafico_dona_base64" id="grafico_dona_base64">
        </form>


        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <!-- Agregamos el ID formFiltroPrincipal -->
                <form action="{{ route('reportes.index') }}" method="GET" id="formFiltroPrincipal">
                    <!-- Input oculto para la agrupación rápida -->
                    <input type="hidden" name="modo_agrupacion" id="input_modo_agrupacion" value="{{ $modoAgrupacion }}">
                    <div class="row align-items-end g-3">
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold text-uppercase"><i class="fa-regular fa-calendar me-1"></i> Desde</label>
                            <!-- Agregamos el ID input_fecha_inicio -->
                            <input type="date" name="fecha_inicio" id="input_fecha_inicio" class="form-control form-control-lg bg-light" value="{{ $fechaInicio }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold text-uppercase"><i class="fa-regular fa-calendar-check me-1"></i> Hasta</label>
                            <!-- Agregamos el ID input_fecha_fin -->
                            <input type="date" name="fecha_fin" id="input_fecha_fin" class="form-control form-control-lg bg-light" value="{{ $fechaFin }}">
                        </div>
                        <div class="col-md-4">
                            <!-- Al hacer clic manualmente, borramos el modo de agrupación para que actúe por días -->
                            <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm" onclick="document.getElementById('input_modo_agrupacion').value=''">
                                <i class="fa-solid fa-filter me-2"></i> Filtrar Datos
                            </button>
                        </div>
                    </div>

                    <!-- BOTONES DE ACCIÓN RÁPIDA -->
                    <div class="mt-3 pt-3 border-top d-flex flex-wrap gap-2 align-items-center">
                        <span class="text-muted small fw-bold me-2"><i class="fa-solid fa-bolt text-warning me-1"></i> Atajos:</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold" onclick="aplicarFiltroRapido('mes')">Mes</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold" onclick="aplicarFiltroRapido('trimestre')">Trimestre</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold" onclick="aplicarFiltroRapido('semestre')">Semestre</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold" onclick="aplicarFiltroRapido('anual')">Anual (3 años)</button>

                        <div class="vr mx-1"></div> <!-- Línea divisoria vertical -->
                        
                        <button type="button" class="btn btn-sm btn-light border text-danger rounded-pill px-3 fw-bold" onclick="aplicarFiltroRapido('limpiar')">
                            <i class="fa-solid fa-eraser me-1"></i> Limpiar Filtro
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
                <div class="card border-0 shadow-sm h-100 rounded-4" style="border-left: 5px solid red !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-uppercase text-muted small fw-bold mb-1">Costos (Gastos de Inventario)
                                </div>
                                <div class="h3 mb-0 fw-bold text-dark">${{ number_format($costosTotales, 2) }}</div>
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

        <!-- Gráfico de Barras Dinámico (Diario o Mensual) -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold text-dark"><i class="fa-solid fa-chart-column text-primary me-2"></i>
                            {{ $tituloGraficoBarras }}</h6>
                        <span class="badge bg-light text-muted border">Ventas Brutas vs Ganancia Neta</span>
                    </div>
                    <div class="card-body" style="position: relative; height: 350px;">
                        <canvas id="graficoBarras" data-etiquetas="{{ $nombresBarras }}"
                            data-ventas="{{ $datosVentasBarras }}" data-ganancias="{{ $datosGananciasBarras }}"
                            data-gastos="{{ $datosGastosBarras }}">
                        </canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fila 2: TABLA DE RENDIMIENTO (ANCHO COMPLETO) -->
        <div class="row mb-4">
            <div class="col-12" id="tabla-ranking">
                <div class="card shadow-sm border-0 h-100 rounded-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="m-0 fw-bold text-primary">
                            <i class="fa-solid fa-trophy text-warning me-2"></i> Rendimiento de Productos
                        </h6>

                        <div class="d-flex align-items-center gap-3">
                            <form action="{{ route('reportes.index') }}#tabla-ranking" method="GET" class="m-0">
                                <input type="hidden" name="fecha_inicio"
                                    value="{{ request('fecha_inicio', $fechaInicio) }}">
                                <input type="hidden" name="fecha_fin" value="{{ request('fecha_fin', $fechaFin) }}">
                                <input type="hidden" name="ranking_categorias" value="{{ $rankingCategorias }}">
                                <input type="hidden" name="modo_agrupacion" value="{{ $modoAgrupacion }}"> <!-- NUEVO -->

                                <select name="ranking_productos"
                                    class="form-select form-select-sm border-primary fw-bold text-primary"
                                    onchange="this.form.submit()" style="cursor: pointer;">
                                    <option value="ventas" {{ $rankingProductos == 'ventas' ? 'selected' : '' }}>🥇 Más
                                        Vendidos (Unidades)</option>
                                    <option value="ganancia" {{ $rankingProductos == 'ganancia' ? 'selected' : '' }}>💰
                                        Mayor Ganancia (Dinero)</option>
                                    <option value="cero" {{ $rankingProductos == 'cero' ? 'selected' : '' }}>📉 Sin
                                        Movimiento (0 Ventas)</option>
                                </select>
                            </form>
                            <span class="badge bg-light text-dark border">Página {{ $productosTop->currentPage() }} de
                                {{ $productosTop->lastPage() }}</span>
                        </div>
                    </div>

                    <div class="card-body p-0 d-flex flex-column">
                        <div class="table-responsive flex-grow-1">
                            <table class="table table-hover align-middle text-center mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4 py-3 text-uppercase text-muted small text-start">Posición</th>
                                        <th class="py-3 text-uppercase text-muted small text-start">Producto</th>
                                        <th class="py-3 text-uppercase text-muted small">Categoría</th>
                                        <th class="py-3 text-uppercase text-muted small">P. Compra</th>
                                        <th class="py-3 text-uppercase text-muted small">P. Venta Actual</th>
                                        <th class="py-3 text-uppercase text-muted small border-start">Total Inversión</th>
                                        <th class="py-3 text-uppercase text-muted small">Total Venta</th>
                                        <th class="py-3 text-uppercase text-muted small text-success fw-bold">Ganancia
                                            Total</th>
                                        <th class="py-3 text-uppercase text-muted small border-start">Unidades</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($productosTop as $index => $producto)
                                        @php
                                            // Cálculos basados en el historial real de la base de datos
                                            $unidades = $producto->total_vendido ?? 0;
                                            $totalVenta = $producto->ingreso_generado ?? 0; // Usamos el ingreso real
                                            $ganancia = $producto->ganancia_generada ?? 0;  // Usamos la ganancia real
                                            $totalInversion = $totalVenta - $ganancia;      // Inversión calculada por diferencia matemática
                                        @endphp
                                        <tr>
                                            <td class="px-4 py-3 fw-bold text-muted text-start">
                                                #{{ ($productosTop->currentPage() - 1) * $productosTop->perPage() + $loop->iteration }}
                                            </td>
                                            <td class="py-3 text-start">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-white border rounded p-1 me-3 d-flex justify-content-center align-items-center shadow-sm"
                                                        style="width: 45px; height: 45px;">
                                                        @if ($producto->imagen)
                                                            <img src="{{ asset($producto->imagen) }}" alt="img"
                                                                class="rounded"
                                                                style="max-width: 100%; max-height: 100%; object-fit: cover;">
                                                        @else
                                                            <span class="fs-5">🍷</span>
                                                        @endif
                                                    </div>
                                                    <strong>{{ $producto->nombre }}</strong>
                                                </div>
                                            </td>
                                            <td class="py-3">{{ $producto->categoria->nombre ?? 'Sin Categoría' }}</td>
                                            <td class="py-3 text-muted">${{ number_format($producto->precio_compra, 2) }}
                                            </td>
                                            <td class="py-3">${{ number_format($producto->precio, 2) }}</td>

                                            <td class="py-3 border-start text-danger">
                                                ${{ number_format($totalInversion, 2) }}</td>
                                            <td class="py-3 text-primary fw-bold">${{ number_format($totalVenta, 2) }}
                                            </td>
                                            <td class="py-3 text-success fw-bold">${{ number_format($ganancia, 2) }}</td>

                                            <td class="py-3 border-start">
                                                @if ($unidades > 0)
                                                    <span class="badge bg-success rounded-pill px-3 py-2"
                                                        style="font-size: 0.9rem;">{{ $unidades }} unid.</span>
                                                @else
                                                    <span class="badge bg-danger rounded-pill px-3 py-2"
                                                        style="font-size: 0.9rem;">0 unid.</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-5 text-muted">
                                                <i class="fa-solid fa-box-open fs-2 mb-2 d-block"></i>
                                                No hay productos en este rango.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="border-top p-3 bg-light rounded-bottom-4">
                            {{ $productosTop->fragment('tabla-ranking')->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fila 3: GRÁFICO DE DONA Y ESTADOS DE RESERVA ABAJO -->
        <div class="row g-4">
            <div class="col-lg-7" id="tabla-categorias">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="m-0 fw-bold text-dark"><i class="fa-solid fa-chart-pie text-success me-2"></i>
                            Rendimiento por Categoría</h6>

                        <form action="{{ route('reportes.index') }}#tabla-categorias" method="GET" class="m-0">
                            <input type="hidden" name="fecha_inicio"
                                value="{{ request('fecha_inicio', $fechaInicio) }}">
                            <input type="hidden" name="fecha_fin" value="{{ request('fecha_fin', $fechaFin) }}">
                            <input type="hidden" name="ranking_productos" value="{{ $rankingProductos }}">
                            <input type="hidden" name="modo_agrupacion" value="{{ $modoAgrupacion }}"> <!-- NUEVO -->

                            <select name="ranking_categorias"
                                class="form-select form-select-sm border-success fw-bold text-success"
                                onchange="this.form.submit()" style="cursor: pointer;">
                                <option value="ventas" {{ $rankingCategorias == 'ventas' ? 'selected' : '' }}>🥇 Más
                                    Vendidos (Unidades)</option>
                                <option value="ganancia" {{ $rankingCategorias == 'ganancia' ? 'selected' : '' }}>💰 Mayor
                                    Ganancia (Dinero)</option>
                                <option value="cero" {{ $rankingCategorias == 'cero' ? 'selected' : '' }}>📉 Sin
                                    Movimiento (0 Ventas)</option>
                            </select>
                        </form>
                    </div>

                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex justify-content-center align-items-center mb-4"
                            style="position: relative; min-height: 250px;">
                            @if ($totalTickets > 0)
                                <canvas id="graficoCategorias" data-nombres="{{ $nombresCategorias }}"
                                    data-cantidades="{{ $cantidadesCategorias }}"></canvas>
                            @else
                                <div class="text-center text-muted">
                                    <i class="fa-solid fa-chart-simple fs-1 mb-2"></i>
                                    <p>No hay datos suficientes.</p>
                                </div>
                            @endif
                        </div>

                        <div class="table-responsive border rounded-3 mt-2 flex-grow-1">
                            <table class="table table-hover align-middle text-center mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-2 text-uppercase text-muted small text-start ps-3">Categoría</th>
                                        <th class="py-2 text-uppercase text-muted small">Inversión</th>
                                        <th class="py-2 text-uppercase text-muted small">Ventas</th>
                                        <th class="py-2 text-uppercase text-muted small text-success fw-bold">Ganancia</th>
                                        <th class="py-2 text-uppercase text-muted small">Unidades</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ventasPorCategoria as $categoria => $datos)
                                        <tr>
                                            <td class="py-2 text-start ps-3 fw-bold">{{ $categoria }}</td>
                                            <td class="py-2 text-danger">${{ number_format($datos['inversion'], 2) }}</td>
                                            <td class="py-2 text-primary fw-bold">
                                                ${{ number_format($datos['ventas'], 2) }}</td>
                                            <td class="py-2 text-success fw-bold">
                                                ${{ number_format($datos['ganancia'], 2) }}</td>
                                            <td class="py-2">
                                                <span
                                                    class="badge @if ($datos['unidades'] == 0) bg-danger @else bg-success @endif rounded-pill px-2">
                                                    {{ $datos['unidades'] }} unid.
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-4 text-center text-muted">No hay datos en este
                                                rango.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumen de Efectividad -->
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-center">
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

    @endsection

    <!-- Gráfico de Ventas por Categoría -->
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <!--Plugin para imprimir etiquetas dentro de los gráficos -->
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

        @vite(['resources/js/reportes.js'])
    @endpush