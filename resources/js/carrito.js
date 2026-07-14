document.addEventListener('DOMContentLoaded', () => {
    // 1. Inicializar el carrito (busca en memoria local o crea un arreglo vacío)
    let carrito = JSON.parse(localStorage.getItem('carritoLicores')) || [];

    // 2. Referencias a la interfaz (Panel Offcanvas)
    const contenedorCarrito = document.getElementById('contenedor-productos-carrito');
    const totalCarrito = document.getElementById('total-carrito');
    const badgeCarrito = document.getElementById('contador-carrito'); 
    const btnProcesar = document.getElementById('btn-procesar-pago');

    // 3. Función Principal: Dibuja los productos en el panel
    const renderizarCarrito = () => {
        contenedorCarrito.innerHTML = ''; // Limpiamos el contenedor

        // ESTADO VACÍO
        if (carrito.length === 0) {
            contenedorCarrito.innerHTML = `
                <div class="text-center text-muted mt-5">
                    <i class="fa-solid fa-cart-arrow-down mb-3" style="font-size: 3rem; opacity: 0.2;"></i>
                    <h6 class="fw-bold" style="font-family: 'Cinzel', serif;">Tu carrito está vacío</h6>
                    <p class="small">¡Anímate a agregar algunas botellas!</p>
                </div>
            `;
            totalCarrito.textContent = '$0.00';
            if(badgeCarrito) badgeCarrito.textContent = '0';
            
            // Si el botón ahora es un <button> dentro del form, lo deshabilitamos
            if(btnProcesar) {
                btnProcesar.classList.add('disabled');
                btnProcesar.disabled = true;
            }
            return;
        }

        // SI HAY PRODUCTOS
        if(btnProcesar) {
            btnProcesar.classList.remove('disabled');
            btnProcesar.disabled = false;
        }
        let total = 0;
        let cantidadTotal = 0;

        carrito.forEach((producto) => {
            let subtotal = producto.precio * producto.cantidad;
            total += subtotal;
            cantidadTotal += producto.cantidad;

            // Inyectamos el HTML de cada producto en el panel
            contenedorCarrito.innerHTML += `
                <div class="d-flex align-items-center mb-3 border-bottom pb-3">
                    <img src="${producto.imagen}" alt="${producto.nombre}" style="width: 60px; height: 60px; object-fit: contain;" class="bg-light rounded border me-3">
                    <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">${producto.nombre}</h6>
                        <span class="text-muted small">$${parseFloat(producto.precio).toFixed(2)} c/u</span>
                        
                        <div class="d-flex align-items-center mt-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 btn-restar" data-id="${producto.id}">-</button>
                            <span class="mx-2 small fw-bold text-dark">${producto.cantidad}</span>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 btn-sumar" data-id="${producto.id}">+</button>
                        </div>
                    </div>
                    <div class="text-end ms-2">
                        <div class="fw-bold mb-2" style="color: var(--color_primario);">$${subtotal.toFixed(2)}</div>
                        <button type="button" class="btn btn-sm text-danger p-0 btn-eliminar" data-id="${producto.id}">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        });

        // Actualizamos totales y contador rojo
        totalCarrito.textContent = `$${total.toFixed(2)}`;
        if(badgeCarrito) badgeCarrito.textContent = cantidadTotal;

        // Guardamos en la memoria del navegador
        localStorage.setItem('carritoLicores', JSON.stringify(carrito));
    };

    // 4. Capturar los Clics dinámicos en toda la página
    document.addEventListener('click', (e) => {
        
        // A) AGREGAR AL CARRITO (Desde el Catálogo)
        const btnAgregar = e.target.closest('.btn-agregar');
        if (btnAgregar) {
            e.preventDefault();
            const id = btnAgregar.getAttribute('data-id');
            const nombre = btnAgregar.getAttribute('data-nombre');
            const precio = parseFloat(btnAgregar.getAttribute('data-precio'));
            const imagen = btnAgregar.getAttribute('data-imagen');

            const existe = carrito.find(item => item.id === id);
            if (existe) {
                existe.cantidad++;
            } else {
                carrito.push({ id, nombre, precio, imagen, cantidad: 1 });
            }
            renderizarCarrito();
            
            // Abrir el panel lateral automáticamente
            const carritoOffcanvas = new bootstrap.Offcanvas(document.getElementById('carritoOffcanvas'));
            carritoOffcanvas.show();
        }

        // B) SUMAR CANTIDAD (Dentro del panel)
        if (e.target.classList.contains('btn-sumar')) {
            e.preventDefault();
            const id = e.target.getAttribute('data-id');
            const producto = carrito.find(item => item.id === id);
            if (producto) producto.cantidad++;
            renderizarCarrito();
        }

        // C) RESTAR CANTIDAD (Dentro del panel)
        if (e.target.classList.contains('btn-restar')) {
            e.preventDefault();
            const id = e.target.getAttribute('data-id');
            const producto = carrito.find(item => item.id === id);
            if (producto && producto.cantidad > 1) {
                producto.cantidad--;
            } else if (producto && producto.cantidad === 1) {
                carrito = carrito.filter(item => item.id !== id);
            }
            renderizarCarrito();
        }

        // D) ELIMINAR PRODUCTO (Dentro del panel)
        const btnEliminar = e.target.closest('.btn-eliminar');
        if (btnEliminar) {
            e.preventDefault();
            const id = btnEliminar.getAttribute('data-id');
            carrito = carrito.filter(item => item.id !== id);
            renderizarCarrito();
        }
    });

    // 5. Enviar el carrito a Laravel al hacer clic en Procesar Reserva
    const formCheckout = document.getElementById('form-checkout');
    if (formCheckout) {
        formCheckout.addEventListener('submit', (e) => {
            // Verificamos si hay productos por seguridad
            if (carrito.length === 0) {
                e.preventDefault();
                alert('Tu carrito está vacío');
                return;
            }
            // Convertimos el carrito a texto (JSON) y lo metemos en el input oculto
            document.getElementById('carrito_datos').value = JSON.stringify(carrito);
            
            // Nota: Aquí no borramos el localStorage todavía. 
            // Lo borraremos desde Laravel cuando el ticket se haya guardado con éxito en la base de datos.
        });
    }

    // 6. Dibujar el carrito apenas cargue la página
    renderizarCarrito();
});