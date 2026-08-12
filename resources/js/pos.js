document.addEventListener('DOMContentLoaded', function() {
    
    // 1. INICIALIZAR MEMORIA (LOCALSTORAGE)
    let carrito = JSON.parse(localStorage.getItem('carritoPOS')) || [];

    // 2. Lógica para Agregar al Carrito (DELEGACIÓN DE EVENTOS GLOBAL)
    // Esto asegura que aunque Livewire redibuje los productos, el clic siga funcionando
    document.addEventListener('click', function(e) {
        const boton = e.target.closest('.btn-agregar-producto');
        
        if (boton) {
            const id = parseInt(boton.getAttribute('data-id'));
            const nombre = boton.getAttribute('data-nombre');
            const precio = parseFloat(boton.getAttribute('data-precio'));
            const maxStock = parseInt(boton.getAttribute('data-stock'));

            const index = carrito.findIndex(p => p.id === id);

            if (index !== -1) {
                if (carrito[index].cantidad < maxStock) {
                    carrito[index].cantidad++;
                } else {
                    alert('¡No hay más stock disponible de este producto!');
                }
            } else {
                carrito.push({
                    id: id,
                    nombre: nombre,
                    precio: precio,
                    cantidad: 1,
                    maxStock: maxStock
                });
            }
            actualizarInterfaz();
        }
    });

    // 3. Función para sumar/restar/eliminar desde el carrito derecho
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

    // 4. Refrescar el HTML del carrito
    function actualizarInterfaz() {
        const lista = document.getElementById('listaCarrito');
        const vacio = document.getElementById('carritoVacio');
        const inputsOcultos = document.getElementById('inputsOcultos');
        const btnCobrar = document.getElementById('btnCobrar');
        
        // Verificación de seguridad por si Livewire oculta el DOM temporalmente
        if (!lista || !vacio || !inputsOcultos || !btnCobrar) return;

        lista.innerHTML = '';
        inputsOcultos.innerHTML = '';
        let total = 0;

        if (carrito.length === 0) {
            vacio.style.display = 'block';
            btnCobrar.disabled = true;
        } else {
            vacio.style.display = 'none';
            btnCobrar.disabled = false;

            carrito.forEach((prod, i) => {
                const subtotal = prod.precio * prod.cantidad;
                total += subtotal;

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

                inputsOcultos.innerHTML += `
                    <input type="hidden" name="productos[${i}][id]" value="${prod.id}">
                    <input type="hidden" name="productos[${i}][cantidad]" value="${prod.cantidad}">
                    <input type="hidden" name="productos[${i}][precio]" value="${prod.precio}">
                `;
            });
        }

        document.getElementById('subtotalDisplay').innerText = '$' + total.toFixed(2);
        document.getElementById('totalDisplay').innerText = '$' + total.toFixed(2);
        localStorage.setItem('carritoPOS', JSON.stringify(carrito));
    }

    // 5. LIMPIEZA POST-VENTA
    const formVenta = document.getElementById('formVenta');
    if (formVenta) {
        formVenta.addEventListener('submit', function() {
            localStorage.removeItem('carritoPOS');
        });
    }

    // 6. ARRANQUE DEL SISTEMA
    actualizarInterfaz();
});