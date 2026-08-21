<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Auditoría</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            color: #1a1a1a;
        }

        .header p {
            margin: 5px 0;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: #343a40;
            color: #ffffff;
            padding: 8px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }

        td {
            border-bottom: 1px solid #ddd;
            padding: 8px;
            vertical-align: top;
        }

        .badge {
            background-color: #e9ecef;
            padding: 3px 6px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 9px;
        }

        .text-success {
            color: #198754;
        }

        .text-danger {
            color: #dc3545;
        }

        .text-muted {
            color: #6c757d;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>REPORTE DE AUDITORÍA DEL SISTEMA</h2>
        <p>Generado el: {{ \Carbon\Carbon::now()->format('d/m/Y h:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="12%">Fecha / Hora</th>
                <th width="14%">Responsable</th>
                <th width="12%">Módulo</th>
                <th width="18%">Afectado / Referencia</th>
                <th width="12%">Evento</th>
                <th width="32%">Descripción de la Acción</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    {{-- 1. FECHA --}}
                    <td>{{ $log->created_at->format('d/m/Y h:i A') }}</td>

                    {{-- 2. RESPONSABLE --}}
                    <td>
                        @if ($log->causer)
                            <strong>{{ $log->causer->name }}</strong><br>
                            <span class="text-muted">Rol: {{ strtoupper($log->causer->rol) }}</span>
                        @else
                            <strong>SISTEMA</strong><br>
                            <span class="text-muted">Automático</span>
                        @endif
                    </td>

                    {{-- 3. MÓDULO --}}
                    <td>{{ strtoupper($log->log_name) }}</td>

                    {{-- 4. AFECTADO / REFERENCIA --}}
                    <td>
                        @if ($log->subject_type)
                            @php
                                $modelo = class_basename($log->subject_type);
                                $nombreDescriptivo = '#' . $log->subject_id;

                                if ($log->subject) {
                                    if ($modelo === 'TurnoCaja') {
                                        $nombreDescriptivo = 'Turno Actual';
                                    } else {
                                        $nombreDescriptivo =
                                            $log->subject->nombre ??
                                            ($log->subject->name ??
                                                ($log->subject->codigo_reserva ?? $nombreDescriptivo));
                                    }
                                } else {
                                    $atributosGuardados =
                                        $log->properties->get('old') ?? ($log->properties->get('attributes') ?? []);
                                    if (!empty($atributosGuardados)) {
                                        if ($modelo === 'TurnoCaja') {
                                            $nombreDescriptivo = 'Turno Cerrado';
                                        } else {
                                            $nombre =
                                                $atributosGuardados['nombre'] ??
                                                ($atributosGuardados['name'] ??
                                                    ($atributosGuardados['codigo_reserva'] ?? null));
                                            if ($nombre) {
                                                $nombreDescriptivo = $nombre;
                                            }
                                        }
                                    }
                                }
                            @endphp
                            <strong>{{ $modelo }}</strong><br>
                            <span class="text-muted">{{ $nombreDescriptivo }}</span>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>

                    {{-- 5. EVENTO --}}
                    <td>
                        @php
                            $textoAccionPdf = match ($log->event) {
                                'created' => 'CREADO',
                                'updated' => 'ACTUALIZADO',
                                'deleted' => 'ELIMINADO',
                                'reserva_online' => 'RESERVA ONLINE',
                                default => strtoupper(str_replace('_', ' ', $log->event)),
                            };
                        @endphp
                        {{ $textoAccionPdf }}
                    </td>

                    {{-- 6. DESCRIPCIÓN DETALLADA --}}
                    <td>
                        @php
                            $diccionario = [
                                'name' => 'Nombre',
                                'email' => 'Correo',
                                'cedula' => 'Cédula',
                                'rol' => 'Rol',
                                'estado' => 'Estado',
                                'password' => 'Contraseña',
                                'precio_compra' => 'Precio Compra',
                                'precio' => 'Precio Venta',
                                'stock' => 'Stock',
                                'descripcion' => 'Descripción',
                                'categoria_id' => 'Categoría',
                                'codigo_reserva' => 'Ticket',
                                'total' => 'Total',
                                'metodo_pago' => 'Método Pago',
                            ];

                            $descripcionPrincipal = $log->description;
                            if (in_array($descripcionPrincipal, ['created', 'updated', 'deleted'])) {
                                $descripcionPrincipal = match ($descripcionPrincipal) {
                                    'created' => 'Registro creado en el sistema.',
                                    'updated' => 'Registro actualizado.',
                                    'deleted' => 'Registro eliminado.',
                                };
                            }
                        @endphp

                        <strong>{{ $descripcionPrincipal }}</strong>

                        {{-- ELIMINACIONES --}}
                        @if ($log->event === 'deleted')
                            @php $datosBorrados = $log->properties->get('old') ?? ($log->properties->get('attributes') ?? []); @endphp
                            @if (!empty($datosBorrados))
                                <ul style="margin: 5px 0; padding-left: 15px; color: #666; font-size: 10px;">
                                    @foreach ($datosBorrados as $key => $value)
                                        @if (!is_array($value) && !empty($value) && !in_array($key, ['id', 'created_at', 'updated_at']))
                                            @php $nombreCampo = $diccionario[$key] ?? ucfirst(str_replace('_', ' ', $key)); @endphp
                                            <li>{{ $nombreCampo }}: {{ $value }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            @endif

                            {{-- PERMISOS ACTUALIZADOS --}}
                        @elseif($log->event === 'permisos_actualizados')
                            @php
                                $agregados = $log->properties->get('agregados') ?? [];
                                $removidos = $log->properties->get('removidos') ?? [];
                            @endphp
                            @if (count($agregados) > 0)
                                <br><span class="text-success">+ Permisos asignados:
                                    {{ implode(', ', $agregados) }}</span>
                            @endif
                            @if (count($removidos) > 0)
                                <br><span class="text-danger">- Permisos quitados:
                                    {{ implode(', ', $removidos) }}</span>
                            @endif

                            {{-- RESERVAS Y CAMBIOS DE STOCK --}}
                        @elseif(in_array($log->event, ['reserva_online', 'devolucion_automatica', 'devolucion_manual', 'venta_pos']))
                            @if ($log->properties->has('old') && $log->properties->has('attributes'))
                                <br><span style="color: #444;">Inventario: <span
                                        style="text-decoration: line-through; color: #dc3545;">{{ $log->properties->get('old')['stock'] ?? 'N/A' }}</span>
                                    -> <strong
                                        style="color: #198754;">{{ $log->properties->get('attributes')['stock'] ?? 'N/A' }}</strong></span>
                            @endif

                            {{-- EDICIONES REGULARES --}}
                        @elseif($log->properties->has('old') && $log->properties->has('attributes'))
                            <ul style="margin: 5px 0; padding-left: 15px; color: #444; font-size: 10px;">
                                @foreach ($log->properties->get('attributes') as $key => $newValue)
                                    @php $oldValue = $log->properties->get('old')[$key] ?? 'N/A'; @endphp
                                    @if ($oldValue != $newValue && $key !== 'updated_at')
                                        @php $nombreCampo = $diccionario[$key] ?? mb_strtolower(str_replace('_', ' ', $key)); @endphp
                                        <li>Cambió <strong>{{ $nombreCampo }}</strong> de <span
                                                class="text-danger">"{{ $oldValue }}"</span> a <span
                                                class="text-success">"{{ $newValue }}"</span></li>
                                    @endif
                                @endforeach
                            </ul>

                            {{-- CREACIONES NUEVAS --}}
                        @elseif($log->properties->has('attributes'))
                            <ul style="margin: 5px 0; padding-left: 15px; color: #666; font-size: 10px;">
                                @foreach ($log->properties->get('attributes') as $key => $value)
                                    @if (!is_array($value) && !empty($value) && !in_array($key, ['id', 'updated_at']))
                                        @php $nombreCampo = $diccionario[$key] ?? ucfirst(str_replace('_', ' ', $key)); @endphp
                                        <li>{{ $nombreCampo }}: {{ $value }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">No hay registros en este rango.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>
