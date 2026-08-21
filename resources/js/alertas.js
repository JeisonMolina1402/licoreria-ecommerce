document.addEventListener('DOMContentLoaded', function () {
    
    // 1. Buscamos el puente de datos en el HTML
    const alertasGlobales = document.getElementById('alertas-globales');
    
    if (alertasGlobales) {
        // 2. Extraemos los mensajes
        const mensajeSuccess = alertasGlobales.getAttribute('data-success');
        const mensajeError = alertasGlobales.getAttribute('data-error');
        const errorValidacion = alertasGlobales.getAttribute('data-validacion') === 'true' || alertasGlobales.getAttribute('data-validacion') === '1';

        // 3. Configuramos el diseño del Toast (Notificación)
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

        // 4. Disparamos la alerta que corresponda
        if (mensajeSuccess) {
            Toast.fire({ icon: 'success', title: mensajeSuccess });
        }

        if (mensajeError) {
            Toast.fire({ icon: 'error', title: mensajeError });
        } 
        else if (errorValidacion) {
            // Mensaje genérico para cuando falte llenar un campo obligatorio en cualquier formulario del sistema
            Toast.fire({ icon: 'warning', title: 'Por favor revisa los campos requeridos.' });
        }
    }
});
// ==========================================
        // 1. ALERTAS DE CONFIRMACIÓN (ELIMINAR/DESACTIVAR)
        // ==========================================
        const formulariosEliminar = document.querySelectorAll('.form-eliminar');
        
        formulariosEliminar.forEach(formulario => {
            formulario.addEventListener('submit', function (e) {
                e.preventDefault(); // Pausamos el envío del formulario
                
                Swal.fire({
                    title: '¿Estás completamente seguro?',
                    text: "Esta acción modificará el registro en la base de datos.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545', // Rojo para el peligro
                    cancelButtonColor: '#6c757d', // Gris para cancelar
                    confirmButtonText: '<i class="fas fa-check me-1"></i> Sí, proceder',
                    cancelButtonText: '<i class="fas fa-times me-1"></i> Cancelar',
                    reverseButtons: true // Pone el botón de cancelar a la izquierda (Mejor UX)
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit(); // Si dice que sí, el formulario continúa su viaje
                    }
                });
            });
        });

        // ==========================================
        // 2. ESTADOS DE CARGA (SPINNERS PARA EVITAR DOBLE CLIC)
        // ==========================================
        const formulariosCarga = document.querySelectorAll('.form-cargando');
        
        formulariosCarga.forEach(formulario => {
            formulario.addEventListener('submit', function () {
                // Buscamos el botón de tipo submit dentro de este formulario específico
                const btnSubmit = this.querySelector('button[type="submit"]');
                
                if (btnSubmit) {
                    // Deshabilitamos el botón para que no pueda dar más clics
                    btnSubmit.disabled = true;
                    // Le agregamos la ruedita giratoria de Bootstrap y cambiamos el texto
                    btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Procesando...';
                }
            });
        });