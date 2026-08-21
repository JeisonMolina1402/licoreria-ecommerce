@extends('layouts.app')

@section('titulo_modulo', 'Panel de Rendimiento')
@section('subtitulo_modulo', 'Resumen financiero y operativo de tu tienda')

@section('content')
    <div class="container-fluid bg-light p-4" style="min-height: 100vh;">

        <div class="d-flex justify-content-between align-items-end mb-3">
            <h5 class="text-dark mb-0 d-none d-md-block">Datos de Rendimiento</h5>
            <button type="button" id="btnExportarPdf" class="btn btn-danger btn-sm fw-bold shadow-sm px-4" onclick="exportarConGraficos()">
                <i class="fa-solid fa-file-pdf me-1"></i> Descargar PDF
            </button>
        </div>

        <form id="formExportarPdf" action="{{ route('reportes.pdf') }}" method="POST" class="d-none">
            @csrf
            <input type="hidden" name="fecha_inicio" value="{{ $fechaInicio }}">
            <input type="hidden" name="fecha_fin" value="{{ $fechaFin }}">
            <input type="hidden" name="ranking_productos" value="ventas">
            <input type="hidden" name="ranking_categorias" value="ventas">
            <input type="hidden" name="modo_agrupacion" value="{{ $modoAgrupacion }}"> 
            <input type="hidden" name="grafico_barras_base64" id="grafico_barras_base64">
            <input type="hidden" name="grafico_dona_base64" id="grafico_dona_base64">
            <input type="hidden" name="grafico_vendedores_base64" id="grafico_vendedores_base64">
            <input type="hidden" name="grafico_usuarios_base64" id="grafico_usuarios_base64">
        </form>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <form action="{{ route('reportes.index') }}" method="GET" id="formFiltroPrincipal" class="form-cargando">
                    <input type="hidden" name="modo_agrupacion" id="input_modo_agrupacion" value="{{ $modoAgrupacion }}">
                    <div class="row align-items-end g-3">
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold text-uppercase"><i class="fa-regular fa-calendar me-1"></i> Desde</label>
                            <input type="date" name="fecha_inicio" id="input_fecha_inicio" class="form-control form-control-lg bg-light" value="{{ $fechaInicio }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold text-uppercase"><i class="fa-regular fa-calendar-check me-1"></i> Hasta</label>
                            <input type="date" name="fecha_fin" id="input_fecha_fin" class="form-control form-control-lg bg-light" value="{{ $fechaFin }}">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm" onclick="document.getElementById('input_modo_agrupacion').value=''">
                                <i class="fa-solid fa-filter me-2"></i> Filtrar Datos
                            </button>
                        </div>
                    </div>

                    <div class="mt-3 pt-3 border-top d-flex flex-wrap gap-2 align-items-center">
                        <span class="text-muted small fw-bold me-2"><i class="fa-solid fa-bolt text-warning me-1"></i> Atajos:</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold" onclick="aplicarFiltroRapido('mes')">Mes</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold" onclick="aplicarFiltroRapido('trimestre')">Trimestre</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold" onclick="aplicarFiltroRapido('semestre')">Semestre</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold" onclick="aplicarFiltroRapido('anual')">Anual (3 años)</button>

                        <div class="vr mx-1"></div> 
                        
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
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
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
                                <div class="text-uppercase text-muted small fw-bold mb-1">Costos (Gastos de Inventario)</div>
                                <div class="h3 mb-0 fw-bold text-dark">${{ number_format($costosTotales, 2) }}</div>
                            </div>
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
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
                            <div class="bg-success bg-opacity-10 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
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
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fa-solid fa-receipt fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 d-none d-xl-block">
                <div class="card border-0 shadow-sm h-100 rounded-4" style="border-left: 5px solid #6f42c1 !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-uppercase text-muted small fw-bold mb-1">Nuevos Clientes</div>
                                <div class="h3 mb-0 fw-bold text-dark">{{ $nuevosUsuarios }}</div>
                            </div>
                            <div class="bg-purple bg-opacity-10 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fa-solid fa-users fa-2x" style="color: #6f42c1;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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

        <div class="row mb-4">
            @livewire('rendimiento-productos', ['fechaInicio' => $fechaInicio, 'fechaFin' => $fechaFin])
        </div>

        <div class="row g-4 mb-4">
            
            @livewire('rendimiento-categorias', ['fechaInicio' => $fechaInicio, 'fechaFin' => $fechaFin, 'tieneTickets' => $totalTickets > 0])

            <div class="col-lg-5 d-flex flex-column gap-4">

                <div class="card shadow-sm border-0 rounded-4 flex-grow-1">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold text-dark"><i class="fa-solid fa-medal text-warning me-2"></i> Ranking de Vendedores</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4" style="position: relative; height: 200px;">
                            @if(count($rendimientoVendedores) > 0)
                                <canvas id="graficoVendedores" data-nombres="{{ $nombresVendedores }}" data-ventas="{{ $ventasVendedores }}"></canvas>
                            @else
                                <div class="d-flex flex-column justify-content-center align-items-center h-100 text-muted">
                                    <i class="fa-solid fa-chart-bar fs-1 mb-2"></i>
                                    <p class="small">No hay ventas registradas.</p>
                                </div>
                            @endif
                        </div>
                        <div class="table-responsive border rounded-3">
                            <table class="table table-hover align-middle text-center mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-2 text-uppercase text-muted small text-start ps-3">#</th>
                                        <th class="py-2 text-uppercase text-muted small text-start">Vendedor</th>
                                        <th class="py-2 text-uppercase text-muted small">Tickets</th>
                                        <th class="py-2 text-uppercase text-muted small text-success fw-bold">Recaudado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rendimientoVendedores as $index => $vendedor)
                                        <tr>
                                            <td class="py-2 text-start ps-3 fw-bold">
                                                @if($index === 0) 🥇 
                                                @elseif($index === 1) 🥈 
                                                @elseif($index === 2) 🥉 
                                                @else {{ $index + 1 }} 
                                                @endif
                                            </td>
                                            <td class="py-2 text-start fw-bold small">{{ $vendedor->vendedor ? $vendedor->vendedor->name : 'Eliminado' }}</td>
                                            <td class="py-2"><span class="badge bg-secondary rounded-pill">{{ $vendedor->total_tickets }}</span></td>
                                            <td class="py-2 text-success fw-bold small">${{ number_format($vendedor->total_recaudado, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="py-4 text-center text-muted small">Aún no hay datos de comisiones.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4 d-flex flex-column justify-content-center">
                        <h6 class="fw-bold text-dark mb-4 border-bottom pb-2">Estado de Reservas</h6>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-end mb-1">
                                <div>
                                    <span class="fw-bold text-success d-block">Entregados (Completados)</span>
                                    <small class="text-muted"><i class="fa-solid fa-check-circle text-success me-1"></i>{{ $ticketsEntregados }} tickets en total</small>
                                </div>
                                <span class="fw-bold fs-5">{{ $totalTickets > 0 ? round(($ticketsEntregados / $totalTickets) * 100) : 0 }}%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" style="width: {{ $totalTickets > 0 ? ($ticketsEntregados / $totalTickets) * 100 : 0 }}%;"></div>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between align-items-end mb-1">
                                <div>
                                    <span class="fw-bold text-danger d-block">Cancelados / Vencidos</span>
                                    <small class="text-muted"><i class="fa-solid fa-xmark-circle text-danger me-1"></i>{{ $ticketsCancelados }} tickets en total</small>
                                </div>
                                <span class="fw-bold fs-5">{{ $totalTickets > 0 ? round(($ticketsCancelados / $totalTickets) * 100) : 0 }}%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-danger" style="width: {{ $totalTickets > 0 ? ($ticketsCancelados / $totalTickets) * 100 : 0 }}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold text-dark"><i class="fa-solid fa-user-trend-up" style="color: #6f42c1; margin-right: 0.5rem;"></i> Crecimiento de Clientes</h6>
                    </div>
                    <div class="card-body p-4">
                        <div style="position: relative; height: 180px;">
                            <canvas id="graficoUsuarios" data-etiquetas="{{ $nombresBarras }}" data-usuarios="{{ $datosUsuariosBarras }}"></canvas>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    @vite(['resources/js/reportes.js'])
    
    <script>
        function exportarConGraficos() {
            const btn = document.getElementById('btnExportarPdf');
            
            // Mostrar Spinner visual
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Generando PDF...';
            btn.classList.add('disabled');
            
            // Llama a la lógica de JS (en reportes.js)
            if (typeof window.procesarGraficosParaPdf === 'function') {
                window.procesarGraficosParaPdf();
            }

            setTimeout(() => {
                btn.innerHTML = '<i class="fa-solid fa-file-pdf me-1"></i> Exportar Reporte';
                btn.classList.remove('disabled');
            }, 3500);
        }
    </script>
@endpush