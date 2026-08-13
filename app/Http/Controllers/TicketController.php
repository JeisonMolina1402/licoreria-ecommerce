<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Support\Facades\DB;   
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Str;
use App\Models\TurnoCaja;

// 🔥 IMPORTAMOS LA NOTIFICACIÓN PARA EL CLIENTE
use App\Notifications\TicketListoClienteNotification;

class TicketController extends Controller
{
    /**
     * CONSTRUCTOR: Seguridad del Controlador.
     * Protege todas las rutas de este controlador. Solo un usuario autenticado cajero/admin 
     * puede interactuar con el Punto de Venta o ver el historial de tickets.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * MÉTODO INDEX : Muestra el historial de tickets con Eager Loading.
     */
    public function index(Request $request)
    {
        // BUSCAMOS SI EL VENDEDOR TIENE UN TURNO DE CAJA ABIERTO PARA EL WIDGET SUPERIOR
        $turnoAbierto = TurnoCaja::where('user_id', Auth::id())
                                 ->where('estado', 'abierto')
                                 ->first();

        return view('tickets.index', compact('turnoAbierto'));
    }

    // MÉTODO CREATE: Prepara y muestra la pantalla del Punto de Venta mediante livewire
    public function create(Request $request)
    {
        return view('tickets.create');
    }

    /**
     * MÉTODO CAMBIAR ESTADO: Permite avanzar el flujo del ticket de Pendiente a Pagado
     */
    public function cambiarEstado(Request $request, $id)
    {
        // 1. SEGURIDAD: Validamos los estados permitidos
        $request->validate([
            'estado' => 'required|in:pendiente,pagado,listo,entregado,cancelado' 
        ], [
            'estado.in' => 'El estado seleccionado no es válido.'
        ]);

        $ticket = Ticket::with('detalles')->findOrFail($id);
        $estadoAnterior = $ticket->estado;

        // Si el ticket NO estaba cancelado antes, y el NUEVO estado que pide el admin ES 'cancelado'...
        if ($estadoAnterior != 'cancelado' && $request->estado == 'cancelado') {
            foreach ($ticket->detalles as $detalle) {
                DB::table('productos')
                    ->where('id', $detalle->producto_id)
                    ->increment('stock', $detalle->cantidad);
            }
        }

        // CONTROL DE CAJA: Si el ticket pasa de 'pendiente' a un estado donde ya se recibió el dinero
        if ($estadoAnterior == 'pendiente' && in_array($request->estado, ['pagado', 'listo', 'entregado'])) {
            
            $turnoAbierto = TurnoCaja::where('user_id', Auth::id())
                                     ->where('estado', 'abierto')
                                     ->first();
            
            if ($turnoAbierto) {
                // Sumamos a las transferencias del vendedor actual
                $turnoAbierto->total_transferencias += $ticket->total;
                $turnoAbierto->save();

                // Aseguramos que quede marcado como transferencia
                $ticket->metodo_pago = 'transferencia';
            } else {
                return redirect()->back()->withErrors(['error' => 'Debes abrir un turno de caja antes de aprobar pagos.']);
            }
        }

        // 3. Actualizamos el estado y guardamos
        $ticket->estado = $request->estado;
        $ticket->save();

        // ==========================================
        // 4. DISPARAR NOTIFICACIÓN AL CLIENTE
        // ==========================================
        // Verificamos si el nuevo estado es "listo" para avisarle que venga al local
        if ($request->estado === 'listo' && $ticket->user) {
            $ticket->user->notify(new TicketListoClienteNotification($ticket));
        }
        // ==========================================

        return redirect()->back()->with('success', '¡El estado del ticket ha sido actualizado!');
    }

    /**
     * MÉTODO STORE (COBRAR):Guardamos la venta, creamos detalles y restamos stock.
     */
    public function store(Request $request)
    {
        // 1. VALIDAMOS LOS DATOS QUE VIENEN DE JAVASCRIPT
        $request->validate([
            'productos' => 'required|array',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio' => 'required|numeric|min:0',
            'metodo_pago' => 'required|in:efectivo,transferencia',
        ], [
            'productos.required' => 'Debes agregar al menos un producto al ticket antes de cobrar.'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $totalReal = 0;

                // PASO A: VERIFICACIÓN DE SEGURIDAD para evitar ventas a medias
                foreach ($request->productos as $item) {
                    $producto = Producto::findOrFail($item['id']);

                    // Si un producto no tiene stock, cancelamos TODO antes de guardar nada
                    if ($producto->stock < $item['cantidad']) {
                        throw new \Exception("Stock insuficiente para: " . $producto->nombre);
                    }

                    // Sumamos al total (calculado de forma segura en el servidor)
                    $totalReal += ($producto->precio * $item['cantidad']);
                }

                // PASO B: CREACIÓN DEL TICKET
                $ticket = Ticket::create([
                    'user_id' => Auth::id(),
                    'codigo_reserva' => strtoupper(Str::random(8)),
                    'estado' => 'entregado',
                    'metodo_pago' => $request->metodo_pago,
                    'total' => $totalReal,
                ]);

                // PASO B.2: SUMAR AL TURNO DE CAJA ACTIVO
                $turnoAbierto = TurnoCaja::where('user_id', Auth::id())
                                         ->where('estado', 'abierto')
                                         ->first();

                if ($turnoAbierto) {
                    // Verificamos el método de pago para sumar en el lugar correcto
                    if ($request->metodo_pago == 'efectivo') {
                        $turnoAbierto->total_efectivo += $totalReal;
                    } else {
                        $turnoAbierto->total_transferencias += $totalReal;
                    }
                    $turnoAbierto->save();
                } else {
                    throw new \Exception("Debes abrir un turno de caja antes de realizar cobros.");
                }

                // PASO C: GUARDAR DETALLES Y DESCONTAR STOCK 
                foreach ($request->productos as $item) {

                    // Volvemos a llamar al producto fresco de la Base de Datos
                    $producto = Producto::findOrFail($item['id']);

                    // 1. Guardamos en la tabla detalle_tickets
                    $ticket->detalles()->create([
                        'producto_id' => $producto->id,
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $producto->precio,
                        'precio_compra' => $producto->precio_compra, // <--- FOTOGRAFÍA DEL COSTO
                    ]);

                    // 2. DESCONTAMOS EL STOCK DIRECTAMENTE
                    $producto->stock = $producto->stock - $item['cantidad'];
                    $producto->save(); // save() fuerza la actualización de 'updated_at' y guarda los datos
                }
            });

            return redirect()->route('tickets.create')->with('success', 'El cobro se ha realizado y el inventario fue actualizado.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}