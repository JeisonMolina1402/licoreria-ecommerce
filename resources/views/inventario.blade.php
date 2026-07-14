@extends('layouts.app')

@section('titulo_modulo', 'Gestión de Inventario')

@section('content')
    <form action="{{ route('inventario') }}" method="GET" id="formBusqueda" class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="nombre" list="sugerenciasProductos" autocomplete="off" placeholder="🔍 Buscar por nombre..." value="{{ request('nombre') }}">
                    <datalist id="sugerenciasProductos">
                        @foreach($nombresProductos as $nombre)
                            <option value="{{ $nombre }}">
                        @endforeach
                    </datalist>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="categoria_id">
                        <option value="">Categoría</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="orden_stock">
                        <option value="">Stock...</option>
                        <option value="desc" {{ request('orden_stock') == 'desc' ? 'selected' : '' }}>Más stock</option>
                        <option value="asc" {{ request('orden_stock') == 'asc' ? 'selected' : '' }}>Menos stock</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="orden_precio">
                        <option value="">Precio...</option>
                        <option value="desc" {{ request('orden_precio') == 'desc' ? 'selected' : '' }}>Más caro</option>
                        <option value="asc" {{ request('orden_precio') == 'asc' ? 'selected' : '' }}>Más barato</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark w-100">Filtrar</button>
                        <a href="{{ route('inventario') }}" class="btn btn-outline-secondary w-100">Limpiar</a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="d-flex justify-content-between align-items-end mb-3">
        <h5 class="text-dark mb-0 d-none d-md-block">Lista de Productos</h5>
        <button class="btn btn-primary btn-sm fw-bold shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#modalAgregarProducto" onclick="prepararModalCrear()">
            + Agregar Producto
        </button>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <strong>¡Éxito!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <strong>¡Faltan datos o son incorrectos!</strong>
        <ul class="mb-0 mt-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

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
                @if(isset($productos) && count($productos) > 0)
                    @foreach($productos as $producto)
                    <tr>
                        <td style="font-size: 1.5rem;">
                            @if($producto->imagen)
                            <img src="{{ asset($producto->imagen) }}" alt="img" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                            @else 🍾 @endif
                        </td>
                        <td class="fw-bold">{{ $producto->nombre }}</td>
                        <td class="text-muted small">{{ \Illuminate\Support\Str::limit($producto->descripcion, 30) }}</td>
                        <td class="d-none d-md-table-cell">{{ $producto->categoria->nombre ?? 'Sin Categoría' }}</td>
                        <td class="text-muted">${{ number_format($producto->precio_compra, 2) }}</td>
                        <td>${{ number_format($producto->precio, 2) }}</td>
                        <td>
                            @if($producto->stock <= 10) 
                                <span class="badge bg-danger">{{ $producto->stock }}</span>
                            @else 
                                <span class="badge bg-success">{{ $producto->stock }}</span> 
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary mb-1 mb-md-0" data-bs-toggle="modal" data-bs-target="#modalAgregarProducto" 
                                data-id="{{ $producto->id }}" data-nombre="{{ $producto->nombre }}" data-categoria="{{ $producto->categoria_id }}" 
                                data-descripcion="{{ $producto->descripcion }}" data-precio_compra="{{ $producto->precio_compra }}" 
                                data-precio="{{ $producto->precio }}" data-stock="{{ $producto->stock }}" 
                                data-imagen="{{ $producto->imagen ? asset($producto->imagen) : '' }}" onclick="prepararModalEditar(this)">✏️ Editar</button>
                            <form action="{{ route('inventario.destroy', $producto->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar producto?')">
                                @csrf @method('DELETE')
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

    <div class="d-flex justify-content-end">
        {{ $productos->links('pagination::bootstrap-5') }}
    </div>

    <div class="modal fade" id="modalAgregarProducto" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <form id="formProducto" action="{{ route('inventario.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                @csrf
                <div class="modal-header bg-white border-bottom-0 pt-4 pb-3 px-4">
                    <h5 class="modal-title fw-bold text-dark">📦 INFORMACIÓN DEL PRODUCTO</h5>
                    <button type="button" class="btn-close" onclick="confirmarCancelacion()"></button>
                </div>
                <div class="modal-body px-4" style="background-color: #f0f4f8;">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">NOMBRE DEL LICOR *</label>
                            <input type="text" class="form-control" name="nombre" required>
                            
                            <label class="form-label fw-bold mt-3">CATEGORÍA *</label>
                            <select class="form-select" name="categoria_id" required>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                @endforeach
                            </select>
                            
                            <label class="form-label fw-bold mt-3">DESCRIPCIÓN</label>
                            <textarea class="form-control" name="descripcion" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">PRECIO COMPRA</label>
                            <input type="number" step="0.01" class="form-control" name="precio_compra">
                            
                            <label class="form-label fw-bold mt-3">PRECIO VENTA *</label>
                            <input type="number" step="0.01" class="form-control" name="precio" required>
                            
                            <label class="form-label fw-bold mt-3">STOCK *</label>
                            <input type="number" class="form-control" name="stock" required>

                            <label class="form-label fw-bold mt-3">IMAGEN</label>
                            <div class="d-flex align-items-center">
                                <label for="imagenInput" class="btn btn-outline-secondary btn-sm">Subir Foto</label>
                                <input type="file" id="imagenInput" name="imagen" class="d-none" accept="image/*" onchange="mostrarVistaPrevia(event)">
                                <img id="previewImg" src="" class="img-thumbnail ms-3 d-none" style="width: 50px; height: 50px; object-fit: cover;">
                                <span id="uploadPlaceholder" class="text-muted ms-2 small">No hay archivo</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top-0 px-4 py-3">
                    <button type="button" class="btn btn-light" onclick="confirmarCancelacion()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/inventario.js'])
@endpush