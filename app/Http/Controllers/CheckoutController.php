<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\DetalleTicket;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 

class CheckoutController extends Controller
{
    public function procesar(Request $request)
    {
        // 1. Decodificamos el archivo JSON de los productos que viene del carrito
        $carrito = json_decode($request->carrito_datos, true);

        // Si llega vacio devolvemos el carrito
        if (!$carrito || count($carrito) == 0) {
            return redirect()->back()->withErrors(['error' => 'El carrito está vacío.']);
        }

        // 2. Calcular el total real sumando las cantidades
        $total = 0;
        foreach ($carrito as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }

        // 3. Crear el Ticket principal 
        $ticket = Ticket::create([
            'user_id' => Auth::id(),
            'codigo_reserva' => 'LCR-' . strtoupper(Str::random(5)),
            'total' => $total,
            'estado' => 'pendiente',
        ]);

        // 4. Guardamos cada producto en la tabla de detalles Y DESCONTAmos STOCK
        foreach ($carrito as $item) {
            
            // Consultamos el producto en la BD para saber su costo de compra actual
            $producto = \App\Models\Producto::find($item['id']);
            
            // A) Creamos el detalle del ticket
            DetalleTicket::create([
                'ticket_id' => $ticket->id,
                'producto_id' => $item['id'],
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $item['precio'],
                'precio_compra' => $producto ? $producto->precio_compra : 0, // <--- FOTOGRAFÍA DEL COSTO
            ]);

            // B) Descontamos el stock inmediatamente
            DB::table('productos')
                ->where('id', $item['id'])
                ->decrement('stock', $item['cantidad']);
        }

        // 5. Redirigir a la pantalla de éxito con el Ticket final
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