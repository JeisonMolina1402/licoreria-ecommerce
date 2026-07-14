window.addEventListener('load', function() {
    console.log("🚀 Archivo exito.js ejecutándose...");

    // 1. FORZAR VACIADO DEL CARRITO
    localStorage.removeItem('carritoLicores');
    const contador = document.getElementById('contador-carrito');
    if(contador) {
        contador.innerText = '0';
        console.log("✅ Carrito vaciado en la memoria.");
    }

    // 2. CONFIGURAR EL BOTÓN DE DESCARGA
    const btnDescargar = document.getElementById('btn-descargar');
    
    if (!btnDescargar) {
        console.error("❌ ERROR: No se encontró el botón con ID 'btn-descargar'.");
        return; // Detenemos todo si no hay botón
    }

    console.log("✅ Botón de descarga detectado y listo.");

    btnDescargar.addEventListener('click', function(e) {
        e.preventDefault();
        console.log("📸 Clic detectado. Iniciando captura...");
        
        const textoOriginal = this.innerHTML;
        this.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Generando...';
        this.classList.add('disabled');
        
        const elemento = document.getElementById('ticket-comprobante');
        const codigoReserva = this.getAttribute('data-codigo'); 
        
        // Verificamos que la librería fotográfica exista
        if (typeof html2canvas === 'undefined') {
            console.error("❌ ERROR: La librería html2canvas no se cargó.");
            alert("Error: No se pudo cargar el motor de imágenes.");
            this.innerHTML = textoOriginal;
            this.classList.remove('disabled');
            return;
        }

        // Tomar la foto
        html2canvas(elemento, {
            scale: 2,
            backgroundColor: "#ffffff",
            useCORS: true
        }).then(canvas => {
            console.log("✅ Imagen generada con éxito. Descargando...");
            let enlace = document.createElement('a');
            enlace.download = `Reserva_${codigoReserva}.png`;
            enlace.href = canvas.toDataURL("image/png");
            enlace.click();

            this.innerHTML = textoOriginal;
            this.classList.remove('disabled');
        }).catch(error => {
            console.error("❌ Error grave al generar la imagen:", error);
            this.innerHTML = textoOriginal;
            this.classList.remove('disabled');
            alert("Hubo un problema interno al generar el ticket.");
        });
    });
});