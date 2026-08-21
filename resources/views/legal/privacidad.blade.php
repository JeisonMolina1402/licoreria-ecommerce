@extends('layouts.tienda')

@section('titulo', 'Políticas de Privacidad')

@section('content')
<div class="container py-5 mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 shadow-sm p-4 p-md-5 bg-white rounded-3">
            <h1 class="titulo-premium mb-4 text-center" style="color: var(--color_primario);">Políticas de Privacidad y Tratamiento de Datos</h1>
            <p class="text-center text-muted small mb-5">Última actualización: Agosto de 2026</p>
            <hr class="mb-5">

            <div class="text-muted" style="line-height: 1.8; text-align: justify;">
                
                <h5 class="fw-bold text-dark mt-4">1. Responsabilidad del Tratamiento</h5>
                <p>En estricto apego a la Ley Orgánica de Protección de Datos Personales (LOPDP) del Ecuador, Licorería Web Store garantiza la confidencialidad y seguridad en el tratamiento de los datos personales proporcionados para la gestión de reservas en nuestra plataforma web.</p>

                <h5 class="fw-bold text-dark mt-4">2. Información Recopilada</h5>
                <p>Al operar bajo un modelo de <strong>reserva y retiro en local</strong>, minimizamos la recolección de datos. Únicamente solicitamos:</p>
                <ul>
                    <li><strong>Datos de Identificación:</strong> Nombre, apellidos y número de Cédula de Ciudadanía (necesario para facturación física y verificación de mayoría de edad).</li>
                    <li><strong>Datos de Contacto:</strong> Correo electrónico y número de teléfono o WhatsApp (para confirmación de reservas).</li>
                    <li><strong>Datos Técnicos:</strong> Datos de sesión segura mediante cookies temporales.</li>
                </ul>
                <p><em>Nota: No solicitamos ni almacenamos direcciones domiciliarias, ya que no ofrecemos servicio de entrega. Tampoco almacenamos datos de tarjetas de crédito.</em></p>

                <h5 class="fw-bold text-dark mt-4">3. Finalidad de los Datos</h5>
                <p>Sus datos serán utilizados exclusivamente para: separar el inventario correspondiente a su reserva, agilizar su proceso de facturación al momento del retiro en el local, notificarle sobre el estado de su pedido vía WhatsApp o correo, y (si lo autoriza) enviarle promociones de nuestro catálogo.</p>

                <h5 class="fw-bold text-dark mt-4">4. Privacidad y No Comercialización</h5>
                <p>Licorería Web Store <strong>no vende, alquila ni comparte</strong> sus datos personales con agencias de marketing, empresas de logística ni terceros ajenos a la operación. Sus datos solo son compartidos, cuando es estrictamente necesario, con el sistema de facturación electrónica del SRI para cumplir con las normativas tributarias ecuatorianas.</p>

                <h5 class="fw-bold text-dark mt-4">5. Seguridad de la Información</h5>
                <p>Implementamos medidas técnicas (como contraseñas encriptadas) para evitar el acceso no autorizado a su perfil. Sus datos de reserva se conservarán únicamente el tiempo necesario para la transacción o lo que la ley tributaria exija mantener como registro de facturación.</p>

                <h5 class="fw-bold text-dark mt-4">6. Derechos del Titular</h5>
                <p>Como cliente, usted tiene derecho a acceder a sus datos, rectificarlos desde su panel de perfil, solicitar la eliminación de su cuenta, u oponerse a recibir publicidad en cualquier momento, conforme lo dicta la LOPDP.</p>

                <h5 class="fw-bold text-dark mt-4">7. Uso de Cookies</h5>
                <p>Utilizamos cookies esenciales únicamente para mantener su sesión activa de forma segura y guardar los productos en su carrito virtual mientras navega por nuestro catálogo.</p>
            </div>
        </div>
    </div>
</div>
@endsection