<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket; 
use App\Models\Producto; 
use App\Models\Categoria; 
use Illuminate\Support\Facades\DB;   //Para las transacciones atómicas (Rollback/Commit)
use Illuminate\Support\Facades\Auth; //Para registrar qué cajero hizo la venta
use Illuminate\Support\Str;

class TicketController extends Controller
{
    /**
     * CONSTRUCTOR: Seguridad del Controlador.
     * Protege todas las rutas de este controlador. Solo un usuario autenticado (cajero/admin) 
     * puede interactuar con el Punto de Venta o ver el historial de tickets.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * MÉTODO INDEX (LEER): Muestra el historial de tickets con Eager Loading.
     */
    public function index(Request $request)
    {
        // 1. OPTIMIZACIÓN (EAGER LOADING): 
        // Usamos 'with' para traer los datos del usuario y los productos asociados en la misma consulta SQL.
        // Esto evita el "Problema de las N+1 consultas", haciendo que la página cargue instantáneamente 
        // sin importar si hay 10 o 1000 tickets.
        $query = Ticket::with(['user', 'detalles.producto']);

        // 2. FILTROS DINÁMICOS:
        // Búsqueda por el código único generado aleatoriamente (ej. A7B9XYZ2)
        if ($request->filled('buscar_codigo')) {
            $query->where('codigo_reserva', 'LIKE', '%' . $request->buscar_codigo . '%');
        }

        // Filtro exacto por el estado actual del ticket
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // 3. PAGINACIÓN: Ordenamos del más reciente al más antiguo (latest) y mantenemos los filtros en la URL (appends).
        $tickets = $query->latest()->paginate(10)->appends($request->all());

        return view('tickets.index', compact('tickets'));
    }

    /**
     * MÉTODO CREATE: Prepara y muestra la pantalla del Punto de Venta (POS).
     */
    public function create()
    {
        // REGLA DE NEGOCIO ESTRICTA: 
        // Solo enviamos a la vista del cajero los productos que tengan un stock mayor a cero.
        // Esto previene desde la raíz que el cajero intente vender algo que no existe físicamente.
        $productos = Producto::where('stock', '>', 0)->get();
        $categorias = Categoria::all();
        
        return view('tickets.create', compact('productos', 'categorias'));
    }

    /**
     * MÉTODO CAMBIAR ESTADO: Permite avanzar el flujo del ticket (ej. de Pendiente a Pagado).
     */
    public function cambiarEstado(Request $request, $id)
    {
        // 1. SEGURIDAD: Validamos que un usuario no inyecte un estado que no existe 
        // (ej. 'estado' => 'hackeado') usando la regla 'in:' de Laravel.
        $request->validate([
            'estado' => 'required|in:pendiente,pagado,entregado,cancelado'
        ], [
            'estado.in' => 'El estado seleccionado no es válido.'
        ]);

        // 2. Buscamos el ticket y actualizamos.
        $ticket = Ticket::findOrFail($id);
        $ticket->estado = $request->estado;
        $ticket->save();

        return redirect()->back()->with('success', '¡El estado del ticket ha sido actualizado!');
    }

    /**
     * MÉTODO STORE (COBRAR):Guarda la venta, crea detalles y resta stock.
     */
    public function store(Request $request)
    {
        // 1. VALIDACIÓN DEL ARREGLO DE PRODUCTOS:
        // Verificamos que el carrito enviado desde JavaScript no esté vacío y que cada producto
        // tenga un ID que realmente exista en nuestra base de datos, cantidades enteras y precios lógicos.
        $request->validate([
            'productos' => 'required|array',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio' => 'required|numeric|min:0',
        ], [
            'productos.required' => 'Debes agregar al menos un producto al ticket antes de cobrar.'
        ]);

        try {
            // 2. INICIO DE TRANSACCIÓN ATÓMICA (DB::transaction)
            // Esto garantiza que si ocurre un error en el paso 3 o 4, se hace un "Rollback" automático.
            // Ningún dato se guarda a medias, protegiendo la integridad del inventario.
            DB::transaction(function () use ($request) {
                $totalReal = 0;
                $detallesVenta = [];

                // 3. SERVER-SIDE VALIDATION (No confiar en el cliente)
                // Recorremos lo que envió JavaScript, pero NO confiamos en su total. Lo recalculamos en el servidor.
                foreach ($request->productos as $item) {
                    $producto = Producto::findOrFail($item['id']);

                    // PREVENCIÓN DE CONDICIÓN DE CARRERA (Race Condition):
                    // Verificamos el stock en el milisegundo exacto antes de cobrar. 
                    // Si otro cajero vendió la última botella un segundo antes, lanzamos una excepción y frenamos todo.
                    if ($producto->stock < $item['cantidad']) {
                        throw new \Exception("Stock insuficiente para: " . $producto->nombre);
                    }

                    // Calculamos el subtotal real usando el precio de la Base de Datos, no el del HTML.
                    $subtotal = $producto->precio * $item['cantidad'];
                    $totalReal += $subtotal;

                    // Preparamos los datos para la tabla pivote (detalle_tickets)
                    $detallesVenta[] = [
                        'producto' => $producto,
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $producto->precio // "Congelamos" el precio histórico
                    ];
                }

                // 4. CREACIÓN DEL TICKET MAESTRO
                $ticket = Ticket::create([
                    'user_id' => Auth::id(), // Registramos automáticamente qué cajero está cobrando
                    'codigo_reserva' => strtoupper(Str::random(8)), // Generamos código único alfanumérico
                    'estado' => 'entregado', // Por lógica de negocio de POS físico, nace pagado/entregado
                    'total' => $totalReal, // Usamos el total que nosotros calculamos de forma segura
                ]);

                // 5. INSERCIÓN DE DETALLES Y DESCUENTO DE STOCK FÍSICO
                foreach ($detallesVenta as $detalle) {
                    // Creamos el registro en la tabla pivote usando la relación de Eloquent
                    $ticket->detalles()->create([
                        'producto_id' => $detalle['producto']->id,
                        'cantidad' => $detalle['cantidad'],
                        'precio_unitario' => $detalle['precio_unitario'],
                    ]);

                    // Descontamos el stock de la base de datos de forma segura usando decrement()
                    $detalle['producto']->decrement('stock', $detalle['cantidad']);
                }
            });

            // Si la transacción finalizó sin excepciones (Commit exitoso), regresamos al POS con éxito.
            return redirect()->route('tickets.create')->with('success', 'El cobro se ha realizado y el inventario fue actualizado.');

        } catch (\Exception $e) {
            // Si hubo cualquier error (ej. falta de stock), Laravel deshace todo lo hecho en la transacción
            // y devolvemos al cajero un mensaje de error exacto de lo que falló.
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}