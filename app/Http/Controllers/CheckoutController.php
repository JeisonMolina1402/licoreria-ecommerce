<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\DetalleTicket;
use App\Models\User; // <-- Importamos el modelo User
use App\Notifications\TicketCreadoClienteNotification; // <-- Importamos la notificación del cliente
use App\Notifications\NuevoTicketAdminNotification; // <-- Importamos la notificación del admin
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

        // 4. Guardamos cada producto en la tabla de detalles Y DESCONTAMOS STOCK
        foreach ($carrito as $item) {
            $producto = \App\Models\Producto::find($item['id']);
            
            if ($producto) {
                DetalleTicket::create([
                    'ticket_id' => $ticket->id,
                    'producto_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'precio_compra' => $producto->precio_compra,
                ]);

                // Guardamos el stock viejo para la auditoría
                $stockAnterior = $producto->stock;

                // Apagamos el log automático un segundo para evitar filas duplicadas
                $producto->disableLogging();
                $producto->stock = $producto->stock - $item['cantidad'];
                $producto->save();
                $producto->enableLogging(); // Lo volvemos a encender

                /// Creamos UN SOLO registro maestro
                activity('inventario') 
                    ->causedBy(Auth::user()) 
                    ->performedOn($producto)
                    ->event('reserva_online')
                    ->withProperties([
                        'old' => ['stock' => $stockAnterior],
                        'attributes' => ['stock' => $producto->stock]
                    ])
                    ->log("{$item['cantidad']} producto(s) reservado(s) por compra online en el Ticket {$ticket->codigo_reserva}");
            }
        }

        // ==========================================
        // 5. DISPARAR NOTIFICACIONES Y CORREOS
        // ==========================================
        
        // A) Notificar al cliente (Email + Campanita)
        $ticket->user->notify(new TicketCreadoClienteNotification($ticket));

        // B) Notificar a todo el personal (Admins y Vendedores) en su campanita
        $personal = User::whereIn('rol', ['admin', 'vendedor'])->get();
        foreach ($personal as $miembro) {
            $miembro->notify(new NuevoTicketAdminNotification($ticket));
        }
        // ==========================================

        // 6. Redirigir a la pantalla de éxito con el Ticket final
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