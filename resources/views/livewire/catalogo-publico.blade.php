<div class="container py-2 " id="catalogo">

    <div class="text-center mb-5">
        <h2 class="titulo-premium mb-2 text-uppercase" style="font-size: 2.8rem; text-shadow: 1px 1px 2px var(--sombra_dorada);">Nuestro Catálogo</h2>
        <p class="text-muted" style="font-size: 1.1rem;">Selecciona tus botellas y resérvalas en minutos.</p>

        <!-- Buscador en Vivo de Livewire -->
        <div class="d-flex justify-content-center mx-auto mt-4" style="max-width: 650px;">
            <div class="position-relative w-100">
                <input class="form-control form-control-lg rounded-pill bg-white shadow-sm" type="search"
                    wire:model.live.debounce.300ms="buscar" placeholder="Busca tu licor favorito..." autocomplete="off"
                    style="padding-left: 1.5rem; border: 1px solid #e0e0e0; font-family: 'Lora', serif;">
            </div>
        </div>
    </div>

    <div class="row">
        <!-- BARRA LATERAL: CATEGORÍAS (Escritorio) -->
        <div class="col-lg-3 mb-4 d-none d-lg-block">
            <div class="card border border-light shadow-sm rounded-0 sticky-top" style="top: 110px;">
                <div class="card-header bg-white fw-bold py-3 fs-5 border-bottom border-light">
                    <span style="color: var(--color_primario);">🏷️</span> Categorías
                </div>
                <div class="list-group list-group-flush">
                    <button wire:click="setCategoria('')"
                        class="list-group-item list-group-item-action py-3 text-start {{ empty($categoria_slug) ? 'fw-bold bg-light text-dark' : 'text-muted' }}"
                        style="{{ empty($categoria_slug) ? 'border-left: 4px solid var(--color_primario);' : 'border-left: 4px solid transparent;' }}">
                        Todas las botellas
                    </button>
                    @foreach ($categorias as $cat)
                        <button wire:click="setCategoria('{{ $cat->slug }}')"
                            class="list-group-item list-group-item-action py-3 text-start {{ $categoria_slug == $cat->slug ? 'fw-bold bg-light text-dark' : 'text-muted' }}"
                            style="{{ $categoria_slug == $cat->slug ? 'border-left: 4px solid var(--color_primario);' : 'border-left: 4px solid transparent;' }}">
                            {{ $cat->nombre }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- VISTA DE CUADRÍCULA: PRODUCTOS -->
        <div class="col-lg-9">
            
            <!-- Spinner de carga para feedback visual -->
            <div wire:loading class="w-100 text-center py-4">
                <div class="spinner-border text-warning" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>

            <div class="row g-3" wire:loading.class="opacity-50">
                @forelse($productos as $producto)
                    <div class="col-6 col-md-4 col-xl-4">
                        <div class="card h-100 shadow-sm product-card">
                            <div class="card-img-container">
                                @if ($producto->imagen)
                                    <img src="{{ asset($producto->imagen) }}" alt="{{ $producto->nombre }}">
                                @else
                                    <span style="font-size: 3rem; color: #ccc;">🍾</span>
                                @endif
                            </div>

                            <div class="card-body d-flex flex-column text-center p-3">
                                <h5 class="titulo-producto" title="{{ $producto->nombre }}">{{ $producto->nombre }}</h5>
                                <small class="text-muted mb-2">{{ $producto->descripcion ?? '750ml' }}</small>

                                <h4 class="fw-bold mb-3 mt-auto" style="color: var(--color_primario);">
                                    ${{ number_format($producto->precio, 2) }}
                                </h4>

                                <!-- Botón de Agregar (Usará Delegación de Eventos en JS) -->
                                <button class="btn btn-outline-dark w-100 rounded-pill fw-bold btn-agregar"
                                    data-id="{{ $producto->id }}" data-nombre="{{ $producto->nombre }}"
                                    data-precio="{{ $producto->precio }}"
                                    data-imagen="{{ asset($producto->imagen) }}">
                                    + Agregar
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <span style="font-size: 4rem;">🍷</span>
                        <h3 class="text-muted mt-3">No encontramos licores con ese filtro.</h3>
                        <button wire:click="setCategoria('')" class="btn btn-premium mt-3 rounded-pill px-4">Ver todo el catálogo</button>
                    </div>
                @endforelse
            </div>

            <!-- Paginación de Livewire -->
            <div class="d-flex justify-content-center mt-5">
                {{ $productos->links() }}
            </div>
        </div>
    </div>
</div>