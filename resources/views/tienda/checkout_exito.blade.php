@extends('layouts.tienda')

@section('titulo', 'Reserva Exitosa')

@section('content')
<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8 text-center">
            
            <i class="fa-solid fa-circle-check text-success mb-3" style="font-size: 4rem;"></i>
            <h2 class="titulo-premium mb-2" style="font-family: 'Cinzel', serif;">¡Reserva Exitosa!</h2>
            <p class="text-muted mb-4" style="font-family: 'Lora', serif;">
                Recuerda que tienes <strong>10 minutos</strong> para realizar la transferencia y asegurar tus botellas.
            </p>

            <div id="voucher-descargable" class="card border-0 shadow-lg mb-4 text-start rounded-4 overflow-hidden" style="background-color: #fff; position: relative;">
                
                <div style="height: 8px; background-color: var(--color_primario); width: 100%;"></div>
                
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <h4 class="fw-bold mb-0" style="font-family: 'Cinzel', serif; color: var(--color_secundario);">LICORERÍA</h4>
                        <span class="text-muted small" style="letter-spacing: 2px;">WEB STORE</span>
                    </div>

                    <div class="border-bottom pb-3 mb-3 border-dashed">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small text-uppercase fw-bold">Código de Reserva</span>
                            <span class="badge fs-6" style="background-color: var(--color_secundario); color: var(--color_primario);">{{ $ticket->codigo }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small text-uppercase fw-bold">Fecha</span>
                            <span class="text-dark fw-bold">{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>

                    <h6 class="fw-bold text-uppercase mb-3" style="color: var(--color_secundario); font-size: 0.9rem;">Detalle de Productos</h6>
                    
                    <ul class="list-group list-group-flush mb-4">
                        @foreach($productos as $item)
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center border-0 py-1 bg-transparent">
                                <div>
                                    <span class="fw-bold text-dark">{{ $item['cantidad'] }}x</span> 
                                    <span class="text-muted ms-1">{{ $item['nombre'] }}</span>
                                </div>
                                <span class="fw-bold">${{ number_format($item['precio'] * $item['cantidad'], 2) }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="d-flex justify-content-between align-items-center border-top pt-3 mb-4">
                        <span class="fw-bold text-uppercase fs-5" style="color: var(--color_secundario);">Total a Pagar</span>
                        <span class="fw-bold fs-3" style="color: var(--color_primario);">${{ number_format($ticket->total, 2) }}</span>
                    </div>

                    <div class="bg-light p-3 rounded-3 border">
                        <h6 class="fw-bold mb-2 text-center text-uppercase" style="font-size: 0.85rem; color: var(--color_secundario);">Datos para Transferencia</h6>
                        <div class="small text-muted text-center" style="font-family: monospace; font-size: 0.95rem;">
                            <strong>Banco Pichincha</strong><br>
                            Cuenta Ahorros: 2200113344<br>
                            A nombre de: Licorería Web Store<br>
                            CI: 1700000000
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                <button id="btn-descargar" class="btn btn-premium px-4 py-3 rounded-pill fw-bold text-uppercase shadow-sm">
                    <i class="fa-solid fa-download me-2"></i> Descargar Comprobante
                </button>
                <a href="{{ route('tienda.index') }}" class="btn btn-outline-dark px-4 py-3 rounded-pill fw-bold text-uppercase">
                    Volver a la Tienda
                </a>
            </div>

        </div>
    </div>
</div>

<style>
    .border-dashed { border-bottom: 2px dashed #dee2e6 !important; }
</style>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. VACIAR EL CARRITO AL LLEGAR A ESTA PANTALLA
    // Laravel envía esta variable de sesión cuando el ticket se crea con éxito
    @if(session('limpiar_carrito'))
        localStorage.removeItem('carritoLicores');
    @endif

    // 2. LÓGICA PARA TOMAR LA "FOTO" Y DESCARGAR EL TICKET
    const btnDescargar = document.getElementById('btn-descargar');
    const voucher = document.getElementById('voucher-descargable');

    btnDescargar.addEventListener('click', function() {
        // Cambiamos el texto del botón para que el usuario sepa que está procesando
        const textoOriginal = btnDescargar.innerHTML;
        btnDescargar.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Generando imagen...';
        btnDescargar.classList.add('disabled');

        // html2canvas toma el elemento HTML y lo convierte en un <canvas>
        html2canvas(voucher, {
            scale: 2, // Mayor calidad (retina)
            backgroundColor: "#ffffff",
            logging: false,
            useCORS: true // Permite renderizar fuentes/imágenes externas si las hay
        }).then(canvas => {
            // Convertimos el canvas a una URL de imagen base64
            const imagenURL = canvas.toDataURL("image/png");

            // Creamos un enlace invisible, le ponemos la URL y forzamos el clic para descargar
            const enlaceDescarga = document.createElement('a');
            enlaceDescarga.href = imagenURL;
            enlaceDescarga.download = 'Comprobante_{{ $ticket->codigo }}.png'; // Nombre del archivo
            document.body.appendChild(enlaceDescarga);
            enlaceDescarga.click();
            document.body.removeChild(enlaceDescarga);

            // Restauramos el botón
            btnDescargar.innerHTML = textoOriginal;
            btnDescargar.classList.remove('disabled');
        }).catch(err => {
            console.error("Error al generar la imagen:", err);
            alert("Hubo un problema al generar tu comprobante. Por favor, toma una captura de pantalla.");
            btnDescargar.innerHTML = textoOriginal;
            btnDescargar.classList.remove('disabled');
        });
    });
});
</script>
@endsection