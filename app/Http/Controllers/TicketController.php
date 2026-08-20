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

// IMPORTAMOS LA NOTIFICACIÓN PARA EL CLIENTE
use App\Notifications\TicketListoClienteNotification;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $turnoAbierto = TurnoCaja::where('user_id', Auth::id())
                                 ->where('estado', 'abierto')
                                 ->first();

        return view('tickets.index', compact('turnoAbierto'));
    }

    public function create(Request $request)
    {
        return view('tickets.create');
    }

    public function cambiarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,pagado,listo,entregado,cancelado' 
        ], [
            'estado.in' => 'El estado seleccionado no es válido.'
        ]);

        $ticket = Ticket::with('detalles')->findOrFail($id);
        $estadoAnterior = $ticket->estado;

        if ($estadoAnterior != 'cancelado' && $request->estado == 'cancelado') {
            
            foreach ($ticket->detalles as $detalle) {
                $producto = Producto::find($detalle->producto_id);
                if ($producto) {
                    $stockAnterior = $producto->stock;

                    $producto->disableLogging();
                    $producto->stock = $producto->stock + $detalle->cantidad;
                    $producto->save();
                    $producto->enableLogging();

                    activity('inventario')
                        ->causedBy(Auth::user())
                        ->performedOn($producto)
                        ->event('devolucion_manual')
                        ->withProperties([
                            'old' => ['stock' => $stockAnterior],
                            'attributes' => ['stock' => $producto->stock]
                        ])
                        ->log("{$detalle->cantidad} producto(s) devuelto(s) al stock debido a la cancelación manual del Ticket {$ticket->codigo_reserva}");
                }
            }

            if (in_array($estadoAnterior, ['pagado', 'listo', 'entregado'])) {
                $turnoAbierto = TurnoCaja::where('user_id', Auth::id())
                                         ->where('estado', 'abierto')
                                         ->first();

                if ($turnoAbierto) {
                    $saldoAnteriorEfectivo = $turnoAbierto->total_efectivo;
                    $saldoAnteriorTransf = $turnoAbierto->total_transferencias;

                    $turnoAbierto->disableLogging();

                    if ($ticket->metodo_pago == 'efectivo') {
                        $turnoAbierto->total_efectivo -= $ticket->total;
                    } else {
                        $turnoAbierto->total_transferencias -= $ticket->total;
                    }
                    $turnoAbierto->save();
                    $turnoAbierto->enableLogging();

                    activity('caja')
                        ->causedBy(Auth::user())
                        ->performedOn($turnoAbierto)
                        ->event('devolucion_dinero')
                        ->withProperties([
                            'old' => [
                                'total_efectivo' => $saldoAnteriorEfectivo,
                                'total_transferencias' => $saldoAnteriorTransf
                            ],
                            'attributes' => [
                                'total_efectivo' => $turnoAbierto->total_efectivo,
                                'total_transferencias' => $turnoAbierto->total_transferencias
                            ]
                        ])
                        ->log("Egreso de \${$ticket->total} ({$ticket->metodo_pago}) devuelto al cliente por cancelación del Ticket {$ticket->codigo_reserva}");
                }
            }
        }

        if ($estadoAnterior == 'pendiente' && in_array($request->estado, ['pagado', 'listo', 'entregado'])) {
            
            $turnoAbierto = TurnoCaja::where('user_id', Auth::id())
                                     ->where('estado', 'abierto')
                                     ->first();
            
            if ($turnoAbierto) {
                $turnoAbierto->total_transferencias += $ticket->total;
                $turnoAbierto->save();

                $ticket->metodo_pago = 'transferencia';
            } else {
                return redirect()->back()->withErrors(['error' => 'Debes abrir un turno de caja antes de aprobar pagos.']);
            }
        }

        // 🔥 NUEVO: ASIGNACIÓN DE VENTA AL CAJERO (LÓGICA DE BONOS)
        // Si el ticket no tiene vendedor asignado (viene de la web) y lo acaban de marcar como completado...
        if (is_null($ticket->vendedor_id) && in_array($request->estado, ['pagado', 'listo', 'entregado'])) {
            $ticket->vendedor_id = Auth::id(); // ¡Punto para el cajero que lo gestionó!
        }

        $ticket->estado = $request->estado;
        $ticket->save();

        if ($request->estado === 'listo' && $ticket->user) {
            $ticket->user->notify(new TicketListoClienteNotification($ticket));
        }

        return redirect()->back()->with('success', '¡El estado del ticket ha sido actualizado!');
    }

    public function store(Request $request)
    {
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

                foreach ($request->productos as $item) {
                    $producto = Producto::findOrFail($item['id']);

                    if ($producto->stock < $item['cantidad']) {
                        throw new \Exception("Stock insuficiente para: " . $producto->nombre);
                    }

                    $totalReal += ($producto->precio * $item['cantidad']);
                }

                $ticket = Ticket::create([
                    'user_id' => Auth::id(), 
                    'vendedor_id' => Auth::id(), // 🔥 NUEVO: LA VENTA POS PERTENECE AL CAJERO ACTUAL
                    'codigo_reserva' => strtoupper(Str::random(8)),
                    'estado' => 'entregado',
                    'metodo_pago' => $request->metodo_pago,
                    'total' => $totalReal,
                ]);

                $turnoAbierto = TurnoCaja::where('user_id', Auth::id())
                                         ->where('estado', 'abierto')
                                         ->first();

                if ($turnoAbierto) {
                    if ($request->metodo_pago == 'efectivo') {
                        $turnoAbierto->total_efectivo += $totalReal;
                    } else {
                        $turnoAbierto->total_transferencias += $totalReal;
                    }
                    $turnoAbierto->save();
                } else {
                    throw new \Exception("Debes abrir un turno de caja antes de realizar cobros.");
                }

                foreach ($request->productos as $item) {
                    $producto = Producto::findOrFail($item['id']);

                    $ticket->detalles()->create([
                        'producto_id' => $producto->id,
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $producto->precio,
                        'precio_compra' => $producto->precio_compra,
                    ]);

                    $stockAnterior = $producto->stock;

                    $producto->disableLogging();
                    $producto->stock = $producto->stock - $item['cantidad'];
                    $producto->save(); 
                    $producto->enableLogging(); 

                    activity('inventario')
                        ->causedBy(Auth::user())
                        ->performedOn($producto)
                        ->event('venta_pos')
                        ->withProperties([
                            'old' => ['stock' => $stockAnterior],
                            'attributes' => ['stock' => $producto->stock]
                        ])
                        ->log("{$item['cantidad']} producto(s) vendido(s) en mostrador en el Ticket {$ticket->codigo_reserva}");
                }
            });

            return redirect()->route('tickets.create')->with('success', 'El cobro se ha realizado y el inventario fue actualizado.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function subirComprobante(Request $request, $id)
    {
        $request->validate([
            'comprobante' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $ticket = \App\Models\Ticket::findOrFail($id);

        if ($request->hasFile('comprobante')) {
            $nombreImagen = 'wp_' . $ticket->codigo_reserva . '_' . time() . '.' . $request->comprobante->extension();
            $request->comprobante->move(public_path('uploads/comprobantes'), $nombreImagen);
            
            $ticket->comprobante_whatsapp = 'uploads/comprobantes/' . $nombreImagen;
            $ticket->save();
        }

        return redirect()->back()->with('success', '¡Comprobante adjuntado exitosamente al ticket ' . $ticket->codigo_reserva . '!');
    }
}