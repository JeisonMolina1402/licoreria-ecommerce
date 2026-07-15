<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\DetalleTicket;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\User;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->toDateString());
        $fechaFin = $request->input('fecha_fin', Carbon::now()->endOfMonth()->toDateString());

        $rangoFechas = [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'];

        $ticketsCompletados = Ticket::with('detalles.producto')
                                    ->where('estado', 'entregado')
                                    ->whereBetween('created_at', $rangoFechas)
                                    ->get();

        $ventasTotales = 0;
        $gananciaNeta = 0;

        foreach ($ticketsCompletados as $ticket) {
            $ventasTotales += $ticket->total;
            foreach ($ticket->detalles as $detalle) {
                if ($detalle->producto) {
                    $ingresoProducto = $detalle->precio_unitario * $detalle->cantidad;
                    $costoProducto = $detalle->producto->precio_compra * $detalle->cantidad;
                    $gananciaNeta += ($ingresoProducto - $costoProducto);
                }
            }
        }

        $nuevosUsuarios = User::whereBetween('created_at', $rangoFechas)->where('rol', '!=', 'admin')->count();
        $totalTickets = Ticket::whereBetween('created_at', $rangoFechas)->count();
        $ticketsEntregados = $ticketsCompletados->count();
        $ticketsCancelados = Ticket::where('estado', 'cancelado')->whereBetween('created_at', $rangoFechas)->count();

        // ---1: Paginación de TODOS los Productos (Incluyendo 0 ventas) ---
        $productosTop = Producto::withSum(['detalles as total_vendido' => function($query) use ($rangoFechas) {
                $query->whereHas('ticket', function($q) use ($rangoFechas) {
                    $q->where('estado', 'entregado')
                      ->whereBetween('created_at', $rangoFechas);
                });
            }], 'cantidad')
            ->orderByRaw('COALESCE(total_vendido, 0) DESC') // Ordena tomando los nulos como 0
            ->paginate(5)
            ->appends($request->all());

        // --- NUEVO 2: Datos para el Gráfico de Categorías (INCLUYENDO 0 VENTAS) ---
        $detallesParaCategorias = DetalleTicket::whereHas('ticket', function($query) use ($rangoFechas) {
                $query->where('estado', 'entregado')->whereBetween('created_at', $rangoFechas);
            })->with('producto.categoria')->get();

        // 1. Sumamos lo que SÍ se vendió
        $categoriasVendidas = $detallesParaCategorias->groupBy(function($detalle) {
            return $detalle->producto->categoria->nombre ?? 'Sin Categoría';
        })->map(function($grupo) {
            return $grupo->sum('cantidad');
        });

        // 2. Traemos TODAS las categorías de la base de datos
        $todasLasCategorias = \App\Models\Categoria::pluck('nombre');
        $ventasPorCategoria = collect();

        // 3. Cruzamos los datos: las que no tienen ventas quedan en 0
        foreach ($todasLasCategorias as $nombreCategoria) {
            $ventasPorCategoria[$nombreCategoria] = $categoriasVendidas->get($nombreCategoria, 0);
        }
        
        // Por si hay algún producto sin categoría asignada
        if ($categoriasVendidas->has('Sin Categoría')) {
            $ventasPorCategoria['Sin Categoría'] = $categoriasVendidas['Sin Categoría'];
        }

        // Ordenamos de mayor a menor para que el gráfico quede proporcionado
        $ventasPorCategoria = $ventasPorCategoria->sortDesc();

        // Convertimos a JSON para que JavaScript (Chart.js) pueda leerlo
        $nombresCategorias = $ventasPorCategoria->keys()->toJson();
        $cantidadesCategorias = $ventasPorCategoria->values()->toJson();

        // --- NUEVO 3: Gráfico de Barras Dinámico (Diario o Mensual) ---
        $fechaInicioObj = Carbon::parse($fechaInicio);
        $fechaFinObj = Carbon::parse($fechaFin);
        $diasDiferencia = $fechaInicioObj->diffInDays($fechaFinObj);

        $mesesEs = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

        $etiquetasBarras = [];
        $ventasBarras = [];
        $gananciasBarras = [];
        $gastosBarras = []; // <-- NUEVO: Contenedor para los costos
        $tituloGraficoBarras = '';

        // MODO 1: Rango de 60 días o menos (Agrupamos por DÍA)
        if ($diasDiferencia <= 60) {
            $tituloGraficoBarras = 'Rendimiento Diario';
            
            for ($date = $fechaInicioObj->copy(); $date->lte($fechaFinObj); $date->addDay()) {
                $etiqueta = $date->format('d') . ' ' . $mesesEs[$date->format('n')];
                $etiquetasBarras[] = $etiqueta;
                $ventasBarras[$etiqueta] = 0;
                $gananciasBarras[$etiqueta] = 0;
                $gastosBarras[$etiqueta] = 0; // Inicializamos en 0
            }

            foreach ($ticketsCompletados as $ticket) {
                $etiqueta = $ticket->created_at->format('d') . ' ' . $mesesEs[$ticket->created_at->format('n')];
                if (isset($ventasBarras[$etiqueta])) {
                    $ventasBarras[$etiqueta] += $ticket->total;
                    
                    foreach ($ticket->detalles as $detalle) {
                        if ($detalle->producto) {
                            $ingreso = $detalle->precio_unitario * $detalle->cantidad;
                            $costo = $detalle->producto->precio_compra * $detalle->cantidad;
                            
                            $gastosBarras[$etiqueta] += $costo; // Sumamos lo que nos costó
                            $gananciasBarras[$etiqueta] += ($ingreso - $costo); // Sumamos la ganancia
                        }
                    }
                }
            }
        } 
        // MODO 2: Rango mayor a 60 días (Agrupamos por MES)
        else {
            $tituloGraficoBarras = 'Rendimiento Mensual';
            
            for ($date = $fechaInicioObj->copy()->startOfMonth(); $date->lte($fechaFinObj); $date->addMonth()) {
                $etiqueta = $mesesEs[$date->format('n')] . ' ' . $date->format('Y');
                $etiquetasBarras[] = $etiqueta;
                $ventasBarras[$etiqueta] = 0;
                $gananciasBarras[$etiqueta] = 0;
                $gastosBarras[$etiqueta] = 0; // Inicializamos en 0
            }

            foreach ($ticketsCompletados as $ticket) {
                $etiqueta = $mesesEs[$ticket->created_at->format('n')] . ' ' . $ticket->created_at->format('Y');
                if (isset($ventasBarras[$etiqueta])) {
                    $ventasBarras[$etiqueta] += $ticket->total;
                    
                    foreach ($ticket->detalles as $detalle) {
                        if ($detalle->producto) {
                            $ingreso = $detalle->precio_unitario * $detalle->cantidad;
                            $costo = $detalle->producto->precio_compra * $detalle->cantidad;
                            
                            $gastosBarras[$etiqueta] += $costo; // Sumamos lo que nos costó
                            $gananciasBarras[$etiqueta] += ($ingreso - $costo); // Sumamos la ganancia
                        }
                    }
                }
            }
        }

        // Convertimos a JSON
        $nombresBarras = json_encode(array_values($etiquetasBarras));
        $datosVentasBarras = json_encode(array_values($ventasBarras));
        $datosGananciasBarras = json_encode(array_values($gananciasBarras));
        $datosGastosBarras = json_encode(array_values($gastosBarras)); // <-- NUEVO JSON

        return view('reportes.index', compact(
            'fechaInicio', 'fechaFin', 'ventasTotales', 'gananciaNeta', 
            'nuevosUsuarios', 'totalTickets', 'ticketsEntregados', 'ticketsCancelados',
            'productosTop', 'nombresCategorias', 'cantidadesCategorias',
            // Asegúrate de agregar datosGastosBarras al final de esta lista:
            'tituloGraficoBarras', 'nombresBarras', 'datosVentasBarras', 'datosGananciasBarras', 'datosGastosBarras'
        ));
    }
     
      
    public function exportarPdf(Request $request)
    {
        // Al ser POST, capturamos los inputs directamente de la petición
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->toDateString());
        $fechaFin = $request->input('fecha_fin', Carbon::now()->endOfMonth()->toDateString());

        $rangoFechas = [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'];

        $ticketsCompletados = Ticket::with('detalles.producto')
                                    ->where('estado', 'entregado')
                                    ->whereBetween('created_at', $rangoFechas)
                                    ->get();

        $ventasTotales = 0;
        $gananciaNeta = 0;

        foreach ($ticketsCompletados as $ticket) {
            $ventasTotales += $ticket->total;
            foreach ($ticket->detalles as $detalle) {
                if ($detalle->producto) {
                    $ingresoProducto = $detalle->precio_unitario * $detalle->cantidad;
                    $costoProducto = $detalle->producto->precio_compra * $detalle->cantidad;
                    $gananciaNeta += ($ingresoProducto - $costoProducto);
                }
            }
        }

        $nuevosUsuarios = User::whereBetween('created_at', $rangoFechas)->where('rol', '!=', 'admin')->count();
        $totalTickets = Ticket::whereBetween('created_at', $rangoFechas)->count();
        $ticketsEntregados = $ticketsCompletados->count();
        
        $productosVendidos = DetalleTicket::selectRaw('producto_id, SUM(cantidad) as total_vendido')
            ->whereHas('ticket', function($query) use ($rangoFechas) {
                $query->where('estado', 'entregado')
                      ->whereBetween('created_at', $rangoFechas);
            })
            ->groupBy('producto_id')
            ->orderByDesc('total_vendido')
            ->with('producto')
            ->get();

        $idsVendidos = $productosVendidos->pluck('producto_id')->toArray();

        $productosCeroVentas = Producto::whereNotIn('id', $idsVendidos)
            ->orderBy('nombre', 'asc')
            ->get();

        $detallesParaCategorias = DetalleTicket::whereHas('ticket', function($query) use ($rangoFechas) {
                $query->where('estado', 'entregado')->whereBetween('created_at', $rangoFechas);
            })->with('producto.categoria')->get();

        $categoriasVendidas = $detallesParaCategorias->groupBy(function($detalle) {
            return $detalle->producto->categoria->nombre ?? 'Sin Categoría';
        })->map(function($grupo) {
            return $grupo->sum('cantidad');
        });

        $todasLasCategorias = Categoria::pluck('nombre');
        $ventasPorCategoria = collect();

        foreach ($todasLasCategorias as $nombreCategoria) {
            $ventasPorCategoria[$nombreCategoria] = $categoriasVendidas->get($nombreCategoria, 0);
        }
        
        if ($categoriasVendidas->has('Sin Categoría')) {
            $ventasPorCategoria['Sin Categoría'] = $categoriasVendidas['Sin Categoría'];
        }

        $ventasPorCategoria = $ventasPorCategoria->sortDesc();

        // --- NUEVO: CAPTURAR LAS IMÁGENES DE LOS GRÁFICOS ---
        $graficoBarras = $request->input('grafico_barras_base64');
        $graficoDona = $request->input('grafico_dona_base64');

        $pdf = Pdf::loadView('reportes.pdf', compact(
            'fechaInicio', 'fechaFin', 'ventasTotales', 'gananciaNeta', 
            'totalTickets', 'ticketsEntregados', 'nuevosUsuarios', 
            'productosVendidos', 'productosCeroVentas', 'ventasPorCategoria',
            // Enviamos las imágenes al PDF
            'graficoBarras', 'graficoDona'
        ));

        return $pdf->download('Reporte_Ventas_'.$fechaInicio.'_al_'.$fechaFin.'.pdf');
    }

}