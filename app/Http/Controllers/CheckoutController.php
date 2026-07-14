<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\DetalleTicket;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function procesar(Request $request)
    {
        // 1. Decodificar el JSON de los productos que viene del carrito
        $carrito = json_decode($request->carrito_datos, true);

        // Si por alguna razón llega vacío, lo regresamos
        if (!$carrito || count($carrito) == 0) {
            return redirect()->back()->withErrors(['error' => 'El carrito está vacío.']);
        }

        // 2. Calcular el total real sumando las cantidades
        $total = 0;
        foreach ($carrito as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }

        // 3. Crear el Ticket principal (Cabecera)
        $ticket = Ticket::create([
            'user_id' => Auth::id(),
            'codigo_reserva' => 'LCR-' . strtoupper(Str::random(5)), // Aquí corregimos 'codigo' por 'codigo_reserva'
            'total' => $total,
            'estado' => 'pendiente',
        ]);

        // 4. Guardar cada producto en la tabla de detalles
        foreach ($carrito as $item) {
            DetalleTicket::create([
                'ticket_id' => $ticket->id,
                'producto_id' => $item['id'],
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $item['precio'],
            ]);
        }

        // 5. Redirigir a la pantalla de éxito (Ticket final)
        return redirect()->route('tienda.exito', $ticket->id);
    }

    public function exito($id)
    {
        // Buscamos el ticket con todos sus detalles y productos asociados
        $ticket = Ticket::with('detalles.producto')->findOrFail($id);
        
        // Retornamos la vista para descargar el comprobante
        return view('tienda.exito', compact('ticket'));
    }
}