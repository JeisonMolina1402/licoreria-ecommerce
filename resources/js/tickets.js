// resources/js/tickets.js

console.log('Script de recarga automática de tickets INICIADO.'); 

// Ejecutar cada 10 segundos (10000 milisegundos) para la demostración
setInterval(() => {
    // 1. Verificamos que no haya ningún Modal (recuadro negro) abierto
    if (!document.body.classList.contains('modal-open')) {
        
        console.log('Buscando actualizaciones...'); 
        
        // 2. Traemos la información más fresca de la base de datos de forma invisible
        fetch(window.location.href)
            .then(response => response.text())
            .then(html => {
                // 3. Convertimos el texto en código HTML estructurado
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // 4. Extraemos SOLO la tabla nueva usando la CLASE y reemplazamos la vieja
                const tablaNueva = doc.querySelector('.table-responsive');
                const tablaVieja = document.querySelector('.table-responsive');

                if (tablaNueva && tablaVieja) {
                    tablaVieja.innerHTML = tablaNueva.innerHTML;
                }
            })
            .catch(error => console.error('Error actualizando la tabla:', error));
    } else {
        console.log('Modal abierto, recarga pausada.'); 
    }
}, 10000); // 10000 ms = 10 segundos 