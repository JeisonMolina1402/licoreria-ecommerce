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
   // =========================================================================
    // MÉTODO 1: VISTA WEB (DASHBOARD)
    // =========================================================================
    public function index(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->toDateString());
        $fechaFin = $request->input('fecha_fin', Carbon::now()->endOfMonth()->toDateString());
        $modoAgrupacion = $request->input('modo_agrupacion');

        $rangoFechas = [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'];

        // Calculamos las métricas globales (Tarjetas superiores)
        $ticketsCompletados = Ticket::with('detalles.producto')
                                    ->where('estado', 'entregado')
                                    ->whereBetween('created_at', $rangoFechas)
                                    ->get();

        $ventasTotales = 0;
        $gananciaNeta = 0;
        $costosTotales = 0;

        foreach ($ticketsCompletados as $ticket) {
            $ventasTotales += $ticket->total;
            foreach ($ticket->detalles as $detalle) {
                if ($detalle->producto) {
                    $ingresoProducto = $detalle->precio_unitario * $detalle->cantidad;
                    $costoProducto = $detalle->precio_compra * $detalle->cantidad;
                    $gananciaNeta += ($ingresoProducto - $costoProducto);
                    $costosTotales += $costoProducto;
                }
            }
        }

        $nuevosUsuarios = User::whereBetween('created_at', $rangoFechas)->where('rol', '!=', 'admin')->count();
        $totalTickets = Ticket::whereBetween('created_at', $rangoFechas)->count();
        $ticketsEntregados = $ticketsCompletados->count();
        $ticketsCancelados = Ticket::where('estado', 'cancelado')->whereBetween('created_at', $rangoFechas)->count();

        // --------------------------------------------------------------
        // GRÁFICOS Y AGRUPACIÓN TEMPORAL (GRÁFICO DE BARRAS)
        // --------------------------------------------------------------
        $fechaInicioObj = Carbon::parse($fechaInicio);
        $fechaFinObj = Carbon::parse($fechaFin);
        $diasDiferencia = $fechaInicioObj->diffInDays($fechaFinObj);

        $mesesEs = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

        $etiquetasBarras = [];
        $ventasBarras = [];
        $gananciasBarras = [];
        $gastosBarras = []; 
        $tituloGraficoBarras = '';
        $tituloTablaTemporal = '';

        if ($modoAgrupacion === 'trimestre') {
            $tituloGraficoBarras = 'Rendimiento Trimestral';
            $tituloTablaTemporal = 'Trimestre';
            $nombresTrimestres = [1 => '(Ene - Mar)', 2 => '(Abr - Jun)', 3 => '(Jul - Sep)', 4 => '(Oct - Dic)'];
            
            for ($date = $fechaInicioObj->copy()->startOfQuarter(); $date->lte($fechaFinObj); $date->addQuarter()) {
                $numTrimestre = ceil($date->format('n') / 3);
                $etiqueta = 'Trimestre ' . $numTrimestre . ' ' . $nombresTrimestres[$numTrimestre] . ' ' . $date->format('Y');
                $etiquetasBarras[] = $etiqueta;
                $ventasBarras[$etiqueta] = 0; $gananciasBarras[$etiqueta] = 0; $gastosBarras[$etiqueta] = 0;
            }
            foreach ($ticketsCompletados as $ticket) {
                $numTrimestre = ceil($ticket->created_at->format('n') / 3);
                $etiqueta = 'Trimestre ' . $numTrimestre . ' ' . $nombresTrimestres[$numTrimestre] . ' ' . $ticket->created_at->format('Y');
                if (isset($ventasBarras[$etiqueta])) {
                    $ventasBarras[$etiqueta] += $ticket->total;
                    foreach ($ticket->detalles as $d) {
                        if ($d->producto) {
                            $c = $d->producto->precio_compra * $d->cantidad; $v = $d->precio_unitario * $d->cantidad;
                            $gastosBarras[$etiqueta] += $c; $gananciasBarras[$etiqueta] += ($v - $c);
                        }
                    }
                }
            }
        } elseif ($modoAgrupacion === 'semestre') {
            $tituloGraficoBarras = 'Rendimiento Semestral';
            $tituloTablaTemporal = 'Semestre';
            $nombresSemestres = [1 => '(Ene - Jun)', 2 => '(Jul - Dic)'];

            for ($date = $fechaInicioObj->copy()->startOfYear(); $date->lte($fechaFinObj); $date->addMonths(6)) {
                $numSemestre = ceil($date->format('n') / 6);
                $etiqueta = 'Semestre ' . $numSemestre . ' ' . $nombresSemestres[$numSemestre] . ' ' . $date->format('Y');
                $etiquetasBarras[] = $etiqueta;
                $ventasBarras[$etiqueta] = 0; $gananciasBarras[$etiqueta] = 0; $gastosBarras[$etiqueta] = 0;
            }
            foreach ($ticketsCompletados as $ticket) {
                $numSemestre = ceil($ticket->created_at->format('n') / 6);
                $etiqueta = 'Semestre ' . $numSemestre . ' ' . $nombresSemestres[$numSemestre] . ' ' . $ticket->created_at->format('Y');
                if (isset($ventasBarras[$etiqueta])) {
                    $ventasBarras[$etiqueta] += $ticket->total;
                    foreach ($ticket->detalles as $d) {
                        if ($d->producto) {
                            $c = $d->producto->precio_compra * $d->cantidad; $v = $d->precio_unitario * $d->cantidad;
                            $gastosBarras[$etiqueta] += $c; $gananciasBarras[$etiqueta] += ($v - $c);
                        }
                    }
                }
            }
        } elseif ($modoAgrupacion === 'anual' || $diasDiferencia >= 365) {
            $tituloGraficoBarras = 'Rendimiento Anual';
            $tituloTablaTemporal = 'Año';
            for ($date = $fechaInicioObj->copy()->startOfYear(); $date->lte($fechaFinObj); $date->addYear()) {
                $etiqueta = $date->format('Y');
                $etiquetasBarras[] = $etiqueta;
                $ventasBarras[$etiqueta] = 0; $gananciasBarras[$etiqueta] = 0; $gastosBarras[$etiqueta] = 0;
            }
            foreach ($ticketsCompletados as $ticket) {
                $etiqueta = $ticket->created_at->format('Y');
                if (isset($ventasBarras[$etiqueta])) {
                    $ventasBarras[$etiqueta] += $ticket->total;
                    foreach ($ticket->detalles as $d) {
                        if ($d->producto) {
                            $c = $d->producto->precio_compra * $d->cantidad; $v = $d->precio_unitario * $d->cantidad;
                            $gastosBarras[$etiqueta] += $c; $gananciasBarras[$etiqueta] += ($v - $c);
                        }
                    }
                }
            }
        } elseif ($modoAgrupacion === 'mes' || ($diasDiferencia > 60 && $diasDiferencia < 365)) {
            $tituloGraficoBarras = 'Rendimiento Mensual';
            $tituloTablaTemporal = 'Mes';
            for ($date = $fechaInicioObj->copy()->startOfMonth(); $date->lte($fechaFinObj); $date->addMonth()) {
                $etiqueta = $mesesEs[$date->format('n')] . ' ' . $date->format('Y');
                $etiquetasBarras[] = $etiqueta;
                $ventasBarras[$etiqueta] = 0; $gananciasBarras[$etiqueta] = 0; $gastosBarras[$etiqueta] = 0;
            }
            foreach ($ticketsCompletados as $ticket) {
                $etiqueta = $mesesEs[$ticket->created_at->format('n')] . ' ' . $ticket->created_at->format('Y');
                if (isset($ventasBarras[$etiqueta])) {
                    $ventasBarras[$etiqueta] += $ticket->total;
                    foreach ($ticket->detalles as $d) {
                        if ($d->producto) {
                            $c = $d->producto->precio_compra * $d->cantidad; $v = $d->precio_unitario * $d->cantidad;
                            $gastosBarras[$etiqueta] += $c; $gananciasBarras[$etiqueta] += ($v - $c);
                        }
                    }
                }
            }
        } else {
            $tituloGraficoBarras = 'Rendimiento Diario';
            $tituloTablaTemporal = 'Día';
            for ($date = $fechaInicioObj->copy(); $date->lte($fechaFinObj); $date->addDay()) {
                $etiqueta = $date->format('d') . ' ' . $mesesEs[$date->format('n')];
                $etiquetasBarras[] = $etiqueta;
                $ventasBarras[$etiqueta] = 0; $gananciasBarras[$etiqueta] = 0; $gastosBarras[$etiqueta] = 0;
            }
            foreach ($ticketsCompletados as $ticket) {
                $etiqueta = $ticket->created_at->format('d') . ' ' . $mesesEs[$ticket->created_at->format('n')];
                if (isset($ventasBarras[$etiqueta])) {
                    $ventasBarras[$etiqueta] += $ticket->total;
                    foreach ($ticket->detalles as $d) {
                        if ($d->producto) {
                            $c = $d->producto->precio_compra * $d->cantidad; $v = $d->precio_unitario * $d->cantidad;
                            $gastosBarras[$etiqueta] += $c; $gananciasBarras[$etiqueta] += ($v - $c);
                        }
                    }
                }
            }
        }

        $nombresBarras = json_encode(array_values($etiquetasBarras));
        $datosVentasBarras = json_encode(array_values($ventasBarras));
        $datosGananciasBarras = json_encode(array_values($gananciasBarras));
        $datosGastosBarras = json_encode(array_values($gastosBarras)); 

        return view('reportes.index', compact(
            'fechaInicio', 'fechaFin', 'ventasTotales', 'gananciaNeta','costosTotales',
            'nuevosUsuarios', 'totalTickets', 'ticketsEntregados', 'ticketsCancelados',
            'tituloGraficoBarras', 'tituloTablaTemporal', 'nombresBarras', 'datosVentasBarras', 'datosGananciasBarras', 'datosGastosBarras',
            'modoAgrupacion'
        ));
    }
     
    // =========================================================================
    // MÉTODO 2: EXPORTAR PDF
    // =========================================================================
    public function exportarPdf(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->toDateString());
        $fechaFin = $request->input('fecha_fin', Carbon::now()->endOfMonth()->toDateString());
        $modoAgrupacion = $request->input('modo_agrupacion');

        $rangoFechas = [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'];

        $ticketsCompletados = Ticket::with('detalles.producto')
                                    ->where('estado', 'entregado')
                                    ->whereBetween('created_at', $rangoFechas)
                                    ->get();

        $ventasTotales = 0;
        $gananciaNeta = 0;
        $costosTotales = 0;

        foreach ($ticketsCompletados as $ticket) {
            $ventasTotales += $ticket->total;
            foreach ($ticket->detalles as $detalle) {
                if ($detalle->producto) {
                    $ingresoProducto = $detalle->precio_unitario * $detalle->cantidad;
                    $costoProducto = $detalle->precio_compra * $detalle->cantidad;
                    $costosTotales += $costoProducto;
                    $gananciaNeta += ($ingresoProducto - $costoProducto);
                }
            }
        }

        $nuevosUsuarios = User::whereBetween('created_at', $rangoFechas)->where('rol', '!=', 'admin')->count();
        $totalTickets = Ticket::whereBetween('created_at', $rangoFechas)->count();
        $ticketsEntregados = $ticketsCompletados->count();
        
        // --------------------------------------------------------------
        // PRODUCTOS (PDF)
        // --------------------------------------------------------------
        $rankingProductos = $request->input('ranking_productos', 'ventas');
        $queryVendidos = DetalleTicket::selectRaw('
                producto_id, 
                SUM(detalle_tickets.cantidad) as total_vendido, 
                SUM(detalle_tickets.precio_unitario * detalle_tickets.cantidad) as ingreso_generado, 
                SUM((detalle_tickets.precio_unitario - detalle_tickets.precio_compra) * detalle_tickets.cantidad) as ganancia_generada
            ')
            ->join('productos', 'detalle_tickets.producto_id', '=', 'productos.id')
            ->whereHas('ticket', function($query) use ($rangoFechas) {
                $query->where('estado', 'entregado')->whereBetween('created_at', $rangoFechas);
            })
            ->groupBy('producto_id')
            ->with('producto.categoria');

        if ($rankingProductos === 'ganancia') { $queryVendidos->orderByDesc('ganancia_generada'); } 
        else { $queryVendidos->orderByDesc('total_vendido'); }

        $productosVendidos = $queryVendidos->get();
        $idsVendidos = $productosVendidos->pluck('producto_id')->toArray();

        $productosCeroVentas = Producto::with('categoria')->whereNotIn('id', $idsVendidos)->orderBy('nombre', 'asc')->get();

        // --------------------------------------------------------------
        // CATEGORÍAS (PDF)
        // --------------------------------------------------------------
        $rankingCategorias = $request->input('ranking_categorias', 'ventas');
        $detallesParaCategorias = DetalleTicket::whereHas('ticket', function($query) use ($rangoFechas) {
                $query->where('estado', 'entregado')->whereBetween('created_at', $rangoFechas);
            })->with('producto.categoria')->get();

        $ventasPorCategoria = []; 
        $todasLasCategorias = Categoria::pluck('nombre');
        foreach ($todasLasCategorias as $nombreCategoria) {
            $ventasPorCategoria[$nombreCategoria] = [ 'unidades' => 0, 'inversion' => 0, 'ventas' => 0, 'ganancia' => 0 ];
        }
        $ventasPorCategoria['Sin Categoría'] = ['unidades' => 0, 'inversion' => 0, 'ventas' => 0, 'ganancia' => 0];

        foreach ($detallesParaCategorias as $detalle) {
            $catNombre = $detalle->producto->categoria->nombre ?? 'Sin Categoría';
            $inversion = $detalle->precio_compra * $detalle->cantidad;
            $venta = $detalle->precio_unitario * $detalle->cantidad;

            $ventasPorCategoria[$catNombre]['unidades'] += $detalle->cantidad;
            $ventasPorCategoria[$catNombre]['inversion'] += $inversion;
            $ventasPorCategoria[$catNombre]['ventas'] += $venta;
            $ventasPorCategoria[$catNombre]['ganancia'] += ($venta - $inversion);
        }

        if ($rankingCategorias === 'ganancia') { $ventasPorCategoria = collect($ventasPorCategoria)->sortByDesc('ganancia'); } 
        elseif ($rankingCategorias === 'cero') { $ventasPorCategoria = collect($ventasPorCategoria)->where('unidades', 0); } 
        else { $ventasPorCategoria = collect($ventasPorCategoria)->sortByDesc('unidades'); }

        // --------------------------------------------------------------
        // TABLA TEMPORAL AVANZADA (PDF)
        // --------------------------------------------------------------
        $fechaInicioObj = Carbon::parse($fechaInicio);
        $fechaFinObj = Carbon::parse($fechaFin);
        $diasDiferencia = $fechaInicioObj->diffInDays($fechaFinObj);
        $mesesEs = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $etiquetasBarras = []; $ventasBarras = []; $gananciasBarras = []; $gastosBarras = []; 
        $tituloGraficoBarras = '';
        $tituloTablaTemporal = '';

        if ($modoAgrupacion === 'trimestre') {
            $tituloGraficoBarras = 'Rendimiento Trimestral';
            $tituloTablaTemporal = 'Trimestre';
            $nombresTrimestres = [1 => '(Ene - Mar)', 2 => '(Abr - Jun)', 3 => '(Jul - Sep)', 4 => '(Oct - Dic)'];
            
            for ($date = $fechaInicioObj->copy()->startOfQuarter(); $date->lte($fechaFinObj); $date->addQuarter()) {
                $numTrimestre = ceil($date->format('n') / 3);
                $etiqueta = 'Trimestre ' . $numTrimestre . ' ' . $nombresTrimestres[$numTrimestre] . ' ' . $date->format('Y');
                $etiquetasBarras[] = $etiqueta;
                $ventasBarras[$etiqueta] = 0; $gananciasBarras[$etiqueta] = 0; $gastosBarras[$etiqueta] = 0;
            }
            foreach ($ticketsCompletados as $ticket) {
                $numTrimestre = ceil($ticket->created_at->format('n') / 3);
                $etiqueta = 'Trimestre ' . $numTrimestre . ' ' . $nombresTrimestres[$numTrimestre] . ' ' . $ticket->created_at->format('Y');
                if (isset($ventasBarras[$etiqueta])) {
                    $ventasBarras[$etiqueta] += $ticket->total;
                    foreach ($ticket->detalles as $d) {
                        if ($d->producto) {
                            $c = $d->producto->precio_compra * $d->cantidad; $v = $d->precio_unitario * $d->cantidad;
                            $gastosBarras[$etiqueta] += $c; $gananciasBarras[$etiqueta] += ($v - $c);
                        }
                    }
                }
            }
        } elseif ($modoAgrupacion === 'semestre') {
            $tituloGraficoBarras = 'Rendimiento Semestral';
            $tituloTablaTemporal = 'Semestre';
            $nombresSemestres = [1 => '(Ene - Jun)', 2 => '(Jul - Dic)'];

            for ($date = $fechaInicioObj->copy()->startOfYear(); $date->lte($fechaFinObj); $date->addMonths(6)) {
                $numSemestre = ceil($date->format('n') / 6);
                $etiqueta = 'Semestre ' . $numSemestre . ' ' . $nombresSemestres[$numSemestre] . ' ' . $date->format('Y');
                $etiquetasBarras[] = $etiqueta;
                $ventasBarras[$etiqueta] = 0; $gananciasBarras[$etiqueta] = 0; $gastosBarras[$etiqueta] = 0;
            }
            foreach ($ticketsCompletados as $ticket) {
                $numSemestre = ceil($ticket->created_at->format('n') / 6);
                $etiqueta = 'Semestre ' . $numSemestre . ' ' . $nombresSemestres[$numSemestre] . ' ' . $ticket->created_at->format('Y');
                if (isset($ventasBarras[$etiqueta])) {
                    $ventasBarras[$etiqueta] += $ticket->total;
                    foreach ($ticket->detalles as $d) {
                        if ($d->producto) {
                            $c = $d->producto->precio_compra * $d->cantidad; $v = $d->precio_unitario * $d->cantidad;
                            $gastosBarras[$etiqueta] += $c; $gananciasBarras[$etiqueta] += ($v - $c);
                        }
                    }
                }
            }
        } elseif ($modoAgrupacion === 'anual' || $diasDiferencia >= 365) {
            $tituloGraficoBarras = 'Rendimiento Anual';
            $tituloTablaTemporal = 'Año';
            for ($date = $fechaInicioObj->copy()->startOfYear(); $date->lte($fechaFinObj); $date->addYear()) {
                $etiqueta = $date->format('Y');
                $etiquetasBarras[] = $etiqueta;
                $ventasBarras[$etiqueta] = 0; $gananciasBarras[$etiqueta] = 0; $gastosBarras[$etiqueta] = 0;
            }
            foreach ($ticketsCompletados as $ticket) {
                $etiqueta = $ticket->created_at->format('Y');
                if (isset($ventasBarras[$etiqueta])) {
                    $ventasBarras[$etiqueta] += $ticket->total;
                    foreach ($ticket->detalles as $d) {
                        if ($d->producto) {
                            $c = $d->producto->precio_compra * $d->cantidad; $v = $d->precio_unitario * $d->cantidad;
                            $gastosBarras[$etiqueta] += $c; $gananciasBarras[$etiqueta] += ($v - $c);
                        }
                    }
                }
            }
        } elseif ($modoAgrupacion === 'mes' || ($diasDiferencia > 60 && $diasDiferencia < 365)) {
            $tituloGraficoBarras = 'Rendimiento Mensual';
            $tituloTablaTemporal = 'Mes';
            for ($date = $fechaInicioObj->copy()->startOfMonth(); $date->lte($fechaFinObj); $date->addMonth()) {
                $etiqueta = $mesesEs[$date->format('n')] . ' ' . $date->format('Y');
                $etiquetasBarras[] = $etiqueta;
                $ventasBarras[$etiqueta] = 0; $gananciasBarras[$etiqueta] = 0; $gastosBarras[$etiqueta] = 0;
            }
            foreach ($ticketsCompletados as $ticket) {
                $etiqueta = $mesesEs[$ticket->created_at->format('n')] . ' ' . $ticket->created_at->format('Y');
                if (isset($ventasBarras[$etiqueta])) {
                    $ventasBarras[$etiqueta] += $ticket->total;
                    foreach ($ticket->detalles as $d) {
                        if ($d->producto) {
                            $c = $d->producto->precio_compra * $d->cantidad; $v = $d->precio_unitario * $d->cantidad;
                            $gastosBarras[$etiqueta] += $c; $gananciasBarras[$etiqueta] += ($v - $c);
                        }
                    }
                }
            }
        } else {
            $tituloGraficoBarras = 'Rendimiento Diario';
            $tituloTablaTemporal = 'Día';
            for ($date = $fechaInicioObj->copy(); $date->lte($fechaFinObj); $date->addDay()) {
                $etiqueta = $date->format('d') . ' ' . $mesesEs[$date->format('n')];
                $etiquetasBarras[] = $etiqueta;
                $ventasBarras[$etiqueta] = 0; $gananciasBarras[$etiqueta] = 0; $gastosBarras[$etiqueta] = 0;
            }
            foreach ($ticketsCompletados as $ticket) {
                $etiqueta = $ticket->created_at->format('d') . ' ' . $mesesEs[$ticket->created_at->format('n')];
                if (isset($ventasBarras[$etiqueta])) {
                    $ventasBarras[$etiqueta] += $ticket->total;
                    foreach ($ticket->detalles as $d) {
                        if ($d->producto) {
                            $c = $d->producto->precio_compra * $d->cantidad; $v = $d->precio_unitario * $d->cantidad;
                            $gastosBarras[$etiqueta] += $c; $gananciasBarras[$etiqueta] += ($v - $c);
                        }
                    }
                }
            }
        }

        $tablaTemporal = [];
        foreach ($etiquetasBarras as $etiqueta) {
            $tablaTemporal[] = [
                'periodo' => $etiqueta, 'ingresos' => $ventasBarras[$etiqueta], 'costos' => $gastosBarras[$etiqueta], 'ganancia' => $gananciasBarras[$etiqueta]
            ];
        }

        $graficoBarras = $request->input('grafico_barras_base64');
        $graficoDona = $request->input('grafico_dona_base64');

        $pdf = Pdf::loadView('reportes.pdf', compact(
            'fechaInicio', 'fechaFin', 'ventasTotales', 'gananciaNeta','costosTotales', 
            'totalTickets', 'ticketsEntregados', 'nuevosUsuarios', 
            'productosVendidos', 'productosCeroVentas', 'ventasPorCategoria',
            'graficoBarras', 'graficoDona', 'tablaTemporal', 'tituloGraficoBarras', 'tituloTablaTemporal'
        ));

        return $pdf->download('Reporte_Ventas_'.$fechaInicio.'_al_'.$fechaFin.'.pdf');
    }
}