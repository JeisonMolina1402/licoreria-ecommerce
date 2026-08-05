<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Inventario</title>
    <style>
        /* Estilos específicos y compatibles con DomPDF */
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #212529;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #6c757d;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
        }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .text-danger { color: #dc3545; }
        .footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #adb5bd;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
        .page-number:after { content: counter(page); }
    </style>
</head>
<body>

    <!-- CABECERA DEL DOCUMENTO -->
    <div class="header">
        <h1>LICORERÍA WEB STORE</h1>
        <p>Reporte de Existencias e Inventario Valorado</p>
        <p>Fecha de emisión: {{ date('d/m/Y H:i') }}</p>
    </div>

    <!-- TABLA DE DATOS -->
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre del Producto</th>
                <th>Categoría</th>
                <th class="text-end">Precio Compra</th>
                <th class="text-end">Precio Venta</th>
                <th class="text-center">Stock</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $totalInversion = 0; 
                $totalVenta = 0;
            @endphp

            @forelse($productos as $index => $producto)
                @php 
                    // Cálculos para el resumen final
                    $totalInversion += ($producto->precio_compra * $producto->stock);
                    $totalVenta += ($producto->precio * $producto->stock);
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="fw-bold">{{ $producto->nombre }}</td>
                    <td>{{ $producto->categoria->nombre ?? 'Sin Categoría' }}</td>
                    <td class="text-end">${{ number_format($producto->precio_compra, 2) }}</td>
                    <td class="text-end">${{ number_format($producto->precio, 2) }}</td>
                    <td class="text-center fw-bold {{ $producto->stock <= 10 ? 'text-danger' : '' }}">
                        {{ $producto->stock }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No se encontraron productos con los filtros aplicados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- RESUMEN FINANCIERO (Un toque extra para impresionar al tutor) -->
    @if(count($productos) > 0)
    <div style="width: 50%; float: right;">
        <table>
            <tr>
                <th style="background-color: #343a40; color: white;">Resumen del Reporte</th>
                <th style="background-color: #343a40; color: white;" class="text-end">Total</th>
            </tr>
            <tr>
                <td>Total Inversión (Costo)</td>
                <td class="text-end fw-bold">${{ number_format($totalInversion, 2) }}</td>
            </tr>
            <tr>
                <td>Proyección de Ventas</td>
                <td class="text-end fw-bold">${{ number_format($totalVenta, 2) }}</td>
            </tr>
            <tr>
                <td>Ganancia Estimada</td>
                <td class="text-end fw-bold" style="color: green;">
                    ${{ number_format($totalVenta - $totalInversion, 2) }}
                </td>
            </tr>
        </table>
    </div>
    @endif

    <!-- PIE DE PÁGINA (Paginación automática) -->
    <div class="footer">
        Documento generado automáticamente por Licorería Web Store | Página <span class="page-number"></span>
    </div>

</body>
</html>