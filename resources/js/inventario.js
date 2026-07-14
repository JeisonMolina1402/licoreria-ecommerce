// resources/js/inventario.js

// Función para mostrar la vista previa de la imagen al subirla
window.mostrarVistaPrevia = function(event) {
    const input = event.target;
    const reader = new FileReader();
    reader.onload = function() {
        document.getElementById('previewImg').src = reader.result;
        document.getElementById('previewImg').classList.remove('d-none');
        document.getElementById('uploadPlaceholder').classList.add('d-none');
    }
    if (input.files && input.files[0]) reader.readAsDataURL(input.files[0]);
}

// Función para confirmar la cancelación y limpiar el modal
window.confirmarCancelacion = function() {
    if (confirm("¿Estás seguro de que deseas cancelar? Los datos no guardados se perderán.")) {
        const modalEl = document.getElementById('modalAgregarProducto');
        const modalInst = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modalInst.hide();
        
        // Limpiar el formulario
        document.getElementById('formProducto').reset();
        document.getElementById('formProducto').action = '/inventario/guardar'; // Restaurar ruta de guardado
        
        // Restaurar el título original
        document.querySelector('#modalAgregarProducto .modal-title').innerHTML = '<span class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; font-size: 1.2rem; flex-shrink: 0;">📦</span> INFORMACIÓN DEL PRODUCTO';
        
        // Limpiar la imagen
        document.getElementById('previewImg').src = "";
        document.getElementById('previewImg').classList.add('d-none');
        document.getElementById('uploadPlaceholder').classList.remove('d-none');
    }
}

// Función para llenar el modal con los datos del producto a editar
window.prepararModalEditar = function(boton) {
    // Extraer datos del botón
    const id = boton.getAttribute('data-id');
    const nombre = boton.getAttribute('data-nombre');
    const categoria = boton.getAttribute('data-categoria');
    const descripcion = boton.getAttribute('data-descripcion');
    const precioCompra = boton.getAttribute('data-precio_compra');
    const precio = boton.getAttribute('data-precio');
    const stock = boton.getAttribute('data-stock');
    const imagenUrl = boton.getAttribute('data-imagen');

    // Cambiar título
    document.querySelector('#modalAgregarProducto .modal-title').innerHTML = '<span class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; font-size: 1.2rem; flex-shrink: 0;">✏️</span> EDITAR PRODUCTO';
    
    // Capturar el formulario del modal
    const form = document.getElementById('formProducto');
    form.action = `/inventario/actualizar/${id}`;

    // Llenar los campos ESPECÍFICAMENTE dentro del formulario del modal (Ignorando los del buscador)
    form.querySelector('[name="nombre"]').value = nombre;
    form.querySelector('[name="categoria_id"]').value = categoria;
    form.querySelector('[name="descripcion"]').value = descripcion;
    form.querySelector('[name="precio_compra"]').value = precioCompra;
    form.querySelector('[name="precio"]').value = precio;
    form.querySelector('[name="stock"]').value = stock;

    // Mostrar imagen si existe
    if (imagenUrl) {
        document.getElementById('previewImg').src = imagenUrl;
        document.getElementById('previewImg').classList.remove('d-none');
        document.getElementById('uploadPlaceholder').classList.add('d-none');
    } else {
        document.getElementById('previewImg').src = "";
        document.getElementById('previewImg').classList.add('d-none');
        document.getElementById('uploadPlaceholder').classList.remove('d-none');
    }
}

// Función para limpiar y preparar el modal para crear un NUEVO producto
window.prepararModalCrear = function() {
    // 1. Limpiar el formulario entero
    document.getElementById('formProducto').reset();
    
    // 2. Apuntar la ruta hacia la función de Guardar (Store)
    document.getElementById('formProducto').action = '/inventario/guardar';
    
    // 3. Poner el título original azul de "Crear"
    document.querySelector('#modalAgregarProducto .modal-title').innerHTML = '<span class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; font-size: 1.2rem; flex-shrink: 0;">📦</span> INFORMACIÓN DEL PRODUCTO';
    
    // 4. Quitar cualquier foto que haya quedado en la vista previa
    document.getElementById('previewImg').src = "";
    document.getElementById('previewImg').classList.add('d-none');
    document.getElementById('uploadPlaceholder').classList.remove('d-none');
}