@extends('layouts.app')

<!-- Inyección a la plantilla maestra -->
@section('titulo_modulo', 'Analítica General')
@section('subtitulo_modulo', 'Métricas en tiempo real y resumen operativo de la tienda')

@section('acciones_modulo')
    {{-- Si a futuro necesitas un botón aquí (como 'Actualizar Datos'), lo colocas en esta sección --}}
@endsection

@section('content')
    
    {{-- ========================================== --}}
    {{-- BLOQUE 1: TARJETAS DE INDICADORES (KPIs)   --}}
    {{-- ========================================== --}}
    <div class="row g-3 mb-5 text-center">
        
        {{-- KPI 1: VENTAS DIARIAS --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 py-3">
                <div class="card-body">
                    <div class="text-muted small mb-1">Ventas Diarias</div>
                    {{-- number_format: Da formato monetario estándar con 2 decimales --}}
                    <h3 class="fw-bold mb-0 text-success">${{ number_format($ventasDiarias, 2) }}</h3>
                </div>
            </div>
        </div>

        {{-- KPI 2: TICKETS PENDIENTES --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 py-3">
                <div class="card-body">
                    <div class="text-muted small mb-1">Tickets Pendientes</div>
                    <h3 class="fw-bold mb-0 text-warning">{{ $ticketsPendientes }}</h3>
                </div>
            </div>
        </div>

        {{-- KPI 3: STOCK CRÍTICOS (ALERTA ROJA) --}}
        <div class="col-md-3">
            {{-- Fondo de alerta aplicado directamente a la tarjeta para destacar urgencia --}}
            <div class="card shadow-sm border-0 h-100 py-3 bg-danger text-white">
                <div class="card-body">
                    <div class="small mb-1"> Productos Stock Bajo</div>
                    <h3 class="fw-bold mb-0">{{ $lowStock }}</h3>
                </div>
            </div>
        </div>

        {{-- KPI 4: TOTAL DE PRODUCTOS REGISTRADOS --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 py-3">
                <div class="card-body">
                    <div class="text-muted small mb-1">Productos en Inventario</div>
                    <h3 class="fw-bold mb-0">{{ $totalProductos }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- BLOQUE 2: TABLA DE ÚLTIMOS INGRESOS        --}}
    {{-- ========================================== --}}
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h5 class="text-dark mb-0">Últimos Productos Agregados</h5>
        {{-- Enlace directo hacia la gestión global del inventario --}}
        <a href="{{ route('inventario') }}" class="btn btn-outline-secondary btn-sm">Ver todo el Inventario →</a>
    </div>

    <div class="bg-white p-3 rounded-3 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-nowrap">
                <thead class="table-light">
                    <tr>
                        <th>Imagen</th>
                        <th>Nombre Producto</th>
                        <th>Descripción</th>
                        <th>Categoría</th>
                        <th>Precio Venta</th>
                        <th>Stock Actual</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- PROGRAMACIÓN DEFENSIVA: Verifica que la colección exista y contenga registros antes de iterar --}}
                    @if (isset($ultimosProductos) && count($ultimosProductos) > 0)
                        @foreach ($ultimosProductos as $producto)
                            <tr>
                                <td>
                                    {{-- FALLBACK DE IMAGEN: Si el producto tiene foto la muestra; si no, imprime un emoji de botella --}}
                                    @if ($producto->imagen)
                                        <img src="{{ asset($producto->imagen) }}" alt="img" style="width: 40px; height: 40px; object-fit: contain;">
                                    @else
                                        <span style="font-size: 1.5rem;">🍾</span>
                                    @endif
                                </td>
                                <td class="fw-bold">{{ $producto->nombre }}</td>
                                
                                {{-- Operador Null Coalescing: Muestra 'N/A' si la descripción está vacía --}}
                                <td class="text-muted">{{ $producto->descripcion ?? 'N/A' }}</td>
                                
                                {{-- 🛡️ PROTECCIÓN RELACIONAL: Previene errores si la categoría fue eliminada --}}
                                <td>{{ $producto->categoria->nombre ?? 'Sin Categoría' }}</td>
                                
                                <td class="fw-bold text-success">${{ number_format($producto->precio, 2) }}</td>
                                
                                <td>
                                    {{-- LÓGICA DE ALERTA DE STOCK: Si es menor o igual a 10 unidades, pinta el badge de rojo (danger). De lo contrario, verde (success) --}}
                                    <span class="badge {{ $producto->stock <= 10 ? 'bg-danger' : 'bg-success' }}">
                                        {{ $producto->stock }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        {{-- ESTADO VACÍO (EMPTY STATE): Evita una tabla rota si la base de datos está limpia --}}
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No hay productos registrados.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection