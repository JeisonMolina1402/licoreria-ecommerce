@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 py-2">

    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
        <div>
            <h2 class="h4 text-dark mb-0">Punto de Venta (POS)</h2>
            <small class="text-muted">Cajero: {{ Auth::user()->name }}</small>
        </div>
        <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary btn-sm fw-bold">⬅ Volver a Tickets</a>
    </div>

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

    <div class="row g-3">
        
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body p-2">
                    <input type="text" id="buscadorPOS" class="form-control form-control-lg bg-light border-0" placeholder="🔍 Buscar licor por nombre...">
                </div>
            </div>

            <div class="row g-3" id="listaProductos" style="height: 70vh; overflow-y: auto; align-content: flex-start;">
                @foreach($productos as $producto)
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
                @endforeach
            </div>
        </div>

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
                        <ul class="list-group list-group-flush" id="listaCarrito">
                        </ul>
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