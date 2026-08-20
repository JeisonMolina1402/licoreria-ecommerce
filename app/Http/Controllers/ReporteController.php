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
        // Pedimos toda la data procesada a nuestra función privada
        $data = $this->generarDatosReporte($request, false);
        return view('reportes.index', $data);
    }
     
    // =========================================================================
    // MÉTODO 2: EXPORTAR PDF
    // =========================================================================
    public function exportarPdf(Request $request)
    {
        // Pedimos toda la data procesada (indicando true para que incluya tablas extra de PDF)
        $data = $this->generarDatosReporte($request, true);

        // Recibimos las fotos (Base64) de todos los gráficos enviadas desde Javascript
        $data['graficoBarras'] = $request->input('grafico_barras_base64');
        $data['graficoDona'] = $request->input('grafico_dona_base64');
        $data['graficoVendedores'] = $request->input('grafico_vendedores_base64');
        $data['graficoUsuarios'] = $request->input('grafico_usuarios_base64');

        $pdf = Pdf::loadView('reportes.pdf', $data);
        return $pdf->download('Reporte_Rendimiento_'.$data['fechaInicio'].'_al_'.$data['fechaFin'].'.pdf');
    }

    // =========================================================================
    // FUNCIÓN PRIVADA: MOTOR DE CÁLCULOS (DRY - No Repetir Código)
    // =========================================================================
    private function generarDatosReporte(Request $request, $esParaPdf = false)
    {
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->toDateString());
        $fechaFin = $request->input('fecha_fin', Carbon::now()->endOfMonth()->toDateString());
        $modoAgrupacion = $request->input('modo_agrupacion');

        $rangoFechas = [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'];
        $estadosValidos = ['pagado', 'listo', 'entregado'];

        // 1. OBTENER TICKETS Y CÁLCULOS GLOBALES
        $ticketsCompletados = Ticket::with('detalles.producto')
                                    ->whereIn('estado', $estadosValidos)
                                    ->whereBetween('created_at', $rangoFechas)->get();

        $ventasTotales = 0; $gananciaNeta = 0; $costosTotales = 0;

        foreach ($ticketsCompletados as $ticket) {
            $ventasTotales += $ticket->total;
            foreach ($ticket->detalles as $detalle) {
                $ingreso = $detalle->precio_unitario * $detalle->cantidad;
                $costo = $detalle->precio_compra * $detalle->cantidad;
                $gananciaNeta += ($ingreso - $costo);
                $costosTotales += $costo;
            }
        }

        $usuariosRegistrados = User::whereBetween('created_at', $rangoFechas)->where('rol', '!=', 'admin')->get();
        $nuevosUsuarios = $usuariosRegistrados->count();
        $totalTickets = Ticket::whereBetween('created_at', $rangoFechas)->count();
        $ticketsEntregados = $ticketsCompletados->count();
        $ticketsCancelados = Ticket::where('estado', 'cancelado')->whereBetween('created_at', $rangoFechas)->count();

        // 2. RENDIMIENTO POR VENDEDOR
        $rendimientoVendedores = Ticket::selectRaw('vendedor_id, COUNT(id) as total_tickets, SUM(total) as total_recaudado')
                                        ->whereIn('estado', $estadosValidos)->whereBetween('created_at', $rangoFechas)
                                        ->whereNotNull('vendedor_id')->groupBy('vendedor_id')
                                        ->with('vendedor')->orderByDesc('total_recaudado')->get();

        $nombresVendedores = $rendimientoVendedores->map(function($item) {
            return $item->vendedor ? explode(' ', $item->vendedor->name)[0] : 'Eliminado'; 
        })->toJson();
        $ventasVendedores = $rendimientoVendedores->pluck('total_recaudado')->toJson();

        // 3. AGRUPACIÓN TEMPORAL PARA GRÁFICOS (Barras y Líneas)
        $fInicio = Carbon::parse($fechaInicio);
        $fFin = Carbon::parse($fechaFin);
        $diasDiferencia = $fInicio->diffInDays($fFin);
        $mesesEs = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

        $etiquetas = []; $ventasB = []; $gananciasB = []; $gastosB = []; $usuariosB = [];

        // Lógica de saltos (Días, Meses, Trimestres, etc)
        if ($modoAgrupacion === 'trimestre') {
            $nombresTrimestres = [1 => '(Ene-Mar)', 2 => '(Abr-Jun)', 3 => '(Jul-Sep)', 4 => '(Oct-Dic)'];
            for ($d = $fInicio->copy()->startOfQuarter(); $d->lte($fFin); $d->addQuarter()) {
                $q = ceil($d->format('n') / 3);
                $et = 'Trim ' . $q . ' ' . $nombresTrimestres[$q] . ' ' . $d->format('Y');
                $etiquetas[] = $et; $ventasB[$et] = 0; $gananciasB[$et] = 0; $gastosB[$et] = 0; $usuariosB[$et] = 0;
            }
            $generarEtiqueta = function($date) use ($nombresTrimestres) {
                $q = ceil($date->format('n') / 3); return 'Trim ' . $q . ' ' . $nombresTrimestres[$q] . ' ' . $date->format('Y');
            };
            $tituloGraficoBarras = 'Rendimiento Trimestral'; $tituloTablaTemporal = 'Trimestre';
        } elseif ($modoAgrupacion === 'semestre') {
            $nombresSemestres = [1 => '(Ene-Jun)', 2 => '(Jul-Dic)'];
            for ($d = $fInicio->copy()->startOfYear(); $d->lte($fFin); $d->addMonths(6)) {
                $s = ceil($d->format('n') / 6);
                $et = 'Sem ' . $s . ' ' . $nombresSemestres[$s] . ' ' . $d->format('Y');
                $etiquetas[] = $et; $ventasB[$et] = 0; $gananciasB[$et] = 0; $gastosB[$et] = 0; $usuariosB[$et] = 0;
            }
            $generarEtiqueta = function($date) use ($nombresSemestres) {
                $s = ceil($date->format('n') / 6); return 'Sem ' . $s . ' ' . $nombresSemestres[$s] . ' ' . $date->format('Y');
            };
            $tituloGraficoBarras = 'Rendimiento Semestral'; $tituloTablaTemporal = 'Semestre';
        } elseif ($modoAgrupacion === 'anual' || $diasDiferencia >= 365) {
            for ($d = $fInicio->copy()->startOfYear(); $d->lte($fFin); $d->addYear()) {
                $et = $d->format('Y'); $etiquetas[] = $et; $ventasB[$et] = 0; $gananciasB[$et] = 0; $gastosB[$et] = 0; $usuariosB[$et] = 0;
            }
            $generarEtiqueta = function($date) { return $date->format('Y'); };
            $tituloGraficoBarras = 'Rendimiento Anual'; $tituloTablaTemporal = 'Año';
        } elseif ($modoAgrupacion === 'mes' || ($diasDiferencia > 60 && $diasDiferencia < 365)) {
            for ($d = $fInicio->copy()->startOfMonth(); $d->lte($fFin); $d->addMonth()) {
                $et = $mesesEs[$d->format('n')] . ' ' . $d->format('Y');
                $etiquetas[] = $et; $ventasB[$et] = 0; $gananciasB[$et] = 0; $gastosB[$et] = 0; $usuariosB[$et] = 0;
            }
            $generarEtiqueta = function($date) use ($mesesEs) { return $mesesEs[$date->format('n')] . ' ' . $date->format('Y'); };
            $tituloGraficoBarras = 'Rendimiento Mensual'; $tituloTablaTemporal = 'Mes';
        } else {
            for ($d = $fInicio->copy(); $d->lte($fFin); $d->addDay()) {
                $et = $d->format('d') . ' ' . $mesesEs[$d->format('n')];
                $etiquetas[] = $et; $ventasB[$et] = 0; $gananciasB[$et] = 0; $gastosB[$et] = 0; $usuariosB[$et] = 0;
            }
            $generarEtiqueta = function($date) use ($mesesEs) { return $date->format('d') . ' ' . $mesesEs[$date->format('n')]; };
            $tituloGraficoBarras = 'Rendimiento Diario'; $tituloTablaTemporal = 'Día';
        }

        // Poblar arrays
        foreach ($ticketsCompletados as $ticket) {
            $et = $generarEtiqueta($ticket->created_at);
            if (isset($ventasB[$et])) {
                $ventasB[$et] += $ticket->total;
                foreach ($ticket->detalles as $d) {
                    $c = $d->precio_compra * $d->cantidad; $v = $d->precio_unitario * $d->cantidad;
                    $gastosB[$et] += $c; $gananciasB[$et] += ($v - $c);
                }
            }
        }
        foreach ($usuariosRegistrados as $u) {
            $et = $generarEtiqueta($u->created_at);
            if (isset($usuariosB[$et])) $usuariosB[$et]++;
        }

        $nombresBarras = json_encode(array_values($etiquetas));
        $datosVentasBarras = json_encode(array_values($ventasB));
        $datosGananciasBarras = json_encode(array_values($gananciasB));
        $datosGastosBarras = json_encode(array_values($gastosB)); 
        $datosUsuariosBarras = json_encode(array_values($usuariosB));

        // Empaquetar todo lo necesario
        $datosFinales = compact(
            'fechaInicio', 'fechaFin', 'ventasTotales', 'gananciaNeta','costosTotales',
            'nuevosUsuarios', 'totalTickets', 'ticketsEntregados', 'ticketsCancelados',
            'tituloGraficoBarras', 'tituloTablaTemporal', 'nombresBarras', 'datosVentasBarras', 
            'datosGananciasBarras', 'datosGastosBarras', 'modoAgrupacion', 'rendimientoVendedores', 
            'nombresVendedores', 'ventasVendedores', 'datosUsuariosBarras'
        );

        // 4. BLOQUE EXTRA: SOLO SI ES PARA EL PDF (Top Productos y Categorías)
        if ($esParaPdf) {
            $tablaTemporal = [];
            foreach ($etiquetas as $et) {
                $tablaTemporal[] = ['periodo' => $et, 'ingresos' => $ventasB[$et], 'costos' => $gastosB[$et], 'ganancia' => $gananciasB[$et]];
            }
            $datosFinales['tablaTemporal'] = $tablaTemporal;

            $rankingProductos = $request->input('ranking_productos', 'ventas');
            $queryVendidos = DetalleTicket::selectRaw('producto_id, SUM(cantidad) as total_vendido, SUM(precio_unitario * cantidad) as ingreso_generado, SUM((precio_unitario - precio_compra) * cantidad) as ganancia_generada')
                ->whereHas('ticket', function($query) use ($rangoFechas, $estadosValidos) {
                    $query->whereIn('estado', $estadosValidos)->whereBetween('created_at', $rangoFechas);
                })->groupBy('producto_id')->with('producto.categoria');

            if ($rankingProductos === 'ganancia') { $queryVendidos->orderByDesc('ganancia_generada'); } else { $queryVendidos->orderByDesc('total_vendido'); }

            $productosVendidos = $queryVendidos->get();
            $idsVendidos = $productosVendidos->pluck('producto_id')->toArray();
            $datosFinales['productosVendidos'] = $productosVendidos;
            $datosFinales['productosCeroVentas'] = Producto::with('categoria')->whereNotIn('id', $idsVendidos)->orderBy('nombre', 'asc')->get();

            $rankingCategorias = $request->input('ranking_categorias', 'ventas');
            $detallesParaCategorias = DetalleTicket::whereHas('ticket', function($query) use ($rangoFechas, $estadosValidos) {
                    $query->whereIn('estado', $estadosValidos)->whereBetween('created_at', $rangoFechas);
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
            $datosFinales['ventasPorCategoria'] = $ventasPorCategoria;
        }

        return $datosFinales;
    }
}