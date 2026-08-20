// Función para mostrar la vista previa de la imagen al subirla
window.mostrarVistaPrevia = function (event) {
    const input = event.target;
    const reader = new FileReader();
    reader.onload = function () {
        document.getElementById("previewImg").src = reader.result;
        document.getElementById("previewImg").classList.remove("d-none");
        document.getElementById("uploadPlaceholder").classList.add("d-none");
    };
    if (input.files && input.files[0]) reader.readAsDataURL(input.files[0]);
};

// Función para confirmar la cancelación usando SweetAlert2
window.confirmarCancelacion = function () {
    Swal.fire({
        title: "¿Cancelar edición?",
        text: "Los datos que no hayas guardado se perderán.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Sí, cancelar",
        cancelButtonText: "Volver al formulario",
    }).then((result) => {
        if (result.isConfirmed) {
            // 1. CERRAR EL MODAL (Hacemos clic en el botón fantasma)
            document.getElementById("btnCerrarModalFantasma").click();

            // 2. LIMPIAR EL FORMULARIO Y RUTAS
            const form = document.getElementById("formProducto");
            form.reset();
            form.action = "/inventario/guardar";

            // 3. QUITAR BORDES ROJOS DE ERRORES DE LARAVEL
            form.querySelectorAll(".is-invalid").forEach((el) =>
                el.classList.remove("is-invalid"),
            );

            // ELIMINA FÍSICAMENTE LOS TEXTOS DE ERROR DEL HTML
            form.querySelectorAll(".texto-error").forEach((el) => el.remove());

            // 4. RESTAURAR EL TÍTULO
            document.querySelector(
                "#modalAgregarProducto .modal-title",
            ).innerHTML =
                '<h5 class="modal-title fw-bold text-dark">📦 INFORMACIÓN DEL PRODUCTO</h5>';

            // 5. LIMPIAR LA IMAGEN
            document.getElementById("previewImg").src = "";
            document.getElementById("previewImg").classList.add("d-none");
            document
                .getElementById("uploadPlaceholder")
                .classList.remove("d-none");
        }
    });
};

// Función para llenar el modal con los datos del producto a editar
window.prepararModalEditar = function (boton) {
    // Extraer datos del botón
    const id = boton.getAttribute("data-id");
    const nombre = boton.getAttribute("data-nombre");
    const categoria = boton.getAttribute("data-categoria");
    const descripcion = boton.getAttribute("data-descripcion");
    const precioCompra = boton.getAttribute("data-precio_compra");
    const precio = boton.getAttribute("data-precio");
    const stock = boton.getAttribute("data-stock");
    const imagenUrl = boton.getAttribute("data-imagen");

    // Cambiar título
    document.querySelector("#modalAgregarProducto .modal-title").innerHTML =
        '<span class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; font-size: 1.2rem; flex-shrink: 0;">✏️</span> EDITAR PRODUCTO';

    // Capturar el formulario del modal
    const form = document.getElementById("formProducto");
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
        document.getElementById("previewImg").src = imagenUrl;
        document.getElementById("previewImg").classList.remove("d-none");
        document.getElementById("uploadPlaceholder").classList.add("d-none");
    } else {
        document.getElementById("previewImg").src = "";
        document.getElementById("previewImg").classList.add("d-none");
        document.getElementById("uploadPlaceholder").classList.remove("d-none");
    }
};

// Función para limpiar y preparar el modal para crear un NUEVO producto
window.prepararModalCrear = function () {
    const form = document.getElementById("formProducto");
    form.reset();
    form.action = "/inventario/guardar";

    // 🔥 NUEVO: Quitar los bordes rojos de error de Laravel por si quedaron pegados
    form.querySelectorAll(".is-invalid").forEach((el) =>
        el.classList.remove("is-invalid"),
    );

    // ELIMINA FÍSICAMENTE LOS TEXTOS DE ERROR DEL HTML
    form.querySelectorAll(".texto-error").forEach((el) => el.remove());

    document.querySelector("#modalAgregarProducto .modal-title").innerHTML =
        '<h5 class="modal-title fw-bold text-dark">📦 INFORMACIÓN DEL PRODUCTO</h5>';

    document.getElementById("previewImg").src = "";
    document.getElementById("previewImg").classList.add("d-none");
    document.getElementById("uploadPlaceholder").classList.remove("d-none");
};
