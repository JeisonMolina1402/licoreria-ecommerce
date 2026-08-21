document.addEventListener('DOMContentLoaded', function() {
    // Seleccionamos TODAS las campanas de la página (PC y Móvil)
    const campanas = document.querySelectorAll('.btn-campana-notificaciones');
    
    // Leemos las rutas inyectadas desde las etiquetas <meta> de Blade
    const metaLeer = document.querySelector('meta[name="ruta-leer-notificaciones"]');
    const metaCheck = document.querySelector('meta[name="ruta-check-notificaciones"]');
    const metaCsrf = document.querySelector('meta[name="csrf-token"]');

    if (campanas.length > 0 && metaLeer && metaCheck && metaCsrf) {
        const rutaLeer = metaLeer.content;
        const rutaCheck = metaCheck.content;
        const csrfToken = metaCsrf.content;

        // 1. EVENTO AL ABRIR EL MENÚ (Marcar como leídas)
        campanas.forEach(campana => {
            const dropdownElement = campana.closest('.dropdown');
            if (dropdownElement) {
                dropdownElement.addEventListener('shown.bs.dropdown', function() {
                    let badge = campana.querySelector('.bg-danger');
                    if (badge) {
                        badge.remove(); // Borra el globo visualmente
                        
                        // Petición silenciosa a la BD
                        fetch(rutaLeer, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        });
                    }
                });
            }
        });

        // 2. CONSULTA EN TIEMPO REAL (Cada 5 segundos)
        setInterval(() => {
            fetch(rutaCheck)
                .then(response => response.json())
                .then(data => {
                    let primerBadge = campanas[0].querySelector('.bg-danger');
                    let currentCount = primerBadge ? parseInt(primerBadge.innerText) : 0;

                    if (data.count > currentCount) {
                        // Alerta SweetAlert
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                toast: true, position: 'top-end', icon: 'info',
                                title: '¡Tienes una nueva notificación!',
                                showConfirmButton: false, timer: 4000
                            });
                        }

                        // Actualizamos TODAS las campanas simultáneamente
                        campanas.forEach(campana => {
                            let badge = campana.querySelector('.bg-danger');
                            if (badge) {
                                badge.innerText = data.count;
                            } else {
                                let iconContainer = campana.querySelector('.position-relative') || campana;
                                iconContainer.innerHTML += `<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.55rem;">${data.count}</span>`;
                            }

                            // Reconstruimos la lista con el historial real
                            let listaDropdown = campana.nextElementSibling;
                            let html = '<li><h6 class="dropdown-header fw-bold border-bottom pb-2">Notificaciones</h6></li>';

                            if (data.notificaciones.length > 0) {
                                data.notificaciones.forEach(notif => {
                                    let isUnread = notif.read_at === null;
                                    let bgClass = isUnread ? 'bg-light' : '';
                                    let textClass = isUnread ? 'text-dark' : 'text-muted';
                                    let url = notif.data.url ? notif.data.url : '#';
                                    let icono = notif.data.icono ? notif.data.icono : 'fa-solid fa-bell';
                                    let titulo = notif.data.titulo ? notif.data.titulo : 'Notificación';
                                    let mensaje = notif.data.mensaje ? notif.data.mensaje : '';

                                    html += `
                                        <li>
                                            <a class="dropdown-item d-flex align-items-start py-3 border-bottom ${bgClass}" href="${url}">
                                                <div class="me-3 mt-1"><i class="${icono} fs-5"></i></div>
                                                <div style="white-space: normal;">
                                                    <strong class="d-block mb-1 ${textClass}">${titulo}</strong>
                                                    <span class="small text-muted d-block">${mensaje}</span>
                                                    <small class="text-secondary" style="font-size: 0.7rem;">${notif.tiempo}</small>
                                                </div>
                                            </a>
                                        </li>
                                    `;
                                });
                            } else {
                                html += '<li><span class="dropdown-item text-center text-muted py-4 small">No tienes notificaciones nuevas</span></li>';
                            }
                            listaDropdown.innerHTML = html;
                        });
                    }
                });
        }, 5000); // 5 segundos
    }
});