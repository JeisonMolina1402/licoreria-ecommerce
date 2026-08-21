<div>
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <!-- align-items-end para alinear el botón rojo con los inputs -->
            <div class="row g-2 align-items-end">
                
                <!-- Buscador -->
                <div class="col-md-4">
                    <label class="form-label text-muted mb-1" style="font-size: 0.75rem; font-weight: 600;">Buscar Producto</label>
                    <div class="input-group input-group-sm shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0 ps-0 text-muted" wire:model.live.debounce.300ms="nombre"
                            list="sugerenciasProductos" autocomplete="off" placeholder="Nombre...">
                    </div>
                    <datalist id="sugerenciasProductos">
                        @foreach ($nombresProductos as $nombreSugerido)
                            <option value="{{ $nombreSugerido }}">
                        @endforeach
                    </datalist>
                </div>

                <!-- Categoría -->
                <div class="col-md-3">
                    <label class="form-label text-muted mb-1" style="font-size: 0.75rem; font-weight: 600;">Categoría</label>
                    <select class="form-select form-select-sm text-muted shadow-sm" wire:model.live="categoria_id">
                        <option value="">Todas las categorías...</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Orden Stock -->
                <div class="col">
                    <label class="form-label text-muted mb-1" style="font-size: 0.75rem; font-weight: 600;">Stock</label>
                    <select class="form-select form-select-sm text-muted shadow-sm" wire:model.live="orden_stock">
                        <option value="">Cualquiera...</option>
                        <option value="desc">Más stock</option>
                        <option value="asc">Menos stock</option>
                    </select>
                </div>

                <!-- Orden Precio -->
                <div class="col">
                    <label class="form-label text-muted mb-1" style="font-size: 0.75rem; font-weight: 600;">Precio</label>
                    <select class="form-select form-select-sm text-muted shadow-sm" wire:model.live="orden_precio">
                        <option value="">Cualquiera...</option>
                        <option value="desc">Más caro</option>
                        <option value="asc">Más barato</option>
                    </select>
                </div>

                <!-- Botón Limpiar -->
                <div class="col-auto">
                    <button type="button" wire:click="limpiar" class="btn btn-outline-danger btn-sm shadow-sm" title="Limpiar Filtros" wire:loading.attr="disabled" wire:target="limpiar">
                        <span wire:loading.remove wire:target="limpiar"><i class="fa-solid fa-eraser"></i></span>
                        <span wire:loading wire:target="limpiar" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </button>
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

    <!-- TABLA CON EFECTO DE CARGA TRANSLÚCIDO -->
    <div class="table-responsive bg-white p-3 rounded-3 shadow-sm mb-4 transition-all" wire:loading.class="opacity-50">
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
                            <td class="fw-bold">
                                {{ $producto->nombre }}
                                @if ($producto->estado === 'inactivo')
                                    <span class="badge bg-danger ms-2" style="font-size: 0.7rem;">Inactivo</span>
                                @endif
                            </td>
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

                                {{-- BOTÓN DINÁMICO DE ACTIVAR / DESACTIVAR (CON ALERTA SWEETALERT) --}}
                                {{-- Se agregó la clase 'form-eliminar' al form y se quitó el onclick viejo --}}
                                <form action="{{ route('inventario.destroy', $producto->id) }}" method="POST"
                                    class="d-inline form-eliminar">
                                    @csrf
                                    @method('DELETE')
                                    @if ($producto->estado === 'activo' || $producto->estado === null)
                                        <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm fw-bold">
                                            <i class="fa-solid fa-ban"></i> Desactivar
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-outline-success shadow-sm fw-bold">
                                            <i class="fa-solid fa-check"></i> Activar
                                        </button>
                                    @endif
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

   <!-- FOOTER: PAGINACIÓN Y BOTÓN PDF -->
    <div class="row align-items-center mt-4 mb-2 pt-2 border-top">
        
        <!-- PAGINACIÓN (Alineada a la izquierda en PC, al centro en Móviles) -->
        <!-- overflow-auto asegura que si los números son muchos en celular, se puedan deslizar sin romper la pantalla -->
        <div class="col-12 col-lg-9 d-flex justify-content-center justify-content-lg-start mb-3 mb-lg-0 overflow-auto" style="scrollbar-width: thin;">
            {{ $productos->links() }}
        </div>

        <!-- BOTÓN PDF (Alineado a la derecha en PC, ancho completo en Móviles) -->
        <div class="col-12 col-lg-3 d-flex justify-content-center justify-content-lg-end">
            <a href="{{ route('inventario.pdf') }}" class="btn btn-danger fw-bold shadow-sm px-4 py-2 w-100 d-flex justify-content-center align-items-center" style="max-width: 220px;" target="_blank">
                <i class="fa-solid fa-file-pdf me-2 fs-5"></i> Descargar PDF
            </a>
        </div>
        
    </div>
</div>
