<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas</title>
    
    {{-- CSS INTERNO: Optimizado específicamente para motores como DomPDF. --}}
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; font-size: 13px; margin: 0; padding: 20px; }
        .header { border-bottom: 2px solid #b07d00; padding-bottom: 10px; margin-bottom: 25px; text-align: center; }
        .header h1 { margin: 0; color: #111; text-transform: uppercase; letter-spacing: 2px; font-size: 22px; }
        .header p { margin: 5px 0 0 0; color: #666; font-size: 13px; }
        
        .resumen-box { width: 100%; margin-bottom: 30px; border-collapse: collapse; }
        .resumen-box td { padding: 12px; text-align: center; border: 1px solid #ddd; background-color: #f9f9f9; width: 25%; }
        .resumen-box .titulo { display: block; font-size: 10px; text-transform: uppercase; color: #777; margin-bottom: 5px; font-weight: bold;}
        .resumen-box .valor { display: block; font-size: 18px; font-weight: bold; color: #111; }
        .resumen-box .valor.success { color: #198754; }
        .resumen-box .valor.primary { color: #0d6efd; }
        .resumen-box .valor.purple { color: #6f42c1; }

        .seccion-titulo { font-size: 14px; border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-bottom: 15px; margin-top: 25px; color: #444; font-weight: bold; text-transform: uppercase;}
        
        table.datos { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.datos th { background-color: #f2f2f2; border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 11px; text-transform: uppercase; color: #555;}
        table.datos td { border: 1px solid #ddd; padding: 8px 10px; font-size: 12px; }
        .text-center { text-align: center; }
        .text-danger { color: #dc3545; font-weight: bold; }
        .text-success { color: #198754; font-weight: bold; }
        .text-primary { color: #0d6efd; font-weight: bold; }    
        
        .footer { margin-top: 40px; font-size: 10px; text-align: center; color: #999; border-top: 1px solid #eee; padding-top: 10px; }

        /* NUEVA CLASE: Fuerza a DomPDF a no separar los elementos internos de este contenedor en dos páginas diferentes */
        .evitar-salto { page-break-inside: avoid; }
    </style>
</head>
<body>

    <div class="header">
        <h1>LICORERÍA WEB STORE</h1>
        <p>Reporte de Rendimiento Financiero y Operativo</p>
        
        <p><strong>Período:</strong> {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</p>
    </div>

    <!-- CAJAS DE RESUMEN (KPIs) -->
    <table class="resumen-box">
        <tr>
            <td>
                <span class="titulo">Ingresos (Ventas)</span>
                <span class="valor primary">${{ number_format($ventasTotales, 2) }}</span>
            </td>
            <td>
                <span class="titulo">Costos (Gastos de Inventario)</span>
                <span class="valor" style="color: red;">${{ number_format($costosTotales, 2) }}</span>
            </td>
            <td>
                <span class="titulo">Ganancia Neta</span>
                <span class="valor success">${{ number_format($gananciaNeta, 2) }}</span>
            </td>
            <td>
                <span class="titulo">Entregas / Tickets</span>
                <span class="valor">{{ $ticketsEntregados }} <small style="font-size: 11px; font-weight: normal; color: #666;">/ {{ $totalTickets }}</small></span>
            </td>
            <td>
                <span class="titulo">Nuevos Clientes</span>
                <span class="valor purple">+{{ $nuevosUsuarios }}</span>
            </td>
        </tr>
    </table>

    <!-- INYECCIÓN DEL GRÁFICO DE BARRAS -->
    @if(isset($graficoBarras) && $graficoBarras)
        <div class="evitar-salto">
            <div class="seccion-titulo">Rendimiento Financiero (Visual)</div>
            <div style="text-align: center; margin-bottom: 30px; background-color: #f9f9f9; border: 1px solid #eee; padding: 10px; border-radius: 8px;">
                <img src="{{ $graficoBarras }}" style="width: 100%; max-height: 250px; object-fit: contain;">
            </div>
        </div>
    @endif

    <!-- TABLA DE RESUMEN POR DÍA O MES (MOVIDA AQUÍ) -->
    <div class="evitar-salto">
        <div class="seccion-titulo">Desglose Financiero por {{ $tituloGraficoBarras == 'Rendimiento Diario' ? 'Día' : ($tituloGraficoBarras == 'Rendimiento Mensual' ? 'Mes' : 'Año') }}</div>
        <table class="datos" style="width: 100%;">
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
                    {{-- Usamos el if para ocultar los días que estuvieron en cero total (días sin actividad) --}}
                    @if($fila['ingresos'] > 0 || $fila['costos'] > 0) 
                        <tr>
                            <td class="fw-bold">{{ $fila['periodo'] }}</td>
                            <td class="text-center text-danger">${{ number_format($fila['costos'], 2) }}</td>
                            <td class="text-center text-primary">${{ number_format($fila['ingresos'], 2) }}</td>
                            <td class="text-center text-success">${{ number_format($fila['ganancia'], 2) }}</td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No hay desglose financiero en este período.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

   <!-- TABLA DE VENTAS EXITOSAS -->
    <div class="seccion-titulo">Detalle de Licores Vendidos (1 o más unidades)</div>
    <table class="datos">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Precio Compra</th>
                <th>Precio Venta</th>
                <th>Total Inversión</th>
                <th>Total Venta</th>
                <th>Ganancia</th>
                <th class="text-center">Unidades</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productosVendidos as $index => $item)
                @php
                    $unidades = $item->total_vendido ?? 0;
                    $precioCompra = $item->producto->precio_compra ?? 0;
                    $precioVenta = $item->producto->precio ?? 0;
                    
                    $totalVenta = $item->ingreso_generado ?? 0; // Ingreso real del SQL
                    $ganancia = $item->ganancia_generada ?? 0;  // Ganancia real del SQL
                    $totalInversion = $totalVenta - $ganancia;  // Inversión real
                @endphp
                <tr>
                    <td class="text-center fw-bold">{{ $index + 1 }}</td>
                    <td>{{ $item->producto->nombre ?? 'Producto Eliminado' }}</td>
                    <td>{{ $item->producto->categoria->nombre ?? 'Sin Categoría' }}</td>
                    <td>${{ number_format($precioCompra, 2) }}</td>
                    <td>${{ number_format($precioVenta, 2) }}</td>
                    <td class="text-danger">${{ number_format($totalInversion, 2) }}</td>
                    <td class="text-primary">${{ number_format($totalVenta, 2) }}</td>
                    <td class="text-success">${{ number_format($ganancia, 2) }}</td>
                    <td class="text-center text-success">{{ $unidades }} unid.</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">No hay ventas registradas en este período.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- TABLA DE ALERTA (INVENTARIO MUERTO) - AHORA CON EL DIV "EVITAR-SALTO" -->
    <div class="evitar-salto">
        <div class="seccion-titulo" style="color: #dc3545;">Atención: Licores Sin Movimiento (0 Ventas)</div>
        <table class="datos">
            <thead>
                <tr>
                    <th class="text-center">-</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Precio Compra</th>
                    <th>Precio Venta</th>
                    <th>Total Inversión</th>
                    <th>Total Venta</th>
                    <th>Ganancia</th>
                    <th class="text-center">Unidades</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productosCeroVentas as $index => $producto)
                    <tr>
                        <td class="text-center fw-bold">-</td>
                        <td>{{ $producto->nombre }}</td>
                        <td>{{ $producto->categoria->nombre ?? 'Sin Categoría' }}</td>
                        <td>${{ number_format($producto->precio_compra, 2) }}</td>
                        <td>${{ number_format($producto->precio, 2) }}</td>
                        <td class="text-danger">$0.00</td>
                        <td class="text-primary">$0.00</td>
                        <td class="text-success">$0.00</td>
                        <td class="text-center text-danger">0 unid.</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">¡Excelente! Todo el inventario tuvo al menos 1 venta.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- INYECCIÓN DEL GRÁFICO DE DONA -->
    @if(isset($graficoDona) && $graficoDona)
        <div class="evitar-salto">
            <div class="seccion-titulo">Distribución de Ventas por Categoría</div>
            <div style="text-align: center; margin-bottom: 35px; background-color: #f9f9f9; border: 1px solid #eee; padding: 15px; border-radius: 8px;">
                <img src="{{ $graficoDona }}" style="width: 70%; max-height: 220px; object-fit: contain; margin: 0 auto;">
            </div>
        </div>
    @endif

    <!-- TABLA DE RESUMEN POR CATEGORÍAS -->
    <div class="evitar-salto">
        <div class="seccion-titulo">Rendimiento Financiero por Categoría</div>
        <table class="datos" style="width: 100%;">
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
                    <tr>
                        <td colspan="5" class="text-center">No hay datos de categorías en este período.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- FOOTER DEL DOCUMENTO -->
    <div class="footer">
        Documento generado automáticamente por el Sistema Administrativo de Licorería Web Store.<br>
        Fecha de emisión: {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>