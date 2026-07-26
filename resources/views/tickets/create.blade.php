@extends('layouts.app')

@section('titulo_modulo', ' Punto de Venta (POS)')
@section('subtitulo_modulo', 'Cajero: ' . Auth::user()->name)

@section('content')
<div class="container-fluid px-3 py-2">

    <!-- ========================================== -->
    <!-- CABECERA DEL POS: FILTROS + BOTÓN VOLVER   -->
    <!-- ========================================== -->
    {{-- Cambiamos align-items-stretch por align-items-center para que el botón derecho no se deforme --}}
    <div class="d-flex flex-column flex-xl-row justify-content-between align-items-center mb-3 gap-3">
        
        <!-- Motor de Búsqueda y Filtrado Multicriterio -->
        <form action="{{ route('tickets.create') }}" method="GET" id="formBusquedaPOS" class="card shadow-sm border-0 flex-grow-1 mb-0" style="border-radius: 12px;">
            <div class="card-body p-2">
                <div class="row g-2 align-items-center">
                    
                    <div class="col-md-3">
                        <input type="text" id="buscadorPOS" name="nombre" class="form-control  bg-light" placeholder="🔍 Buscar por nombre..." value="{{ request('nombre') }}">
                    </div>

                    <div class="col-md-2">
                        <select class="form-select  bg-light" name="categoria_id">
                            <option value="">Todas las Categorías</option>
                            @if(isset($categorias))
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select class="form-select  bg-light" name="orden_stock">
                            <option value="">Stock...</option>
                            <option value="desc" {{ request('orden_stock') == 'desc' ? 'selected' : '' }}>Más stock</option>
                            <option value="asc" {{ request('orden_stock') == 'asc' ? 'selected' : '' }}>Menos stock</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select class="form-select  bg-light" name="orden_precio">
                            <option value="">Precio...</option>
                            <option value="desc" {{ request('orden_precio') == 'desc' ? 'selected' : '' }}>Más caro</option>
                            <option value="asc" {{ request('orden_precio') == 'asc' ? 'selected' : '' }}>Más barato</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-dark w-100 fw-bold">Filtrar</button>
                            <a href="{{ route('tickets.create') }}" class="btn btn-outline-secondary w-100 fw-bold">Limpiar</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Botón de Volver a Tickets equilibrado -->
        <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary shadow-sm px-4 fw-bold text-nowrap py-2" style="border-radius: 12px;">
            ⬅ Volver a Tickets
        </a>
        
    </div>

    <!-- ========================================== -->
    <!-- MANEJO DE ALERTAS (FLASH DATA & ERRORS)    -->
    <!-- ========================================== -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <strong>¡Venta exitosa!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <strong>¡Hubo un problema con la venta!</strong>
        <ul class="mb-0 mt-1">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- ========================================== -->
    <!-- CUERPO DEL POS (GRID DE PRODUCTOS Y TICKET)-->
    <!-- ========================================== -->
    <div class="row g-3">
        
        <!-- PANEL IZQUIERDO: CATÁLOGO -->
        <div class="col-lg-8">
            
            <div class="row g-3" id="listaProductos" style="height: 75vh; overflow-y: auto; align-content: flex-start;">
                
                {{-- CAMBIO CLAVE: Usamos @forelse para detectar si hay resultados --}}
                @forelse($productos as $producto)
                    <div class="col-md-4 col-sm-6 item-producto" data-nombre="{{ strtolower($producto->nombre) }}">
                        <div class="card h-100 shadow-sm border-0 btn-agregar-producto" style="cursor: pointer; transition: transform 0.2s;" 
                            data-id="{{ $producto->id }}" 
                            data-nombre="{{ $producto->nombre }}" 
                            data-precio="{{ $producto->precio }}" 
                            data-stock="{{ $producto->stock }}">
                            
                            <div class="d-flex justify-content-center align-items-center bg-white rounded-top p-2" style="height: 160px;">
                                @if($producto->imagen)
                                    <img src="{{ asset($producto->imagen) }}" class="img-fluid" style="object-fit: contain; max-height: 100%; max-width: 100%;">
                                @else
                                    <span style="font-size: 4rem; color: #ccc;">🍾</span>
                                @endif
                            </div>
                            
                            <div class="card-body p-3 text-center bg-light border-top">
                                <h6 class="card-title fw-bold mb-0 text-truncate" title="{{ $producto->nombre }}">{{ $producto->nombre }}</h6>
                                <small class="text-muted d-block mb-2">{{ $producto->descripcion ?? 'Sin descripción' }}</small>
                                <h5 class="text-success fw-bold mb-2">${{ number_format($producto->precio, 2) }}</h5>
                                <span class="badge {{ $producto->stock <= 5 ? 'bg-danger' : 'bg-secondary' }}">Stock: {{ $producto->stock }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- ESTADO VACÍO: Se muestra cuando la consulta no devuelve ningún producto --}}
                    <div class="col-12 d-flex flex-column justify-content-center align-items-center text-center py-5" style="height: 100%;">
                        <div class="bg-white p-5 rounded-4 shadow-sm border" style="max-width: 400px;">
                            <i class="fa-solid fa-wine-bottle fa-3x text-muted mb-3 opacity-50"></i>
                            <h5 class="fw-bold text-dark">No hay productos disponibles</h5>
                            <p class="text-muted small mb-4">No se encontraron licores con los filtros actuales o la categoría seleccionada no tiene stock.</p>
                            <a href="{{ route('tickets.create') }}" class="btn btn-primary shadow-sm px-4 rounded-pill">
                                <i class="fa-solid fa-rotate-right me-2"></i>Restablecer Filtros
                            </a>
                        </div>
                    </div>
                @endforelse
                
            </div>
        </div>

        <!-- PANEL DERECHO: TICKET DE COMPRA -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-dark text-white py-3">
                    <h5 class="mb-0 text-center">🧾 Detalle de Venta</h5>
                </div>

                <div class="card-body p-0 d-flex flex-column">
                    <div class="p-3 flex-grow-1" id="carritoContenedor" style="overflow-y: auto; max-height: 45vh;">
                        <div id="carritoVacio" class="text-center text-muted py-5">
                            <span class="fs-1 d-block mb-2">🛒</span>
                            Selecciona productos a la izquierda para agregarlos al ticket.
                        </div>
                        <ul class="list-group list-group-flush" id="listaCarrito"></ul>
                    </div>

                    <div class="bg-light p-3 border-top mt-auto">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fw-bold">Subtotal:</span>
                            <span class="fw-bold text-dark" id="subtotalDisplay">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <h4 class="fw-bold mb-0">Total a Pagar:</h4>
                            <h4 class="fw-bold text-success mb-0" id="totalDisplay">$0.00</h4>
                        </div>

                        <form action="{{ route('tickets.store') }}" method="POST" id="formVenta">
                            @csrf
                            <div id="inputsOcultos"></div>
                            <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm" id="btnCobrar" disabled>
                                💵 COBRAR VENTA
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
@vite(['resources/js/pos.js'])
@endpush