<div>
    <div>
        <div>
            <div class="row g-2 mb-4 align-items-end">

                <div class="col-lg-2 col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">Módulo</label>
                    <select wire:model.live="modulo" class="form-select shadow-sm">
                        <option value="">Todos</option>
                        @foreach ($modulos as $mod)
                            <option value="{{ $mod }}">{{ strtoupper($mod) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">Rol Responsable</label>
                    <select wire:model.live="filtroRol" class="form-select shadow-sm">
                        <option value="">Todos los roles</option>
                        <option value="admin">Administrador</option>
                        <option value="vendedor">Vendedor</option>
                        <option value="cliente">Cliente</option>
                    </select>
                </div>

                <div class="col-lg-3 col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">Buscar Usuario</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><i
                                class="fa-solid fa-search text-muted"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="searchUsuario"
                            class="form-control border-start-0 ps-0" placeholder="Nombre o Cédula...">
                    </div>
                </div>

                <div class="col-lg-2 col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">Desde Fecha</label>
                    <input type="date" wire:model.live="fechaInicio" class="form-control shadow-sm">
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">Hasta Fecha</label>
                    <input type="date" wire:model.live="fechaFin" class="form-control shadow-sm">
                </div>

                <div class="col-lg-1 col-md-4">
                    <label class="form-label small mb-1 d-none d-lg-block">&nbsp;</label>
                    <button wire:click="limpiarFiltros" class="btn btn-outline-danger w-100 shadow-sm"
                        title="Limpiar Filtros" wire:loading.attr="disabled" wire:target="limpiarFiltros">
                        <span wire:loading.remove wire:target="limpiarFiltros">
                            <i class="fa-solid fa-eraser"></i>
                        </span>
                        <span wire:loading wire:target="limpiarFiltros" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </button>
                </div>

            </div>

            <div class="table-responsive transition-all" wire:loading.class="opacity-50">
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
                                <td class="text-nowrap text-muted" style="font-size: 0.9rem;">
                                    {{ $log->created_at->format('d/m/Y h:i A') }}
                                </td>

                                <td>
                                    @if($log->causer)
                                        <strong class="text-dark d-block">{{ $log->causer->name }}</strong>
                                        <span class="text-muted small">Rol: {{ strtoupper($log->causer->rol ?? 'Usuario') }}</span>
                                    @else
                                        <strong class="text-dark d-block">SISTEMA</strong>
                                        <span class="text-muted small">Automático</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="text-dark fw-bold" style="font-size: 0.85rem;">
                                        {{ strtoupper($log->log_name) }}
                                    </span>
                                </td>

                                <td>
                                    @if ($log->subject_type)
                                        @php
                                            // Traducción de modelos al español
                                            $modeloOriginal = class_basename($log->subject_type);
                                            $modeloTraducido = match($modeloOriginal) {
                                                'Role' => 'Rol',
                                                'Permission' => 'Permiso',
                                                'User' => 'Usuario',
                                                'TurnoCaja' => 'Caja',
                                                'Categoria' => 'Categoría',
                                                default => $modeloOriginal
                                            };

                                            $nombreDescriptivo = '#' . $log->subject_id;
                                            if ($log->subject) {
                                                if ($modeloOriginal === 'TurnoCaja') {
                                                    $nombreDescriptivo = 'Turno Actual';
                                                } else {
                                                    $nombreDescriptivo = $log->subject->nombre ?? ($log->subject->name ?? ($log->subject->codigo_reserva ?? $nombreDescriptivo));
                                                }
                                            } else {
                                                $atributosGuardados = $log->properties->get('old') ?? ($log->properties->get('attributes') ?? []);
                                                if (!empty($atributosGuardados)) {
                                                    if ($modeloOriginal === 'TurnoCaja') {
                                                        $nombreDescriptivo = 'Turno Cerrado';
                                                    } else {
                                                        $nombre = $atributosGuardados['nombre'] ?? ($atributosGuardados['name'] ?? ($atributosGuardados['codigo_reserva'] ?? null));
                                                        if ($nombre) {
                                                            $nombreDescriptivo = $nombre;
                                                        }
                                                    }
                                                }
                                            }
                                        @endphp
                                        <strong class="text-dark" style="font-size: 0.85rem;">{{ mb_strtoupper($modeloTraducido) }}</strong><br>
                                        <span class="text-muted small">{{ $nombreDescriptivo }}</span>
                                    @else
                                        <span class="text-muted small">N/A</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                        {{ strtoupper($log->event) }}
                                    </span>
                                </td>

                                <td style="min-width: 300px; font-size: 0.85rem;">
                                    @php
                                        $diccionario = [
                                            'name' => 'Nombre', 'email' => 'Correo Electrónico', 'cedula' => 'Cédula / DNI',
                                            'rol' => 'Nivel de Acceso', 'estado' => 'Estado', 'password' => 'Contraseña',
                                            'precio_compra' => 'Precio de Compra', 'precio' => 'Precio de Venta',
                                            'stock' => 'Inventario', 'descripcion' => 'Descripción', 'categoria_id' => 'Categoría',
                                            'codigo_reserva' => 'Código de Ticket', 'total' => 'Total a Pagar', 'metodo_pago' => 'Método de Pago',
                                            'comprobante_deposito' => 'Comprobante de Depósito', 
                                            'comprobante_whatsapp' => 'Comprobante de WhatsApp'  
                                        ];

                                        $descripcionPrincipal = $log->description;
                                        if (in_array($descripcionPrincipal, ['created', 'updated', 'deleted'])) {
                                            $descripcionPrincipal = match($descripcionPrincipal) {
                                                'created' => 'Registro creado en el sistema.',
                                                'updated' => 'Registro actualizado.',
                                                'deleted' => 'Registro eliminado.',
                                            };
                                        }
                                    @endphp

                                    <strong class="text-dark d-block mb-1">{{ $descripcionPrincipal }}</strong>
                                    
                                    {{-- ELIMINACIONES --}}
                                    @if ($log->event === 'deleted')
                                        @php $datosBorrados = $log->properties->get('old') ?? ($log->properties->get('attributes') ?? []); @endphp
                                        @if(!empty($datosBorrados))
                                            <ul class="list-unstyled mb-0 text-muted ps-2" style="border-left: 2px solid #ccc;">
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
                                        @if(count($agregados) > 0) <div class="text-success">+ Permisos asignados: {{ implode(', ', $agregados) }}</div> @endif
                                        @if(count($removidos) > 0) <div class="text-danger">- Permisos quitados: {{ implode(', ', $removidos) }}</div> @endif

                                    {{-- RESERVAS Y CAMBIOS DE STOCK --}}
                                    @elseif(in_array($log->event, ['reserva_online', 'devolucion_automatica', 'devolucion_manual', 'venta_pos']))
                                        @if ($log->properties->has('old') && $log->properties->has('attributes'))
                                            <ul class="list-unstyled mb-0 text-muted ps-2" style="border-left: 2px solid #ccc;">
                                                <li>Cambió <strong>stock</strong> de <span class="text-danger">"{{ $log->properties->get('old')['stock'] ?? 'N/A' }}"</span> a <span class="text-success">"{{ $log->properties->get('attributes')['stock'] ?? 'N/A' }}"</span></li>
                                            </ul>
                                        @endif

                                    {{-- EDICIONES REGULARES (AQUÍ ESTÁ LA MAGIA DE LA FOTO) --}}
                                    @elseif($log->properties->has('old') && $log->properties->has('attributes'))
                                        <ul class="list-unstyled mb-0 text-muted ps-2" style="border-left: 2px solid #ccc;">
                                            @foreach ($log->properties->get('attributes') as $key => $newValue)
                                                @php $oldValue = $log->properties->get('old')[$key] ?? 'N/A'; @endphp
                                                @if ($oldValue != $newValue && $key !== 'updated_at')
                                                    @php $nombreCampo = $diccionario[$key] ?? mb_strtolower(str_replace('_', ' ', $key)); @endphp
                                                    
                                                    @if(in_array($key, ['comprobante_deposito', 'comprobante_whatsapp']) && $newValue && $newValue !== 'N/A')
                                                        <li class="mb-1">
                                                            Se adjuntó <strong>{{ strtolower($nombreCampo) }}</strong>
                                                            <button type="button" class="btn btn-sm btn-outline-primary border-0 py-0 px-2 ms-1 fw-bold" data-bs-toggle="modal" data-bs-target="#modalAuditoriaImg{{ $log->id }}">
                                                                <i class="fas fa-image"></i> Ver Foto
                                                            </button>

                                                            <div wire:ignore.self class="modal fade" id="modalAuditoriaImg{{ $log->id }}" tabindex="-1" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered">
                                                                    <div class="modal-content border-0 shadow-lg">
                                                                        <div class="modal-header bg-dark text-white">
                                                                            <h6 class="modal-title fw-bold">📷 {{ $nombreCampo }}</h6>
                                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                                        </div>
                                                                        <div class="modal-body text-center bg-light p-4">
                                                                            <img src="{{ asset($newValue) }}" alt="Comprobante" class="img-fluid rounded shadow-sm border" style="max-height: 60vh; object-fit: contain;">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @else
                                                        <li>Cambió <strong>{{ $nombreCampo }}</strong> de <span class="text-danger">"{{ $oldValue }}"</span> a <span class="text-success">"{{ $newValue }}"</span></li>
                                                    @endif

                                                @endif
                                            @endforeach
                                        </ul>

                                    {{-- CREACIONES NUEVAS --}}
                                    @elseif($log->properties->has('attributes'))
                                        <ul class="list-unstyled mb-0 text-muted ps-2" style="border-left: 2px solid #ccc;">
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
                                <td colspan="6" class="text-center text-muted py-5">
                                    No hay registros que coincidan con los filtros de búsqueda.<br>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
                <div>
                    <a href="{{ route('auditoria.pdf', ['modulo' => $modulo, 'filtroRol' => $filtroRol, 'searchUsuario' => $searchUsuario, 'fechaInicio' => $fechaInicio, 'fechaFin' => $fechaFin]) }}"
                        class="btn btn-danger btn-sm fw-bold shadow-sm px-3 py-2" target="_blank">
                        <i class="fa-solid fa-file-pdf me-1"></i> Descargar PDF
                    </a>
                </div>
                <div>
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>