<div>
    <!-- CABECERA: FILTROS -->
    <div class="d-flex flex-column flex-xl-row justify-content-between align-items-center mb-3 gap-3">
        <div class="card shadow-sm border-0 flex-grow-1 mb-0" style="border-radius: 12px;">
            <div class="card-body p-2">
                <div class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <input type="text" wire:model.live.debounce.300ms="nombre" class="form-control bg-light" placeholder="🔍 Buscar por nombre..." autocomplete="off">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select bg-light" wire:model.live="categoria_id">
                            <option value="">Todas las Categorías</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select bg-light" wire:model.live="orden_stock">
                            <option value="">Stock...</option>
                            <option value="desc">Más stock</option>
                            <option value="asc">Menos stock</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select bg-light" wire:model.live="orden_precio">
                            <option value="">Precio...</option>
                            <option value="desc">Más caro</option>
                            <option value="asc">Más barato</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <!-- BOTÓN LIMPIAR CON SPINNER LIVEWIRE -->
                        <button type="button" wire:click="limpiar" class="btn btn-outline-secondary w-100 fw-bold" wire:loading.attr="disabled" wire:target="limpiar">
                            <span wire:loading.remove wire:target="limpiar">Limpiar</span>
                            <span wire:loading wire:target="limpiar" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary shadow-sm px-4 fw-bold text-nowrap py-2" style="border-radius: 12px;">
            ⬅ Volver a Tickets
        </a>
    </div>

    <!-- CUERPO DEL POS -->
    <div class="row g-3">
        <!-- PANEL IZQUIERDO: CATÁLOGO (Controlado por Livewire) -->
        <div class="col-lg-8">
            <!-- AGREGADO: transition-all y wire:loading.class="opacity-50" PARA FEEDBACK VISUAL -->
            <div class="row g-3 transition-all" style="height: 75vh; overflow-y: auto; align-content: flex-start;" wire:loading.class="opacity-50">
                @forelse($productos as $producto)
                    <div class="col-md-4 col-sm-6 item-producto">
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
                                <h5 class="text-success fw-bold mb-2 mt-2">${{ number_format($producto->precio, 2) }}</h5>
                                <span class="badge {{ $producto->stock <= 5 ? 'bg-danger' : 'bg-secondary' }}">Stock: {{ $producto->stock }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 d-flex flex-column justify-content-center align-items-center text-center py-5" style="height: 100%;">
                        <div class="bg-white p-5 rounded-4 shadow-sm border" style="max-width: 400px;">
                            <i class="fa-solid fa-wine-bottle fa-3x text-muted mb-3 opacity-50"></i>
                            <h5 class="fw-bold text-dark">No hay productos</h5>
                            <p class="text-muted small mb-4">No se encontraron licores con los filtros actuales.</p>
                            <button wire:click="limpiar" class="btn btn-primary shadow-sm px-4 rounded-pill">
                                Restablecer Filtros
                            </button>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- PANEL DERECHO: TICKET DE COMPRA (Ignorado por Livewire) -->
        <div class="col-lg-4" wire:ignore>
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
                        
                        <!-- AGREGADA CLASE form-cargando PARA EVITAR COBROS DUPLICADOS POR DOBLE CLIC -->
                        <form action="{{ route('tickets.store') }}" method="POST" id="formVenta" class="form-cargando">
                            @csrf
                            <!-- Selector de Método de Pago -->
                            <div class="mb-3">
                                <label class="form-label text-muted fw-bold small mb-1">Método de Pago:</label>
                                <select name="metodo_pago" class="form-select border-2 bg-white fw-bold text-dark shadow-sm" required>
                                    <option value="efectivo" selected>💵 Efectivo</option>
                                    <option value="transferencia">📱 Transferencia Bancaria</option>
                                </select>
                            </div>
                            
                            <div id="inputsOcultos"></div>
                            
                            <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm py-3" id="btnCobrar" disabled style="font-size: 1.1rem; border-radius: 10px;">
                                COBRAR VENTA
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>