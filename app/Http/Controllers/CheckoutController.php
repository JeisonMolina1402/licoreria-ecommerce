<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Ticket; // <-- Ajusta esto si tu modelo se llama Venta o Pedido

class CheckoutController extends Controller
{
    public function procesar(Request $request)
    {
        // 1. Recibir los datos del carrito oculto en el formulario
        $carritoJson = $request->input('carrito_datos');
        $carrito = json_decode($carritoJson, true);

        // Si por algún motivo llega vacío, lo regresamos a la tienda
        if (!$carrito || count($carrito) == 0) {
            return redirect()->route('tienda.index')->with('error', 'Tu carrito está vacío.');
        }

        // 2. Calcular el total a pagar en el servidor (Seguridad Backend)
        $total = 0;
        foreach ($carrito as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }

        // 3. Generar un Código de Compra Único (Ej: LCR-A4F9K)
        $codigoCompra = 'LCR-' . strtoupper(Str::random(5));

        // 4. Guardar el Ticket en la Base de Datos
        // (Ajusta los nombres de las columnas a como los tengas en tu base de datos)
        $ticket = new Ticket();
        $ticket->user_id = Auth::id(); // Relacionamos la compra con el cliente logueado
        $ticket->codigo = $codigoCompra;
        $ticket->total = $total;
        $ticket->estado = 'Pendiente de Pago'; 
        
        // Guardamos todo el JSON de productos directamente en la base de datos 
        // para no tener que crear otra tabla extra de "detalles_ticket" si no quieres.
        $ticket->detalle_productos = $carritoJson; 
        
        $ticket->save();

        // 5. Redirigir a la pantalla del Comprobante Digital (que crearemos en el siguiente paso)
        // Usamos "with" para enviarle a la vista los datos recién guardados.
        return redirect()->route('tienda.exito', $ticket->id)->with([
            'mensaje' => '¡Reserva generada con éxito!',
            'limpiar_carrito' => true // Esta bandera le dirá a JS que vacíe el carrito
        ]);
    }

    public function exito($id)
    {
        // Buscamos el ticket recién creado
        $ticket = Ticket::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        
        // Convertimos el JSON de productos nuevamente a arreglo para poder dibujarlo en la vista
        $productos = json_decode($ticket->detalle_productos, true);

        return view('tienda.checkout_exito', compact('ticket', 'productos'));
    }
}