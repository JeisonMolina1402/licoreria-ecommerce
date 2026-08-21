@extends('layouts.tienda')

@section('titulo', 'Términos y Condiciones')

@section('content')
<div class="container py-5 mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 shadow-sm p-4 p-md-5 bg-white rounded-3">
            <h1 class="titulo-premium mb-4 text-center" style="color: var(--color_primario);">Términos y Condiciones de Uso</h1>
            <p class="text-center text-muted small mb-5">Última actualización: Agosto de 2026</p>
            <hr class="mb-5">

            <div class="text-muted" style="line-height: 1.8; text-align: justify;">
                
                <h5 class="fw-bold text-dark mt-4">1. Introducción y Aceptación</h5>
                <p>El presente documento establece los Términos y Condiciones Generales (en adelante, los "Términos") aplicables al uso de los servicios y la reserva de productos a través del sitio web de Licorería Web Store (en adelante, "la Plataforma"). Al acceder, registrarse o realizar una reserva, el Usuario acepta de manera expresa someterse a la totalidad de las cláusulas aquí descritas.</p>

                <h5 class="fw-bold text-dark mt-4">2. Modelo de Operación: Reservas y Retiro en Local</h5>
                <p>Licorería Web Store opera bajo un modelo exclusivo de <strong>Reserva en línea y Retiro en tienda física</strong>. <strong>No realizamos envíos ni entregas a domicilio</strong>. Una vez procesada la reserva a través de la Plataforma, el Usuario deberá acercarse a nuestro establecimiento físico ubicado en la ciudad de Quito para retirar su pedido dentro del horario de atención establecido.</p>

                <h5 class="fw-bold text-dark mt-4">3. Capacidad Legal y Restricción de Edad</h5>
                <p>En estricto cumplimiento con la legislación de la República del Ecuador, la comercialización de bebidas alcohólicas está <strong>estrictamente prohibida a menores de 18 años</strong>. El personal de caja está facultado y obligado a exigir el documento de identidad original (Cédula de Ciudadanía o Pasaporte) al momento del retiro en el local. Si el titular de la reserva es menor de edad, el pedido será cancelado de inmediato.</p>

                <h5 class="fw-bold text-dark mt-4">4. Registro y Seguridad de la Cuenta</h5>
                <p>Para procesar una reserva, el Usuario debe crear una cuenta aportando datos veraces. El Usuario es el único responsable de mantener la confidencialidad de su contraseña. La empresa se reserva el derecho de suspender cuentas que presenten actividad fraudulenta.</p>

                <h5 class="fw-bold text-dark mt-4">5. Precios, Impuestos y Disponibilidad</h5>
                <p>Todos los precios publicados incluyen los impuestos de ley vigentes (IVA, ICE). El catálogo es dinámico; si por un error de sincronización un producto reservado se agota físicamente en el local antes de su retiro, nuestro equipo se comunicará con el Usuario para ofrecerle un producto sustituto de igual valor o la anulación de la reserva sin recargo alguno.</p>

                <h5 class="fw-bold text-dark mt-4">6. Tiempos de Tolerancia para Retiros</h5>
                <p>Las reservas realizadas a través de la Plataforma garantizan la separación del stock por un tiempo limitado. Si el Usuario no se presenta a retirar y completar la transacción en el establecimiento físico en el tiempo estipulado tras la confirmación, la reserva será cancelada automáticamente y los productos regresarán al inventario general.</p>

                <h5 class="fw-bold text-dark mt-4">7. Políticas de Devolución y Cambios</h5>
                <p>Por razones de salubridad y garantía del producto, <strong>no se aceptan devoluciones de bebidas alcohólicas una vez que el cliente haya abandonado el local comercial</strong>. El Usuario tiene la obligación de revisar el estado de los sellos, etiquetas y botellas físicas en el mostrador junto con el vendedor antes de finalizar su compra y retirarse del establecimiento.</p>

                <h5 class="fw-bold text-dark mt-4">8. Propiedad Intelectual</h5>
                <p>Todo el contenido de la Plataforma (logotipos, imágenes, textos) es propiedad exclusiva de Licorería Web Store. Queda prohibida su reproducción o uso sin autorización previa.</p>

                <h5 class="fw-bold text-dark mt-4">9. Legislación Aplicable</h5>
                <p>Estos Términos se regirán por las leyes de la República del Ecuador. Cualquier controversia se someterá a los jueces y tribunales competentes de la ciudad de Quito.</p>
            </div>
        </div>
    </div>
</div>
@endsection