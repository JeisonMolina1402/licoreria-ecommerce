<div>
    <div>
        <div>
            <!-- FILTROS EN UNA SOLA LÍNEA COMPACTA -->
            <div class="row g-2 mb-4 align-items-end">

                <div class="col">
                    <label class="form-label text-muted mb-1" style="font-size: 0.75rem; font-weight: 600;">Módulo</label>
                    <select wire:model.live="modulo" class="form-select form-select-sm shadow-sm">
                        <option value="">Todos</option>
                        @foreach ($modulos as $mod)
                            <!-- Sin wire:key, de forma natural -->
                            <option value="{{ $mod }}">
                                {{ strtoupper($mod) === 'DEFAULT' ? 'SISTEMA' : strtoupper(str_replace('_', ' ', $mod)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col">
                    <label class="form-label text-muted mb-1"
                        style="font-size: 0.75rem; font-weight: 600;">Acción</label>
                    <select wire:model.live="filtroAccion" class="form-select form-select-sm shadow-sm">
                        <option value="">Todas</option>
                        <option value="created">Creado</option>
                        <option value="updated">Actualizado</option>
                        <option value="deleted">Eliminado</option>
                        <option value="reserva_online">Reserva Online</option>
                    </select>
                </div>

                <div class="col">
                    <label class="form-label text-muted mb-1" style="font-size: 0.75rem; font-weight: 600;">Rol</label>
                    <select wire:model.live="filtroRol" class="form-select form-select-sm shadow-sm">
                        <option value="">Todos</option>
                        <option value="admin">Administrador</option>
                        <option value="vendedor">Vendedor</option>
                        <option value="cliente">Cliente</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-muted mb-1"
                        style="font-size: 0.75rem; font-weight: 600;">Buscar</label>
                    <div class="input-group input-group-sm shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><i
                                class="fa-solid fa-search text-muted"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="searchUsuario"
                            class="form-control border-start-0 ps-0" placeholder="Nombre o Cédula...">
                    </div>
                </div>

                <div class="col">
                    <label class="form-label text-muted mb-1"
                        style="font-size: 0.75rem; font-weight: 600;">Desde</label>
                    <input type="date" wire:model.live="fechaInicio" class="form-control form-control-sm shadow-sm">
                </div>

                <div class="col">
                    <label class="form-label text-muted mb-1"
                        style="font-size: 0.75rem; font-weight: 600;">Hasta</label>
                    <input type="date" wire:model.live="fechaFin" class="form-control form-control-sm shadow-sm">
                </div>

                <div class="col-auto">
                    <button type="button" wire:click.prevent="limpiarFiltros"
                        class="btn btn-outline-danger btn-sm shadow-sm" title="Limpiar Filtros">
                        <span wire:loading.remove wire:target="limpiarFiltros"><i class="fa-solid fa-eraser"></i></span>
                        <span wire:loading wire:target="limpiarFiltros" class="spinner-border spinner-border-sm"
                            role="status"></span>
                    </button>
                </div>

            </div>

            <div class="table-responsive transition-all" wire:loading.class="opacity-50" id="tabla-auditoria">
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
                                    @if ($log->causer)
                                        <strong class="text-dark d-block">{{ $log->causer->name }}</strong>
                                        <span class="text-muted small">Rol:
                                            {{ strtoupper($log->causer->rol ?? 'Usuario') }}</span>
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
                                            $modeloTraducido = match ($modeloOriginal) {
                                                'Role' => 'Rol',
                                                'Permission' => 'Permiso',
                                                'User' => 'Usuario',
                                                'TurnoCaja' => 'Caja',
                                                'Categoria' => 'Categoría',
                                                default => $modeloOriginal,
                                            };

                                            $nombreDescriptivo = '#' . $log->subject_id;
                                            if ($log->subject) {
                                                if ($modeloOriginal === 'TurnoCaja') {
                                                    $nombreDescriptivo = 'Turno Actual';
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
                                                    if ($modeloOriginal === 'TurnoCaja') {
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
                                        <strong class="text-dark"
                                            style="font-size: 0.85rem;">{{ mb_strtoupper($modeloTraducido) }}</strong><br>
                                        <span class="text-muted small">{{ $nombreDescriptivo }}</span>
                                    @else
                                        <span class="text-muted small">N/A</span>
                                    @endif
                                </td>

                                <td>
                                    @php
                                        $estiloBadge = match ($log->event) {
                                            'created' => 'bg-success-subtle text-success border-success-subtle',
                                            'updated' => 'bg-primary-subtle text-primary border-primary-subtle',
                                            'deleted' => 'bg-danger-subtle text-danger border-danger-subtle',
                                            default => 'bg-secondary-subtle text-secondary border-secondary-subtle',
                                        };

                                        $textoAccion = match ($log->event) {
                                            'created' => 'CREADO',
                                            'updated' => 'ACTUALIZADO',
                                            'deleted' => 'ELIMINADO',
                                            default => strtoupper($log->event),
                                        };
                                    @endphp

                                    <span class="badge {{ $estiloBadge }} border px-2 py-1"
                                        style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                        {{ $textoAccion }}
                                    </span>
                                </td>

                                <td style="min-width: 300px; font-size: 0.85rem;">
                                    @php
                                        $diccionario = [
                                            'name' => 'Nombre',
                                            'email' => 'Correo Electrónico',
                                            'cedula' => 'Cédula / DNI',
                                            'rol' => 'Nivel de Acceso',
                                            'estado' => 'Estado',
                                            'password' => 'Contraseña',
                                            'precio_compra' => 'Precio de Compra',
                                            'precio' => 'Precio de Venta',
                                            'stock' => 'Inventario',
                                            'descripcion' => 'Descripción',
                                            'categoria_id' => 'Categoría',
                                            'codigo_reserva' => 'Código de Ticket',
                                            'total' => 'Total a Pagar',
                                            'metodo_pago' => 'Método de Pago',
                                            'comprobante_deposito' => 'Comprobante de Depósito',
                                            'comprobante_whatsapp' => 'Comprobante de WhatsApp',
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

                                    <strong class="text-dark d-block mb-1">{{ $descripcionPrincipal }}</strong>

                                    {{-- ELIMINACIONES --}}
                                    @if ($log->event === 'deleted')
                                        @php $datosBorrados = $log->properties->get('old') ?? ($log->properties->get('attributes') ?? []); @endphp
                                        @if (!empty($datosBorrados))
                                            <ul class="list-unstyled mb-0 text-muted ps-2"
                                                style="border-left: 2px solid #ccc;">
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
                                            <div class="text-success">+ Permisos asignados:
                                                {{ implode(', ', $agregados) }}</div>
                                        @endif
                                        @if (count($removidos) > 0)
                                            <div class="text-danger">- Permisos quitados:
                                                {{ implode(', ', $removidos) }}</div>
                                        @endif

                                        {{-- RESERVAS Y CAMBIOS DE STOCK --}}
                                    @elseif(in_array($log->event, ['reserva_online', 'devolucion_automatica', 'devolucion_manual', 'venta_pos']))
                                        @if ($log->properties->has('old') && $log->properties->has('attributes'))
                                            <ul class="list-unstyled mb-0 text-muted ps-2"
                                                style="border-left: 2px solid #ccc;">
                                                <li>Cambió <strong>stock</strong> de <span
                                                        class="text-danger">"{{ $log->properties->get('old')['stock'] ?? 'N/A' }}"</span>
                                                    a <span
                                                        class="text-success">"{{ $log->properties->get('attributes')['stock'] ?? 'N/A' }}"</span>
                                                </li>
                                            </ul>
                                        @endif

                                        {{-- EDICIONES REGULARES (AQUÍ ESTÁ LA MAGIA DE LA FOTO) --}}
                                    @elseif($log->properties->has('old') && $log->properties->has('attributes'))
                                        <ul class="list-unstyled mb-0 text-muted ps-2"
                                            style="border-left: 2px solid #ccc;">
                                            @foreach ($log->properties->get('attributes') as $key => $newValue)
                                                @php $oldValue = $log->properties->get('old')[$key] ?? 'N/A'; @endphp
                                                @if ($oldValue != $newValue && $key !== 'updated_at')
                                                    @php $nombreCampo = $diccionario[$key] ?? mb_strtolower(str_replace('_', ' ', $key)); @endphp

                                                    @if (in_array($key, ['comprobante_deposito', 'comprobante_whatsapp']) && $newValue && $newValue !== 'N/A')
                                                        <li class="mb-1">
                                                            Se adjuntó <strong>{{ strtolower($nombreCampo) }}</strong>
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-primary border-0 py-0 px-2 ms-1 fw-bold"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#modalAuditoriaImg{{ $log->id }}">
                                                                <i class="fas fa-image"></i> Ver Foto
                                                            </button>

                                                            <div wire:ignore.self class="modal fade"
                                                                id="modalAuditoriaImg{{ $log->id }}"
                                                                tabindex="-1" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered">
                                                                    <div class="modal-content border-0 shadow-lg">
                                                                        <div class="modal-header bg-dark text-white">
                                                                            <h6 class="modal-title fw-bold">📷
                                                                                {{ $nombreCampo }}</h6>
                                                                            <button type="button"
                                                                                class="btn-close btn-close-white"
                                                                                data-bs-dismiss="modal"></button>
                                                                        </div>
                                                                        <div
                                                                            class="modal-body text-center bg-light p-4">
                                                                            <img src="{{ asset($newValue) }}"
                                                                                alt="Comprobante"
                                                                                class="img-fluid rounded shadow-sm border"
                                                                                style="max-height: 60vh; object-fit: contain;">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @else
                                                        <li>Cambió <strong>{{ $nombreCampo }}</strong> de <span
                                                                class="text-danger">"{{ $oldValue }}"</span> a
                                                            <span class="text-success">"{{ $newValue }}"</span>
                                                        </li>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </ul>

                                        {{-- CREACIONES NUEVAS --}}
                                    @elseif($log->properties->has('attributes'))
                                        <ul class="list-unstyled mb-0 text-muted ps-2"
                                            style="border-left: 2px solid #ccc;">
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

        <!-- FOOTER: PAGINACIÓN Y BOTÓN PDF -->
    <div class="row align-items-center mt-4 mb-2 pt-2 border-top">
        
       <!-- ESTILO PARA DOMAR LA PAGINACIÓN DE LARAVEL -->
        <style>
            /* 1. Ocultamos y destruimos los botones gigantes de celular para siempre */
            .paginacion-apilada nav > .d-sm-none {
                display: none !important;
            }

            /* 2. Forzamos a que la versión de PC se apile y se centre en celular */
            .paginacion-apilada nav > .d-none.d-sm-flex {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                gap: 10px;
                width: 100%;
            }

            /* 3. Centrar el texto en celular */
            .paginacion-apilada nav p {
                margin-bottom: 0 !important;
                text-align: center !important;
            }

            /* 🔥 4. MODO SÚPER COMPACTO PARA CELULARES 🔥 */
            @media (max-width: 575px) {
                /* Oculta todos los números sueltos y puntos suspensivos */
                .paginacion-apilada .pagination .page-item:not(.active):not(:first-child):not(:last-child) {
                    display: none !important;
                }
                /* Hace que los 3 botones que quedan sean anchos y cómodos para el dedo */
                .paginacion-apilada .pagination .page-link {
                    padding: 0.5rem 1.2rem !important;
                    font-weight: bold;
                    border-radius: 6px;
                }
                .paginacion-apilada .pagination {
                    gap: 5px;
                }
            }

            /* 5. En PC, restauramos la alineación a la izquierda */
            @media (min-width: 992px) {
                .paginacion-apilada nav > .d-none.d-sm-flex {
                    align-items: flex-start !important;
                }
                .paginacion-apilada nav p {
                    text-align: left !important;
                }
            }
        </style>

        <!-- PAGINACIÓN -->
        <div class="col-12 col-lg-9 mb-4 mb-lg-0 overflow-auto paginacion-apilada" style="scrollbar-width: thin;">
            {{ $logs->links(data: ['scrollTo' => '#tabla-auditoria']) }}
        </div>

        <!-- BOTÓN PDF -->
        <div class="col-12 col-lg-3 d-flex justify-content-center justify-content-lg-end">
            <a href="{{ route('inventario.pdf') }}" class="btn btn-danger btn-sm fw-bold shadow-sm px-2 d-flex justify-content-center align-items-center w-100" style="max-width: 220px;" target="_blank">
                <i class="fa-solid fa-file-pdf me-2 fs-5"></i> Descargar PDF
            </a>
        </div>
        
    </div>
</div>
