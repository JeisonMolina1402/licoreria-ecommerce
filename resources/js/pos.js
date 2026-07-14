// resources/js/pos.js

document.addEventListener('DOMContentLoaded', function() {
    let carrito = [];

    // 1. Lógica del Buscador en vivo
    const buscadorInput = document.getElementById('buscadorPOS');
    if (buscadorInput) {
        buscadorInput.addEventListener('input', function() {
            const texto = this.value.toLowerCase();
            const productos = document.querySelectorAll('.item-producto');
            
            productos.forEach(prod => {
                const nombre = prod.getAttribute('data-nombre');
                if (nombre.includes(texto)) {
                    prod.style.display = '';
                } else {
                    prod.style.display = 'none';
                }
            });
        });
    }

    // 2. Lógica para Agregar al Carrito (Capturando los clics en las tarjetas)
    const botonesAgregar = document.querySelectorAll('.btn-agregar-producto');
    botonesAgregar.forEach(boton => {
        boton.addEventListener('click', function() {
            // Extraemos los datos de la tarjeta que el usuario clickeó
            const id = parseInt(this.getAttribute('data-id'));
            const nombre = this.getAttribute('data-nombre');
            const precio = parseFloat(this.getAttribute('data-precio'));
            const maxStock = parseInt(this.getAttribute('data-stock'));

            // Buscamos si ya está en el carrito
            const index = carrito.findIndex(p => p.id === id);

            if (index !== -1) {
                // Si ya está, sumamos 1 sin pasarnos del stock
                if (carrito[index].cantidad < maxStock) {
                    carrito[index].cantidad++;
                } else {
                    alert('¡No hay más stock disponible de este producto!');
                }
            } else {
                // Si no está, lo agregamos nuevo
                carrito.push({
                    id: id,
                    nombre: nombre,
                    precio: precio,
                    cantidad: 1,
                    maxStock: maxStock
                });
            }
            actualizarInterfaz();
        });
    });

    // 3. Función para sumar/restar/eliminar desde el carrito derecho
    // La asignamos al window para que pueda ser llamada desde los botones HTML generados dinámicamente
    window.cambiarCantidadPOS = function(id, accion) {
        const index = carrito.findIndex(p => p.id === id);
        if (index !== -1) {
            if (accion === 'sumar' && carrito[index].cantidad < carrito[index].maxStock) {
                carrito[index].cantidad++;
            } else if (accion === 'restar') {
                carrito[index].cantidad--;
                if (carrito[index].cantidad <= 0) {
                    carrito.splice(index, 1);
                }
            } else if (accion === 'eliminar') {
                carrito.splice(index, 1);
            }
            actualizarInterfaz();
        }
    }

    // 4. Refrescar el HTML del carrito y preparar los inputs ocultos
    function actualizarInterfaz() {
        const lista = document.getElementById('listaCarrito');
        const vacio = document.getElementById('carritoVacio');
        const inputsOcultos = document.getElementById('inputsOcultos');
        const btnCobrar = document.getElementById('btnCobrar');
        
        lista.innerHTML = '';
        inputsOcultos.innerHTML = '';
        let total = 0;

        if (carrito.length === 0) {
            vacio.style.display = 'block';
            btnCobrar.disabled = true; // Se deshabilita si está vacío
        } else {
            vacio.style.display = 'none';
            btnCobrar.disabled = false; // Se habilita si hay productos

            carrito.forEach((prod, i) => {
                const subtotal = prod.precio * prod.cantidad;
                total += subtotal;

                // Dibujar el item
                lista.innerHTML += `
                    <li class="list-group-item px-0 py-2 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold small text-truncate" style="max-width: 180px;">${prod.nombre}</span>
                            <span class="text-danger small" style="cursor: pointer;" onclick="cambiarCantidadPOS(${prod.id}, 'eliminar')">❌</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-secondary" onclick="cambiarCantidadPOS(${prod.id}, 'restar')">-</button>
                                <button type="button" class="btn btn-light disabled text-dark fw-bold px-3">${prod.cantidad}</button>
                                <button type="button" class="btn btn-outline-secondary" onclick="cambiarCantidadPOS(${prod.id}, 'sumar')">+</button>
                            </div>
                            <span class="fw-bold">$${subtotal.toFixed(2)}</span>
                        </div>
                    </li>
                `;

                // Preparar inputs ocultos (ESTO ES LO QUE SE ENVÍA AL CONTROLADOR)
                inputsOcultos.innerHTML += `
                    <input type="hidden" name="productos[${i}][id]" value="${prod.id}">
                    <input type="hidden" name="productos[${i}][cantidad]" value="${prod.cantidad}">
                    <input type="hidden" name="productos[${i}][precio]" value="${prod.precio}">
                `;
            });
        }

        // Actualizar totales
        document.getElementById('subtotalDisplay').innerText = '$' + total.toFixed(2);
        document.getElementById('totalDisplay').innerText = '$' + total.toFixed(2);
    }
});