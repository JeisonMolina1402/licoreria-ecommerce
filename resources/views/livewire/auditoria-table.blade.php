<div>
    <div>
        <div>
            <!-- BARRA DE FILTROS MEJORADA -->
            <div class="row g-2 mb-4 align-items-end">

                <!-- 1. Módulo -->
                <div class="col-lg-2 col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">Módulo</label>
                    <select wire:model.live="modulo" class="form-select shadow-sm">
                        <option value="">Todos</option>
                        @foreach ($modulos as $mod)
                            <option value="{{ $mod }}">{{ strtoupper($mod) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. Nivel de Acceso (Rol) -->
                <div class="col-lg-2 col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">Rol Responsable</label>
                    <select wire:model.live="filtroRol" class="form-select shadow-sm">
                        <option value="">Todos los roles</option>
                        <option value="admin">Administrador</option>
                        <option value="vendedor">Vendedor</option>
                        <option value="cliente">Cliente</option>
                    </select>
                </div>

                <!-- 3. Buscador de Texto (Reducido ligeramente para dar espacio al botón) -->
                <div class="col-lg-3 col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">Buscar Usuario</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><i
                                class="fa-solid fa-search text-muted"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="searchUsuario"
                            class="form-control border-start-0 ps-0" placeholder="Nombre o Cédula...">
                    </div>
                </div>

                <!-- 4. Fechas -->
                <div class="col-lg-2 col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">Desde Fecha</label>
                    <input type="date" wire:model.live="fechaInicio" class="form-control shadow-sm">
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">Hasta Fecha</label>
                    <input type="date" wire:model.live="fechaFin" class="form-control shadow-sm">
                </div>

                <!-- 5. Botón Limpiar -->
                <div class="col-lg-1 col-md-4">
                    <!-- Etiqueta invisible para mantener la alineación de alturas con los inputs -->
                    <label class="form-label small mb-1 d-none d-lg-block">&nbsp;</label>
                    <button wire:click="limpiarFiltros" class="btn btn-outline-danger w-100 shadow-sm"
                        title="Limpiar Filtros">
                        <i class="fa-solid fa-eraser"></i>
                    </button>
                </div>

            </div>

            <!-- TABLA ORIGINAL -->
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle text-sm bg-white shadow-sm">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Fecha y Hora</th>
                            <th scope="col">Usuario Responsable</th>
                            <th scope="col">Módulo</th>
                            <th scope="col">Afectado / Referencia</th>
                            <th scope="col">Acción</th>
                            <th scope="col">Detalles del Cambio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <!-- 1. COLUMNA: FECHA Y HORA -->
                                <td class="text-nowrap text-muted">
                                    {{ $log->created_at->format('d/m/Y h:i A') }}
                                </td>

                               <!-- 2. COLUMNA: USUARIO RESPONSABLE -->
                                <td>
                                    <div class="d-flex flex-column align-items-start">
                                        @if($log->causer)
                                            <span class="badge bg-secondary mb-1" style="font-size: 0.7rem;">
                                                <i class="fa-solid fa-id-badge me-1"></i> {{ strtoupper($log->causer->rol ?? 'Usuario') }}
                                            </span>
                                            <span class="fw-medium text-dark" style="font-size: 0.9rem;">
                                                <i class="fa-solid fa-user text-muted me-1"></i> {{ $log->causer->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-dark mb-1" style="font-size: 0.7rem;">
                                                <i class="fa-solid fa-robot me-1"></i> SISTEMA
                                            </span>
                                            <span class="fw-medium text-muted" style="font-size: 0.9rem;">
                                                Acción Automática
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- 3. COLUMNA: MÓDULO (Texto normalizado) -->
                                <td>
                                    <span class="text-muted fw-semibold" style="font-size: 0.9rem;">
                                        {{ ucfirst(strtolower($log->log_name)) }}
                                    </span>
                                </td>

                                <!-- 4. COLUMNA: SUJETO AFECTADO -->
                                <td>
                                    @if ($log->subject_type)
                                        @php
                                            $modelo = class_basename($log->subject_type);
                                            $icono = match ($modelo) {
                                                'Ticket' => '🎟️ Ticket',
                                                'Producto' => '📦 Producto',
                                                'User' => '👤 Usuario',
                                                'TurnoCaja' => '💰 Caja',
                                                'Categoria' => '🏷️ Categoría',
                                                default => '📄 ' . $modelo,
                                            };

                                            $nombreDescriptivo = '#' . $log->subject_id;

                                            if ($log->subject) {
                                                if ($modelo === 'TurnoCaja') {
                                                    $nombreDescriptivo = 'Turno Actual'; // Quitamos el #ID
                                                } else {
                                                    $nombreDescriptivo =
                                                        $log->subject->nombre ??
                                                        ($log->subject->name ??
                                                            ($log->subject->codigo_reserva ?? $nombreDescriptivo));
                                                }
                                            } else {
                                                $atributosGuardados =
                                                    $log->properties->get('old') ??
                                                    ($log->properties->get('attributes') ?? []);
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
                                        <span class="badge bg-light text-dark border shadow-sm px-2 py-1 fw-medium"
                                            style="font-size: 0.85rem;" title="ID Interno: {{ $log->subject_id }}">
                                            {{ $icono }}: <span class="fw-bold">{{ $nombreDescriptivo }}</span>
                                        </span>
                                    @else
                                        <span class="text-muted small">N/A</span>
                                    @endif
                                </td>

                                <!-- 5. COLUMNA: ACCIÓN -->
                                <td>
                                    @if ($log->event == 'created')
                                        <span class="badge bg-success">Creación</span>
                                    @elseif(in_array($log->event, ['reserva_online', 'devolucion_automatica', 'devolucion_manual', 'venta_pos']))
                                        <span class="badge bg-info text-dark">Stock</span>
                                    @elseif($log->event == 'updated')
                                        <span class="badge bg-warning text-dark">Modificación</span>
                                    @elseif($log->event == 'deleted')
                                        <span class="badge bg-danger">Eliminación</span>
                                    @elseif($log->event == 'custom')
                                        <span class="badge bg-primary">Manual</span>
                                    @elseif($log->event === 'devolucion_dinero')
                                        <span class="badge bg-danger">Reembolso</span>
                                    @elseif(in_array($log->event, ['reserva_online', 'devolucion_automatica', 'devolucion_manual']))
                                        <span class="badge bg-info text-dark">Stock</span>
                                    @else
                                        <span class="badge bg-info">{{ ucfirst($log->event) }}</span>
                                    @endif
                                </td>

                                <!-- 6. COLUMNA: DETALLES DEL CAMBIO -->
                                <td style="min-width: 300px;">
                                    @if ($log->event === 'custom' || empty($log->properties->toArray()))
                                        <span class="text-primary fw-bold">
                                            <i class="fa-solid fa-circle-info me-1"></i> {{ $log->description }}
                                        </span>
                                    @elseif($log->event === 'deleted')
                                        @php
                                            $datosBorrados =
                                                $log->properties->get('old') ??
                                                ($log->properties->get('attributes') ?? []);
                                        @endphp

                                        @if (!empty($datosBorrados))
                                            <span class="text-danger small fw-bold d-block mb-1"><i
                                                    class="fa-solid fa-trash me-1"></i> Datos borrados:</span>
                                            <ul class="list-unstyled mb-0 text-muted" style="font-size: 0.85rem;">
                                                @foreach ($datosBorrados as $key => $value)
                                                    @if (!is_array($value) && !empty($value) && !in_array($key, ['id', 'created_at', 'updated_at']))
                                                        <li><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                            {{ $value }}</li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-muted">Sin detalles (El registro se borró vacío)</span>
                                        @endif

                                    {{-- REEMBOLSOS DE CAJA --}}
                                    @elseif($log->event === 'devolucion_dinero')
                                        <div class="alert alert-danger py-1 px-2 mb-1 border-0 shadow-sm">
                                            <i class="fa-solid fa-money-bill-wave me-1"></i> 
                                            <strong>Atención:</strong> {{ $log->description }}
                                        </div>

                                        {{-- RESERVAS, DEVOLUCIONES Y VENTAS FUSIONADAS --}}
                                    @elseif(in_array($log->event, ['reserva_online', 'devolucion_automatica', 'devolucion_manual', 'venta_pos']))
                                        <div class="text-primary fw-semibold mb-1" style="font-size: 0.85rem;">
                                            <i class="fa-solid fa-boxes-stacked me-1"></i> {{ $log->description }}
                                        </div>
                                        @if ($log->properties->has('old') && $log->properties->has('attributes'))
                                            <ul class="list-unstyled mb-0" style="font-size: 0.85rem;">
                                                <li class="mb-1">
                                                    <strong class="text-dark">Stock:</strong>
                                                    <span
                                                        class="text-danger text-decoration-line-through mx-1">{{ $log->properties->get('old')['stock'] ?? 'N/A' }}</span>
                                                    ➡️
                                                    <span
                                                        class="text-success fw-bold ms-1">{{ $log->properties->get('attributes')['stock'] ?? 'N/A' }}</span>
                                                </li>
                                            </ul>
                                        @endif
                                    @elseif($log->properties->has('old') && $log->properties->has('attributes'))
                                        <ul class="list-unstyled mb-0" style="font-size: 0.85rem;">
                                            @foreach ($log->properties->get('attributes') as $key => $newValue)
                                                @php $oldValue = $log->properties->get('old')[$key] ?? 'N/A'; @endphp

                                                @if ($oldValue != $newValue && $key !== 'updated_at')
                                                    <li class="mb-1">

                                                        {{-- TRADUCCIÓN PARA CAJA (Transferencias) --}}
                                                        @if (class_basename($log->subject_type) === 'TurnoCaja' && $key === 'total_transferencias')
                                                            @php $ingreso = floatval($newValue) - floatval($oldValue); @endphp
                                                            <div class="alert alert-success py-1 px-2 mb-1 border-0">
                                                                <i class="fa-solid fa-money-bill-transfer me-1"></i>
                                                                Ingreso por transferencia de
                                                                <strong>${{ number_format($ingreso, 2) }}</strong>
                                                                <br><small class="text-muted">Fondo total de
                                                                    transferencias actual:
                                                                    ${{ number_format($newValue, 2) }}</small>
                                                            </div>

                                                            {{-- TRADUCCIÓN PARA CAJA (Efectivo) --}}
                                                        @elseif(class_basename($log->subject_type) === 'TurnoCaja' && $key === 'total_efectivo')
                                                            @php $ingreso = floatval($newValue) - floatval($oldValue); @endphp
                                                            <div class="alert alert-success py-1 px-2 mb-1 border-0">
                                                                <i class="fa-solid fa-money-bill-wave me-1"></i>
                                                                Ingreso en efectivo de
                                                                <strong>${{ number_format($ingreso, 2) }}</strong>
                                                                <br><small class="text-muted">Efectivo total en caja
                                                                    actual: ${{ number_format($newValue, 2) }}</small>
                                                            </div>

                                                            {{-- TRADUCCIÓN PARA TICKETS (Estado) --}}
                                                        @elseif(class_basename($log->subject_type) === 'Ticket' && $key === 'estado')
                                                            @php
                                                                $color = match ($newValue) {
                                                                    'pagado', 'listo' => 'success',
                                                                    'cancelado' => 'danger',
                                                                    default => 'info',
                                                                };
                                                            @endphp
                                                            <span class="text-dark">
                                                                <i
                                                                    class="fa-solid fa-arrows-rotate text-muted me-1"></i>
                                                                El ticket fue marcado como <span
                                                                    class="badge bg-{{ $color }}">{{ strtoupper($newValue) }}</span>
                                                            </span>

                                                            {{-- COMPORTAMIENTO POR DEFECTO PARA EL RESTO (ej. Inventario, Nombres) --}}
                                                        @else
                                                            <strong
                                                                class="text-dark">{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                            <span
                                                                class="text-danger text-decoration-line-through mx-1">{{ $oldValue }}</span>
                                                            ➡️
                                                            <span
                                                                class="text-success fw-bold ms-1">{{ $newValue }}</span>
                                                        @endif

                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @elseif($log->properties->has('attributes'))
                                        <ul class="list-unstyled mb-0 text-success" style="font-size: 0.85rem;">
                                            @foreach ($log->properties->get('attributes') as $key => $value)
                                                @if (!is_array($value) && !empty($value) && !in_array($key, ['id', 'updated_at']))
                                                    <li><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                        {{ $value }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted">Sin detalles registrados</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="fas fa-clipboard-list fs-1 mb-3 text-light"></i><br>
                                    No hay registros que coincidan con los filtros de búsqueda.<br>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINACIÓN -->
            <div class="mt-3">
                {{ $logs->links() }}
            </div>
        </div>
