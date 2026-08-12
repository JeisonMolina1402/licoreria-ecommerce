<div>
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="nombre"
                        list="sugerenciasProductos" autocomplete="off" placeholder="🔍 Buscar por nombre...">
                    <datalist id="sugerenciasProductos">
                        @foreach ($nombresProductos as $nombreSugerido)
                            <option value="{{ $nombreSugerido }}">
                        @endforeach
                    </datalist>
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model.live="categoria_id">
                        <option value="">Categoría...</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" wire:model.live="orden_stock">
                        <option value="">Stock...</option>
                        <option value="desc">Más stock</option>
                        <option value="asc">Menos stock</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" wire:model.live="orden_precio">
                        <option value="">Precio...</option>
                        <option value="desc">Más caro</option>
                        <option value="asc">Más barato</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="button" wire:click="limpiar" class="btn btn-outline-secondary w-100">Limpiar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-end mb-3">
        <h5 class="text-dark mb-0 d-none d-md-block">Lista de Productos</h5>
        <button type="button" class="btn btn-primary fw-bold px-4" data-bs-toggle="modal"
            data-bs-target="#modalAgregarProducto" onclick="prepararModalCrear()">
            + Agregar Producto
        </button>
    </div>

    <div class="table-responsive bg-white p-3 rounded-3 shadow-sm mb-4">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Imagen</th>
                    <th>Nombre Producto</th>
                    <th>Descripción</th>
                    <th class="d-none d-md-table-cell">Categoría</th>
                    <th>Precio Compra</th>
                    <th>Precio Venta</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($productos) && count($productos) > 0)
                    @foreach ($productos as $producto)
                        <tr>
                            <td style="font-size: 1.5rem;">
                                @if ($producto->imagen)
                                    <img src="{{ asset($producto->imagen) }}" alt="img" class="rounded"
                                        style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                    🍾
                                @endif
                            </td>
                            <td class="fw-bold">{{ $producto->nombre }}</td>
                            <td class="text-muted small">
                                {{ \Illuminate\Support\Str::limit($producto->descripcion, 30) }}</td>
                            <td class="d-none d-md-table-cell">{{ $producto->categoria->nombre ?? 'Sin Categoría' }}
                            </td>
                            <td class="text-muted">${{ number_format($producto->precio_compra, 2) }}</td>
                            <td>${{ number_format($producto->precio, 2) }}</td>
                            <td>
                                @if ($producto->stock <= 10)
                                    <span class="badge bg-danger">{{ $producto->stock }}</span>
                                @else
                                    <span class="badge bg-success">{{ $producto->stock }}</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary mb-1 mb-md-0" data-bs-toggle="modal"
                                    data-bs-target="#modalAgregarProducto" data-id="{{ $producto->id }}"
                                    data-nombre="{{ $producto->nombre }}"
                                    data-categoria="{{ $producto->categoria_id }}"
                                    data-descripcion="{{ $producto->descripcion }}"
                                    data-precio_compra="{{ $producto->precio_compra }}"
                                    data-precio="{{ $producto->precio }}" data-stock="{{ $producto->stock }}"
                                    data-imagen="{{ $producto->imagen ? asset($producto->imagen) : '' }}"
                                    onclick="prepararModalEditar(this)">✏️ Editar</button>

                                <form action="{{ route('inventario.destroy', $producto->id) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('¿Eliminar producto?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">🗑️ Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No se encontraron productos.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <div>
            <a href="{{ route('inventario.pdf', ['nombre' => $nombre, 'categoria_id' => $categoria_id, 'orden_stock' => $orden_stock, 'orden_precio' => $orden_precio]) }}"
                class="btn btn-danger btn-sm fw-bold shadow-sm px-3 py-2" target="_blank">
                <i class="fa-solid fa-file-pdf me-1"></i> Descargar PDF
            </a>
        </div>
        <div>
            {{ $productos->links() }}
        </div>
    </div>
</div>
