<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reserva Confirmada</title>
</head>
<body style="background-color: #f4f6f9; font-family: Arial, sans-serif; padding: 20px; margin: 0;">

    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="background-color: #1a1a1a; padding: 30px; text-align: center;">
            <h1 style="color: #d1b48c; margin: 0; font-size: 24px; letter-spacing: 2px;">LICORERÍA WEB STORE</h1>
            <p style="color: #ffffff; margin: 5px 0 0 0; font-size: 14px;">¡Reserva Confirmada!</p>
        </div>

        <!-- Body -->
        <div style="padding: 30px;">
            <p style="color: #333333; font-size: 16px;">Hola <strong>{{ $ticket->user->name ?? 'Cliente' }}</strong>,</p>
            <p style="color: #555555; font-size: 15px; line-height: 1.5;">Tu pedido ha sido registrado correctamente. Recuerda que tienes <strong>10 minutos</strong> para enviar el comprobante de transferencia y asegurar tu compra.</p>

            <!-- Detalles del Ticket -->
            <div style="background-color: #fff9e6; border: 1px solid #f39c12; border-radius: 6px; padding: 20px; margin-top: 20px;">
                <h3 style="color: #b07d00; margin-top: 0;">Código de Retiro: {{ $ticket->codigo_reserva }}</h3>
                
                <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                    @foreach($ticket->detalles as $detalle)
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eeeeee; color: #333;">{{ $detalle->cantidad }}x {{ $detalle->producto->nombre }}</td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eeeeee; text-align: right; color: #333;">${{ number_format($detalle->precio_unitario, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td style="padding: 15px 0 0 0; font-weight: bold; font-size: 18px; color: #1a1a1a;">TOTAL A PAGAR</td>
                        <td style="padding: 15px 0 0 0; font-weight: bold; font-size: 18px; text-align: right; color: #d1b48c;">${{ number_format($ticket->total, 2) }}</td>
                    </tr>
                </table>
            </div>

            <!-- Datos Bancarios -->
            <div style="background-color: #fff9e6; border: 1px solid #f39c12; border-left: 5px solid #f39c12; border-radius: 6px; padding: 20px; margin-top: 20px;">
                <h3 style="color: #b07d00; margin-top: 0; font-size: 16px;">Datos para transferencia directa:</h3>
                <ul style="list-style: none; padding: 0; margin: 0; color: #333333; font-size: 14px; line-height: 1.6;">
                    <li><strong>Banco:</strong> Pichincha</li>
                    <li><strong>Tipo:</strong> Cuenta de Ahorros</li>
                    <li><strong>Número:</strong> 2200113344</li>
                    <li><strong>Titular:</strong> Licorería Web Store</li>
                    <li><strong>Cédula/RUC:</strong> 1700000000001</li>
                </ul>
            </div>

            <!-- Instrucciones de Pago -->
            <div style="margin-top: 30px; text-align: center;">
                <p style="color: #777777; font-size: 14px;">Envía tu comprobante de pago a nuestro WhatsApp para validar la orden:</p>
                <a href="https://wa.me/593981766228?text=Hola,%20adjunto%20comprobante%20del%20pedido%20*{{ $ticket->codigo_reserva }}*" 
                   style="display: inline-block; background-color: #25D366; color: white; padding: 12px 25px; text-decoration: none; border-radius: 50px; font-weight: bold; margin-top: 10px;">
                   Enviar Comprobante WhatsApp
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div style="background-color: #f4f6f9; padding: 20px; text-align: center; border-top: 1px solid #eeeeee;">
            <p style="color: #999999; font-size: 12px; margin: 0;">Presenta este correo o tu código al momento de retirar en el local.</p>
        </div>
    </div>

</body>
</html>