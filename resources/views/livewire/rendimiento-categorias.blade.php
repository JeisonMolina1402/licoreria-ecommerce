<div class="col-lg-7" id="tabla-categorias">
    <div class="card shadow-sm border-0 rounded-4 h-100">
        <!-- CABECERA: RENDIMIENTO POR CATEGORÍA -->
        <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-md-row align-items-center justify-content-md-between gap-3 gap-md-0 text-center text-md-start">
            <h6 class="m-0 fw-bold text-dark"><i class="fa-solid fa-chart-pie text-success me-2"></i> Rendimiento por Categoría</h6>
            <select wire:model.live="ranking_categorias" class="form-select form-select-sm border-success fw-bold text-success w-100 w-md-auto" style="cursor: pointer; max-width: 250px;">
                <option value="ventas">🥇 Más Vendidos (Unidades)</option>
                <option value="ganancia">💰 Mayor Ganancia (Dinero)</option>
                <option value="cero">📉 Sin Movimiento (0 Ventas)</option>
            </select>
        </div>

        <div class="card-body p-4 d-flex flex-column">
            <div wire:ignore class="d-flex justify-content-center align-items-center mb-4" style="position: relative; min-height: 250px;">
                @if ($tieneTickets)
                    <canvas id="graficoCategorias" data-nombres="{{ $nombresCategorias }}" data-cantidades="{{ $cantidadesCategorias }}"></canvas>
                @else
                    <div class="text-center text-muted">
                        <i class="fa-solid fa-chart-simple fs-1 mb-2"></i>
                        <p>No hay datos suficientes.</p>
                    </div>
                @endif
            </div>

            <div class="table-responsive border rounded-3 mt-2 flex-grow-1 transition-all" wire:loading.class="opacity-50" wire:target="ranking_categorias">
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
                                <td class="py-2 text-primary fw-bold">${{ number_format($datos['ventas'], 2) }}</td>
                                <td class="py-2 text-success fw-bold">${{ number_format($datos['ganancia'], 2) }}</td>
                                <td class="py-2">
                                    <span class="badge @if ($datos['unidades'] == 0) bg-danger @else bg-success @endif rounded-pill px-2">
                                        {{ $datos['unidades'] }} unid.
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-muted">No hay datos en este rango.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>