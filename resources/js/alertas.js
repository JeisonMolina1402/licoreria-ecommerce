document.addEventListener('DOMContentLoaded', function () {
    
    // 1. ALERTAS GLOBALES (ÉXITO / ERROR)
    const alertasGlobales = document.getElementById('alertas-globales');
    if (alertasGlobales) {
        const mensajeSuccess = alertasGlobales.getAttribute('data-success');
        const mensajeError = alertasGlobales.getAttribute('data-error');
        const errorValidacion = alertasGlobales.getAttribute('data-validacion') === 'true' || alertasGlobales.getAttribute('data-validacion') === '1';

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        if (mensajeSuccess) {
            Toast.fire({ icon: 'success', title: mensajeSuccess });
        }
        if (mensajeError) {
            Toast.fire({ icon: 'error', title: mensajeError });
        } 
        else if (errorValidacion) {
            Toast.fire({ icon: 'warning', title: 'Por favor revisa los campos requeridos.' });
        }
    }

    // 2. ESTADOS DE CARGA (SPINNERS PARA EVITAR DOBLE CLIC)
    const formulariosCarga = document.querySelectorAll('.form-cargando');
    formulariosCarga.forEach(formulario => {
        formulario.addEventListener('submit', function () {
            const btnSubmit = this.querySelector('button[type="submit"]');
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Procesando...';
            }
        });
    });

    // 3. ALERTA AMARILLA: CAMBIAR ESTADO (PRODUCTOS Y USUARIOS)
    const formulariosEstado = document.querySelectorAll('.form-estado');
    formulariosEstado.forEach(formulario => {
        formulario.addEventListener('submit', function (e) {
            e.preventDefault(); 
            
            // Texto dinámico dependiendo si estamos en Usuarios o Inventario
            const esUsuario = window.location.pathname.includes('usuarios');
            const textoMensaje = esUsuario 
                                 ? "El usuario perderá o recuperará su acceso al sistema." 
                                 : "El producto se ocultará o mostrará en la tienda pública.";

            Swal.fire({
                title: '¿Estás seguro?',
                text: textoMensaje,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107', 
                cancelButtonColor: '#6c757d', 
                confirmButtonText: '<i class="fa-solid fa-check me-1"></i> Sí, continuar',
                cancelButtonText: '<i class="fa-solid fa-times me-1"></i> Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit(); 
                }
            });
        });
    });

    // 4. ALERTA ROJA: ELIMINAR DEFINITIVAMENTE (ESTA FALTABA)
    const formulariosEliminar = document.querySelectorAll('.form-eliminar');
    formulariosEliminar.forEach(formulario => {
        formulario.addEventListener('submit', function (e) {
            e.preventDefault(); 
            
            Swal.fire({
                title: '¿Estás seguro de eliminar?',
                text: "¡Esta acción no se puede deshacer y los datos se perderán para siempre!",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#dc3545', // Rojo peligro
                cancelButtonColor: '#6c757d', // Gris
                confirmButtonText: '<i class="fa-solid fa-trash-can me-1"></i> Sí, eliminar',
                cancelButtonText: '<i class="fa-solid fa-times me-1"></i> Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit(); 
                }
            });
        });
    });

});