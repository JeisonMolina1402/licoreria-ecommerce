@extends('layouts.app')

@section('titulo_modulo', 'Gestión de Inventario')
@section('subtitulo_modulo', 'Control de stock, precios y catálogo de licores')

@section('content')

    <!-- LA ALERTA VERDE NATIVA FUE ELIMINADA PORQUE SWEETALERT2 YA HACE EL TRABAJO AUTOMÁTICAMENTE -->

    <!-- AQUÍ ESTÁ LA MAGIA DE LIVEWIRE -->
    @livewire('inventario-table')

    <!-- EL MODAL DE PRODUCTOS CON VALIDACIONES UX (CORREGIDO) -->
    <div class="modal fade" id="modalAgregarProducto" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <!-- AGREGADA LA CLASE form-cargando PARA EVITAR DOBLE CLIC Y SUBIDAS DE FOTO DUPLICADAS -->
            <form id="formProducto" action="{{ route('inventario.store') }}" method="POST" enctype="multipart/form-data"
                class="modal-content border-0 shadow-lg form-cargando" style="border-radius: 16px;">
                @csrf
                <div class="modal-header bg-white border-bottom-0 pt-4 pb-3 px-4">
                    <h5 class="modal-title fw-bold text-dark">📦 INFORMACIÓN DEL PRODUCTO</h5>
                    <!-- Botón visible que muestra la alerta -->
                    <button type="button" class="btn-close" onclick="confirmarCancelacion()"></button>
                    <!-- BOTÓN FANTASMA QUE REALMENTE CIERRA EL MODAL -->
                    <button type="button" id="btnCerrarModalFantasma" data-bs-dismiss="modal" class="d-none"></button>
                </div>

                <div class="modal-body px-4" style="background-color: #f0f4f8;">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <!-- Nombre -->
                            <label class="form-label fw-bold">NOMBRE DEL LICOR *</label>
                            <!-- AGREGADO: required -->
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" name="nombre"
                                value="{{ old('nombre') }}" required>
                            <!-- AGREGADO: clase 'texto-error' -->
                            @error('nombre')
                                <div class="invalid-feedback fw-bold texto-error">{{ $message }}</div>
                            @enderror

                            <!-- Categoría -->
                            <label class="form-label fw-bold mt-3">CATEGORÍA *</label>
                            <!-- AGREGADO: required -->
                            <select class="form-select @error('categoria_id') is-invalid @enderror" name="categoria_id"
                                id="categoriaSelect" onchange="toggleNuevaCategoria()" required>
                                <option value="" disabled {{ old('categoria_id') ? '' : 'selected' }}>Seleccione una
                                    categoría</option>
                                @php
                                    $categoriasModal = \App\Models\Categoria::orderBy('nombre', 'asc')->get();
                                @endphp
                                @foreach ($categoriasModal as $categoria)
                                    <option value="{{ $categoria->id }}"
                                        {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                                <!-- LA OPCIÓN MÁGICA -->
                                <option value="nueva" class="text-primary fw-bold" style="background-color: #e9ecef;">
                                    + Crear Nueva Categoría
                                </option>
                            </select>
                            @error('categoria_id')
                                <div class="invalid-feedback fw-bold texto-error">{{ $message }}</div>
                            @enderror

                            <!-- EL INPUT OCULTO PARA LA NUEVA CATEGORÍA -->
                            <div id="divNuevaCategoria" class="mt-2 d-none">
                                <input type="text" class="form-control border-primary shadow-sm" name="nueva_categoria"
                                    id="nuevaCategoriaInput" placeholder="Escribe el nombre de la nueva categoría...">
                                @error('nueva_categoria')
                                    <div class="text-danger fw-bold small mt-1 texto-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Descripción -->
                            <label class="form-label fw-bold mt-3">DESCRIPCIÓN *</label>
                            <!-- AGREGADO: required -->
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" name="descripcion" rows="2" required>{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback fw-bold texto-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <!-- Precio Compra -->
                            <label class="form-label fw-bold">PRECIO COMPRA *</label>
                            <!-- AGREGADO: required -->
                            <input type="number" step="0.01"
                                class="form-control @error('precio_compra') is-invalid @enderror" name="precio_compra"
                                value="{{ old('precio_compra') }}" required>
                            @error('precio_compra')
                                <div class="invalid-feedback fw-bold texto-error">{{ $message }}</div>
                            @enderror

                            <!-- Precio Venta -->
                            <label class="form-label fw-bold mt-3">PRECIO VENTA *</label>
                            <!-- AGREGADO: required -->
                            <input type="number" step="0.01" class="form-control @error('precio') is-invalid @enderror"
                                name="precio" value="{{ old('precio') }}" required>
                            @error('precio')
                                <div class="invalid-feedback fw-bold texto-error">{{ $message }}</div>
                            @enderror

                            <!-- Stock -->
                            <label class="form-label fw-bold mt-3">STOCK *</label>
                            <!-- AGREGADO: required -->
                            <input type="number" class="form-control @error('stock') is-invalid @enderror" name="stock"
                                value="{{ old('stock') }}" required>
                            @error('stock')
                                <div class="invalid-feedback fw-bold texto-error">{{ $message }}</div>
                            @enderror

                            <!-- Imagen -->
                            <label class="form-label fw-bold mt-3">IMAGEN</label>
                            <div class="d-flex align-items-center">
                                <label for="imagenInput" class="btn btn-outline-secondary btn-sm">Subir Foto</label>
                                <!-- NOTA: Aquí no va 'required' porque al EDITAR un producto no es obligatorio subir foto nueva -->
                                <input type="file" id="imagenInput" name="imagen"
                                    class="d-none @error('imagen') is-invalid @enderror" accept="image/*"
                                    onchange="mostrarVistaPrevia(event)">
                                <img id="previewImg" src="" class="img-thumbnail ms-3 d-none"
                                    style="width: 50px; height: 50px; object-fit: cover;">
                                <span id="uploadPlaceholder" class="text-muted ms-2 small">No hay archivo</span>
                            </div>
                            @error('imagen')
                                <div class="text-danger small fw-bold mt-1 texto-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-white border-top-0 px-4 py-3">
                    <button type="button" class="btn btn-light" onclick="confirmarCancelacion()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Guardar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    @vite(['resources/js/inventario.js'])

    <!-- TRUCO A PRUEBA DE FALLOS: Si hay errores, hacemos un clic fantasma -->
    @if ($errors->any())
        <button type="button" id="btnAutoOpenModal" data-bs-toggle="modal" data-bs-target="#modalAgregarProducto"
            class="d-none"></button>
        <script>
            window.addEventListener('DOMContentLoaded', (event) => {
                setTimeout(() => {
                    document.getElementById('btnAutoOpenModal').click();
                }, 150);
            });
        </script>
    @endif

    <script>
        function toggleNuevaCategoria() {
            const select = document.getElementById('categoriaSelect');
            const divNueva = document.getElementById('divNuevaCategoria');
            const inputNueva = document.getElementById('nuevaCategoriaInput');

            if (select.value === 'nueva') {
                divNueva.classList.remove('d-none');
                inputNueva.focus();
            } else {
                divNueva.classList.add('d-none');
                inputNueva.value = '';
            }
        }
    </script>
@endpush
