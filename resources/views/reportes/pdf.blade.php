<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas</title>
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
        
        .footer { margin-top: 40px; font-size: 10px; text-align: center; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>LICORERÍA WEB STORE</h1>
        <p>Reporte de Rendimiento Financiero y Operativo</p>
        <p><strong>Período:</strong> {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</p>
    </div>

    <table class="resumen-box">
        <tr>
            <td>
                <span class="titulo">Ingresos (Ventas)</span>
                <span class="valor primary">${{ number_format($ventasTotales, 2) }}</span>
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

    @if(isset($graficoBarras) && $graficoBarras)
        <div class="seccion-titulo">Rendimiento Financiero (Visual)</div>
        <div style="text-align: center; margin-bottom: 30px; background-color: #f9f9f9; border: 1px solid #eee; padding: 10px; border-radius: 8px;">
            <img src="{{ $graficoBarras }}" style="width: 100%; max-height: 250px; object-fit: contain;">
        </div>
    @endif

    @if(isset($graficoDona) && $graficoDona)
        <div class="seccion-titulo">Distribución de Ventas por Categoría</div>
        <div style="text-align: center; margin-bottom: 35px; background-color: #f9f9f9; border: 1px solid #eee; padding: 15px; border-radius: 8px;">
            <img src="{{ $graficoDona }}" style="width: 70%; max-height: 220px; object-fit: contain; margin: 0 auto;">
        </div>
    @endif

    <div class="seccion-titulo">Detalle de Licores Vendidos (1 o más unidades)</div>
    <table class="datos">
        <thead>
            <tr>
                <th style="width: 10%;" class="text-center">Posición</th>
                <th style="width: 65%;">Producto</th>
                <th style="width: 25%;" class="text-center">Unidades Vendidas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productosVendidos as $index => $item)
                <tr>
                    <td class="text-center fw-bold">#{{ $index + 1 }}</td>
                    <td>{{ $item->producto->nombre ?? 'Producto Eliminado' }}</td>
                    <td class="text-center text-success">{{ $item->total_vendido }} unid.</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">No hay ventas registradas en este período.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="seccion-titulo" style="color: #dc3545;">Atención: Licores Sin Movimiento (0 Ventas)</div>
    <table class="datos">
        <thead>
            <tr>
                <th style="width: 10%;" class="text-center">Ítem</th>
                <th style="width: 65%;">Producto</th>
                <th style="width: 25%;" class="text-center">Unidades Vendidas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productosCeroVentas as $index => $producto)
                <tr>
                    <td class="text-center fw-bold">-</td>
                    <td>{{ $producto->nombre }}</td>
                    <td class="text-center text-danger">0 unid.</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">¡Excelente! Todo el inventario tuvo al menos 1 venta.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="seccion-titulo">Resumen de Ventas por Categoría</div>
    <table class="datos" style="width: 70%;">
        <thead>
            <tr>
                <th style="width: 70%;">Nombre de Categoría</th>
                <th style="width: 30%;" class="text-center">Total Vendidas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventasPorCategoria as $categoria => $cantidad)
                <tr>
                    <td>{{ $categoria }}</td>
                    <td class="text-center fw-bold @if($cantidad == 0) text-danger @else text-success @endif">
                        {{ $cantidad }} unid.
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-center">No hay datos de categorías en este período.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Documento generado automáticamente por el Sistema Administrativo de Licorería Web Store.<br>
        Fecha de emisión: {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>