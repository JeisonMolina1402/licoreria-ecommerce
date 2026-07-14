@extends('layouts.app')

@section('titulo_modulo', 'Analítica General')

@section('content')
    <div class="row g-3 mb-5 text-center">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 py-3">
                <div class="card-body">
                    <div class="text-muted small mb-1">Ventas Diarias</div>
                    <h3 class="fw-bold mb-0 text-success">${{ number_format($ventasDiarias, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 py-3">
                <div class="card-body">
                    <div class="text-muted small mb-1">Tickets Pendientes</div>
                    <h3 class="fw-bold mb-0 text-warning">{{ $ticketsPendientes }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 py-3 bg-danger text-white">
                <div class="card-body">
                    <div class="small mb-1">Low Stock</div>
                    <h3 class="fw-bold mb-0">{{ $lowStock }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 py-3">
                <div class="card-body">
                    <div class="text-muted small mb-1">Total Productos</div>
                    <h3 class="fw-bold mb-0">{{ $totalProductos }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-end mb-3">
        <h5 class="text-dark mb-0">Últimos Productos Agregados</h5>
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
                    @if (isset($ultimosProductos) && count($ultimosProductos) > 0)
                        @foreach ($ultimosProductos as $producto)
                            <tr>
                                <td>
                                    @if ($producto->imagen)
                                        <img src="{{ asset($producto->imagen) }}" alt="img" style="width: 40px; height: 40px; object-fit: contain;">
                                    @else
                                        <span style="font-size: 1.5rem;">🍾</span>
                                    @endif
                                </td>
                                <td class="fw-bold">{{ $producto->nombre }}</td>
                                <td class="text-muted">{{ $producto->descripcion ?? 'N/A' }}</td>
                                <td>{{ $producto->categoria->nombre ?? 'Sin Categoría' }}</td>
                                <td class="fw-bold text-success">${{ number_format($producto->precio, 2) }}</td>
                                <td>
                                    <span class="badge {{ $producto->stock <= 10 ? 'bg-danger' : 'bg-success' }}">
                                        {{ $producto->stock }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No hay productos registrados.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection