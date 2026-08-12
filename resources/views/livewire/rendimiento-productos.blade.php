<div class="col-12" id="tabla-ranking">
    <div class="card shadow-sm border-0 h-100 rounded-4">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fa-solid fa-trophy text-warning me-2"></i> Rendimiento de Productos
            </h6>

            <div class="d-flex align-items-center gap-3">
                <select wire:model.live="ranking_productos" class="form-select form-select-sm border-primary fw-bold text-primary" style="cursor: pointer;">
                    <option value="ventas">🥇 Más Vendidos (Unidades)</option>
                    <option value="ganancia">💰 Mayor Ganancia (Dinero)</option>
                    <option value="cero">📉 Sin Movimiento (0 Ventas)</option>
                </select>
                <span class="badge bg-light text-dark border">Página {{ $productosTop->currentPage() }} de {{ $productosTop->lastPage() }}</span>
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
                            <th class="py-3 text-uppercase text-muted small text-success fw-bold">Ganancia Total</th>
                            <th class="py-3 text-uppercase text-muted small border-start">Unidades</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productosTop as $index => $producto)
                            @php
                                $unidades = $producto->total_vendido ?? 0;
                                $totalVenta = $producto->ingreso_generado ?? 0; 
                                $ganancia = $producto->ganancia_generada ?? 0;  
                                $totalInversion = $totalVenta - $ganancia;      
                            @endphp
                            <tr>
                                <td class="px-4 py-3 fw-bold text-muted text-start">
                                    #{{ ($productosTop->currentPage() - 1) * $productosTop->perPage() + $loop->iteration }}
                                </td>
                                <td class="py-3 text-start">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-white border rounded p-1 me-3 d-flex justify-content-center align-items-center shadow-sm" style="width: 45px; height: 45px;">
                                            @if ($producto->imagen)
                                                <img src="{{ asset($producto->imagen) }}" alt="img" class="rounded" style="max-width: 100%; max-height: 100%; object-fit: cover;">
                                            @else
                                                <span class="fs-5">🍷</span>
                                            @endif
                                        </div>
                                        <strong>{{ $producto->nombre }}</strong>
                                    </div>
                                </td>
                                <td class="py-3">{{ $producto->categoria->nombre ?? 'Sin Categoría' }}</td>
                                <td class="py-3 text-muted">${{ number_format($producto->precio_compra, 2) }}</td>
                                <td class="py-3">${{ number_format($producto->precio, 2) }}</td>
                                <td class="py-3 border-start text-danger">${{ number_format($totalInversion, 2) }}</td>
                                <td class="py-3 text-primary fw-bold">${{ number_format($totalVenta, 2) }}</td>
                                <td class="py-3 text-success fw-bold">${{ number_format($ganancia, 2) }}</td>
                                <td class="py-3 border-start">
                                    @if ($unidades > 0)
                                        <span class="badge bg-success rounded-pill px-3 py-2" style="font-size: 0.9rem;">{{ $unidades }} unid.</span>
                                    @else
                                        <span class="badge bg-danger rounded-pill px-3 py-2" style="font-size: 0.9rem;">0 unid.</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-box-open fs-2 mb-2 d-block"></i> No hay productos en este rango.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-top p-3 bg-light rounded-bottom-4">
                {{ $productosTop->links() }}
            </div>
        </div>
    </div>
</div>