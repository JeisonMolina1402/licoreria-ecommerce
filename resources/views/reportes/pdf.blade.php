<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Rendimiento</title>
    
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; font-size: 12px; margin: 0; padding: 15px; }
        
        /* CABECERA */
        .header { border-bottom: 2px solid #b07d00; padding-bottom: 10px; margin-bottom: 20px; text-align: center; }
        .header h1 { margin: 0; color: #111; text-transform: uppercase; letter-spacing: 1px; font-size: 20px; }
        .header p { margin: 5px 0 0 0; color: #666; font-size: 12px; }
        
        /* TARJETAS RESUMEN (KPIs) */
        .resumen-box { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .resumen-box td { padding: 10px; text-align: center; border: 1px solid #ddd; background-color: #f9f9f9; width: 20%; vertical-align: top;}
        .resumen-box .titulo { display: block; font-size: 9px; text-transform: uppercase; color: #777; margin-bottom: 5px; font-weight: bold;}
        .resumen-box .valor { display: block; font-size: 16px; font-weight: bold; color: #111; }
        .resumen-box .detalle { display: block; font-size: 10px; color: #555; margin-top: 4px; }
        
        .valor.success { color: #198754; }
        .valor.primary { color: #0d6efd; }
        .valor.purple { color: #6f42c1; }
        .valor.warning { color: #ffc107; }
        .valor.danger { color: #dc3545; }

        /* ESTADOS DE RESERVA */
        .estado-reservas { width: 100%; margin-bottom: 25px; border-collapse: collapse; background-color: #fff; }
        .estado-reservas td { padding: 8px; border: 1px solid #ddd; font-size: 11px; }
        .estado-bar { height: 6px; border-radius: 3px; margin-top: 3px; }

        /* TÍTULOS DE SECCIÓN */
        .seccion-titulo { font-size: 13px; border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-bottom: 15px; margin-top: 25px; color: #444; font-weight: bold; text-transform: uppercase;}
        
        /* GRÁFICOS */
        .contenedor-grafico { text-align: center; margin-bottom: 20px; background-color: #fcfcfc; border: 1px solid #eee; padding: 10px; border-radius: 5px; page-break-inside: avoid; }
        .contenedor-grafico img { width: 100%; max-height: 220px; object-fit: contain; }

        /* TABLAS DE DATOS (Anti-cortes) */
        table.datos { width: 100%; border-collapse: collapse; margin-bottom: 20px; page-break-inside: auto; }
        table.datos tr { page-break-inside: avoid; page-break-after: auto; }
        table.datos thead { display: table-header-group; }
        table.datos tfoot { display: table-footer-group; }
        table.datos th { background-color: #f2f2f2; border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 10px; text-transform: uppercase; color: #555;}
        table.datos td { border: 1px solid #ddd; padding: 7px 8px; font-size: 11px; }
        
        /* UTILIDADES */
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .text-danger { color: #dc3545; font-weight: bold; }
        .text-success { color: #198754; font-weight: bold; }
        .text-primary { color: #0d6efd; font-weight: bold; }    
        .fw-bold { font-weight: bold; }
        
        .footer { margin-top: 40px; font-size: 10px; text-align: center; color: #999; border-top: 1px solid #eee; padding-top: 10px; }

        /* CUIDA QUE LOS ELEMENTOS DENTRO NO SE SEPAREN DE PÁGINA */
        .evitar-salto { page-break-inside: avoid; }
    </style>
</head>
<body>

    <div class="header">
        <h1>LICORERÍA WEB STORE</h1>
        <p>Reporte de Rendimiento Financiero y Operativo</p>
        <p><strong>Período:</strong> {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</p>
    </div>

    <!-- 1. CAJAS DE RESUMEN (KPIs) -->
    <table class="resumen-box">
        <tr>
            <td>
                <span class="titulo">Ingresos (Ventas)</span>
                <span class="valor primary">${{ number_format($ventasTotales, 2) }}</span>
            </td>
            <td>
                <span class="titulo">Costos (Inversión)</span>
                <span class="valor danger">${{ number_format($costosTotales, 2) }}</span>
            </td>
            <td>
                <span class="titulo">Ganancia Neta</span>
                <span class="valor success">${{ number_format($gananciaNeta, 2) }}</span>
            </td>
            <td>
                <span class="titulo">Entregas / Tickets</span>
                <span class="valor">{{ $ticketsEntregados }} <small style="font-size: 13px; font-weight: normal; color: #888;">/ {{ $totalTickets }}</small></span>
            </td>
            <td>
                <span class="titulo">Nuevos Clientes</span>
                <span class="valor purple">+{{ $nuevosUsuarios }}</span>
            </td>
        </tr>
    </table>

    <!-- 2. ESTADO DE RESERVAS -->
    @php
        $porcEntregados = $totalTickets > 0 ? round(($ticketsEntregados / $totalTickets) * 100) : 0;
        $porcCancelados = $totalTickets > 0 ? round(($ticketsCancelados / $totalTickets) * 100) : 0;
    @endphp
    <table class="estado-reservas">
        <tr>
            <td style="width: 50%;">
                <div style="display: flex; justify-content: space-between;">
                    <strong><span style="color: #198754;">Entregados (Completados)</span></strong>
                    <strong>{{ $porcEntregados }}%</strong>
                </div>
                <div class="detalle">{{ $ticketsEntregados }} tickets de {{ $totalTickets }} en total</div>
                <div class="estado-bar" style="background-color: #e9ecef; width: 100%;"><div style="background-color: #198754; width: {{ $porcEntregados }}%; height: 100%; border-radius: 3px;"></div></div>
            </td>
            <td style="width: 50%;">
                <div style="display: flex; justify-content: space-between;">
                    <strong><span style="color: #dc3545;">Cancelados / Vencidos</span></strong>
                    <strong>{{ $porcCancelados }}%</strong>
                </div>
                <div class="detalle">{{ $ticketsCancelados }} tickets de {{ $totalTickets }} en total</div>
                <div class="estado-bar" style="background-color: #e9ecef; width: 100%;"><div style="background-color: #dc3545; width: {{ $porcCancelados }}%; height: 100%; border-radius: 3px;"></div></div>
            </td>
        </tr>
    </table>

    <!-- 3. GRÁFICO FINANCIERO Y TABLA -->
    @if(isset($graficoBarras) && $graficoBarras)
        <!-- ENVUELTO EN EVITAR SALTO -->
        <div class="evitar-salto">
            <div class="seccion-titulo">Rendimiento Financiero General</div>
            <div class="contenedor-grafico">
                <img src="{{ $graficoBarras }}">
            </div>
        </div>
    @endif

    <div class="seccion-titulo">Desglose Financiero por {{ $tituloGraficoBarras == 'Rendimiento Diario' ? 'Día' : ($tituloGraficoBarras == 'Rendimiento Mensual' ? 'Mes' : 'Período') }}</div>
    <table class="datos">
        <thead>
            <tr>
                <th style="width: 25%;">Período</th>
                <th class="text-center">Costos (Inversión)</th>
                <th class="text-center">Ingresos (Ventas)</th>
                <th class="text-center">Ganancia Neta</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tablaTemporal as $fila)
                @if($fila['ingresos'] > 0 || $fila['costos'] > 0) 
                    <tr>
                        <td class="fw-bold">{{ $fila['periodo'] }}</td>
                        <td class="text-center text-danger">${{ number_format($fila['costos'], 2) }}</td>
                        <td class="text-center text-primary">${{ number_format($fila['ingresos'], 2) }}</td>
                        <td class="text-center text-success">${{ number_format($fila['ganancia'], 2) }}</td>
                    </tr>
                @endif
            @empty
                <tr><td colspan="4" class="text-center">No hay desglose financiero en este período.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- 4. DETALLE DE LICORES VENDIDOS -->
    <div class="seccion-titulo">Detalle de Licores Vendidos (1 o más unidades)</div>
    <table class="datos">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th>Producto</th>
                <th>Categoría</th>
                <th class="text-center">P. Compra</th>
                <th class="text-center">P. Venta</th>
                <th class="text-center">Inversión</th>
                <th class="text-center">Total Venta</th>
                <th class="text-center">Ganancia</th>
                <th class="text-center">Unidades</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productosVendidos as $index => $item)
                @php
                    $unidades = $item->total_vendido ?? 0;
                    $precioCompra = $item->producto->precio_compra ?? 0;
                    $precioVenta = $item->producto->precio ?? 0;
                    $totalVenta = $item->ingreso_generado ?? 0; 
                    $ganancia = $item->ganancia_generada ?? 0;  
                    $totalInversion = $totalVenta - $ganancia;  
                @endphp
                <tr>
                    <td class="text-center fw-bold">{{ $index + 1 }}</td>
                    <td>{{ $item->producto->nombre ?? 'Producto Eliminado' }}</td>
                    <td>{{ $item->producto->categoria->nombre ?? 'Sin Categoría' }}</td>
                    <td class="text-center">${{ number_format($precioCompra, 2) }}</td>
                    <td class="text-center">${{ number_format($precioVenta, 2) }}</td>
                    <td class="text-center text-danger">${{ number_format($totalInversion, 2) }}</td>
                    <td class="text-center text-primary">${{ number_format($totalVenta, 2) }}</td>
                    <td class="text-center text-success">${{ number_format($ganancia, 2) }}</td>
                    <td class="text-center text-success fw-bold">{{ $unidades }} unid.</td>
                </tr>
            @empty
                <tr><td colspan="9" class="text-center">No hay ventas registradas en este período.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- 5. INVENTARIO MUERTO -->
    @if(count($productosCeroVentas) > 0)
        <div class="seccion-titulo" style="color: #dc3545; margin-top: 30px;">Atención: Licores Sin Movimiento (0 Ventas)</div>
        <table class="datos">
            <thead>
                <tr>
                    <th class="text-center">-</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th class="text-center">P. Compra</th>
                    <th class="text-center">P. Venta</th>
                    <th class="text-center">Unidades Vendidas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productosCeroVentas as $producto)
                    <tr>
                        <td class="text-center fw-bold">-</td>
                        <td>{{ $producto->nombre }}</td>
                        <td>{{ $producto->categoria->nombre ?? 'Sin Categoría' }}</td>
                        <td class="text-center">${{ number_format($producto->precio_compra, 2) }}</td>
                        <td class="text-center">${{ number_format($producto->precio, 2) }}</td>
                        <td class="text-center text-danger fw-bold">0 unid.</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- 6. RENDIMIENTO POR CATEGORÍAS (GRÁFICO + TABLA) -->
    @if(isset($graficoDona) && $graficoDona)
        <!-- ENVUELTO EN EVITAR SALTO -->
        <div class="evitar-salto">
            <div class="seccion-titulo">Distribución de Ventas por Categoría</div>
            <div class="contenedor-grafico" style="background-color: #fff; border: none;">
                <img src="{{ $graficoDona }}" style="max-height: 250px; width: auto;">
            </div>
        </div>
    @endif

    <table class="datos">
        <thead>
            <tr>
                <th style="width: 30%;">Categoría</th>
                <th class="text-center">Total Inversión</th>
                <th class="text-center">Total Venta</th>
                <th class="text-center">Ganancia</th>
                <th class="text-center">Unidades</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventasPorCategoria as $categoria => $datos)
                <tr>
                    <td class="fw-bold">{{ $categoria }}</td>
                    <td class="text-center text-danger">${{ number_format($datos['inversion'], 2) }}</td>
                    <td class="text-center text-primary">${{ number_format($datos['ventas'], 2) }}</td>
                    <td class="text-center text-success">${{ number_format($datos['ganancia'], 2) }}</td>
                    <td class="text-center fw-bold @if($datos['unidades'] == 0) text-danger @else text-success @endif">
                        {{ $datos['unidades'] }} unid.
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">No hay datos de categorías en este período.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- 7. RANKING DE VENDEDORES -->
    <!-- ENVUELTO EN EVITAR SALTO -->
    <div class="evitar-salto">
        <div class="seccion-titulo" style="color: #b07d00;">Ranking de Vendedores (Bonos y Comisiones)</div>
        @if(isset($graficoVendedores) && $graficoVendedores)
            <div class="contenedor-grafico" style="background-color: #fff; border: none; margin-bottom: 10px;">
                <img src="{{ $graficoVendedores }}" style="max-height: 180px; width: 80%;">
            </div>
        @endif
    </div>

    <table class="datos">
        <thead>
            <tr>
                <th class="text-center" style="width: 10%;">Posición</th>
                <th>Cajero / Vendedor</th>
                <th class="text-center">Tickets Gestionados</th>
                <th class="text-center">Total Recaudado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rendimientoVendedores as $index => $vendedor)
                <tr>
                    <td class="text-center fw-bold">{{ $index + 1 }}</td>
                    <td class="fw-bold">{{ $vendedor->vendedor ? $vendedor->vendedor->name : 'Usuario Eliminado' }}</td>
                    <td class="text-center">{{ $vendedor->total_tickets }}</td>
                    <td class="text-center text-success fw-bold">${{ number_format($vendedor->total_recaudado, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center">No hay datos de vendedores en este período.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- 8. CRECIMIENTO DE NUEVOS CLIENTES -->
    @php
        $labelsUsuarios = json_decode($nombresBarras, true) ?? [];
        $dataUsuarios = json_decode($datosUsuariosBarras, true) ?? [];
        $hayClientes = array_sum($dataUsuarios) > 0;
    @endphp

    <!-- ENVUELTO EN EVITAR SALTO -->
    <div class="evitar-salto">
        <div class="seccion-titulo" style="color: #6f42c1;">Crecimiento de Nuevos Clientes</div>
        @if(isset($graficoUsuarios) && $graficoUsuarios && $hayClientes)
            <div class="contenedor-grafico" style="background-color: #fff; border: none; margin-bottom: 10px;">
                <img src="{{ $graficoUsuarios }}" style="max-height: 180px;">
            </div>
        @endif
    </div>

    <table class="datos" style="width: 60%; margin: 0 auto;">
        <thead>
            <tr>
                <th>Período</th>
                <th class="text-center">Nuevos Clientes Registrados</th>
            </tr>
        </thead>
        <tbody>
            @if($hayClientes)
                @foreach($labelsUsuarios as $i => $label)
                    @if($dataUsuarios[$i] > 0)
                        <tr>
                            <td class="fw-bold">{{ $label }}</td>
                            <td class="text-center text-purple fw-bold">+{{ $dataUsuarios[$i] }} clientes</td>
                        </tr>
                    @endif
                @endforeach
            @else
                <tr><td colspan="2" class="text-center">No se registraron nuevos clientes en este período.</td></tr>
            @endif
        </tbody>
    </table>

    <!-- FOOTER DEL DOCUMENTO -->
    <div class="footer">
        Documento generado automáticamente por el Sistema Administrativo de Licorería Web Store.<br>
        Fecha de emisión: {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>