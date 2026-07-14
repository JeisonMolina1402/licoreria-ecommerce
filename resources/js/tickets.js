// resources/js/tickets.js

// Quitamos el DOMContentLoaded porque Vite ya lo carga de forma segura al final

console.log('Script de recarga automática de tickets INICIADO.'); // Un chismoso para la consola

// Ejecutar cada 5 segundos (5000 milisegundos) PARA PRUEBAS
setInterval(() => {
    // 1. Verificamos que no haya ningún Modal (recuadro negro) abierto
    if (!document.body.classList.contains('modal-open')) {
        
        console.log('Buscando actualizaciones...'); // Chismoso 2
        
        // 2. Traemos la información más fresca de la base de datos de forma invisible
        fetch(window.location.href)
            .then(response => response.text())
            .then(html => {
                // 3. Convertimos el texto en código HTML estructurado
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // 4. Extraemos SOLO la tabla nueva y reemplazamos la vieja
                const tablaNueva = doc.querySelector('.table-responsive');
                const tablaVieja = document.querySelector('.table-responsive');

                if (tablaNueva && tablaVieja) {
                    tablaVieja.innerHTML = tablaNueva.innerHTML;
                }
            })
            .catch(error => console.error('Error actualizando la tabla:', error));
    } else {
        console.log('Modal abierto, recarga pausada.'); // Chismoso 3
    }
}, 30000); //aqui colocamos el tiempo en milisegundos 