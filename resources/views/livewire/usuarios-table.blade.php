<div>
    <!-- FILTROS (Buscador y Roles) -->
    <div class="card shadow-sm border-0 mb-4 rounded-4">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                
                <!-- Buscador -->
                <div class="col-md-5">
                    <label class="form-label text-muted mb-1" style="font-size: 0.75rem; font-weight: 600;">Buscar Usuario</label>
                    <div class="input-group input-group-sm shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0 ps-0 text-muted" wire:model.live.debounce.300ms="buscar" placeholder="Nombre, cédula o correo...">
                    </div>
                </div>

                <!-- Filtro de Rol -->
                <div class="col-md-4">
                    <label class="form-label text-muted mb-1" style="font-size: 0.75rem; font-weight: 600;">Filtrar por Rol</label>
                    <select class="form-select form-select-sm text-muted shadow-sm" wire:model.live="filtro_rol">
                        <option value="">Todos los roles...</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Botón Limpiar -->
                <div class="col-auto">
                    <button type="button" wire:click="limpiar" class="btn btn-outline-danger btn-sm shadow-sm" title="Limpiar Filtros">
                        <i class="fa-solid fa-eraser"></i>
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- TABLA ÚNICA DE USUARIOS -->
    <div class="table-responsive bg-white p-3 rounded-4 shadow-sm mb-4 transition-all" wire:loading.class="opacity-50" id="tabla-usuarios">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Contacto</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $usuario)
                    <tr>
                        <td class="fw-bold">{{ $usuario->name }}</td>
                        <td>
                            <div><i class="fas fa-envelope text-muted"></i> {{ $usuario->email }}</div>
                            <div class="mt-1">
                                @if ($usuario->email_verified_at)
                                    <span class="badge bg-success" style="font-size: 0.7rem;"><i class="fas fa-check-circle"></i> Verificado</span>
                                @else
                                    <span class="badge bg-warning text-dark" style="font-size: 0.7rem;"><i class="fas fa-clock"></i> Pendiente</span>
                                @endif
                            </div>
                            <div class="small mt-1"><i class="fa-solid fa-phone text-muted me-1"></i> {{ $usuario->telefono ?? 'N/A' }}</div>
                            <div class="small"><i class="fa-solid fa-id-card text-muted me-1"></i> {{ $usuario->cedula ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $usuario->rol === 'admin' ? 'bg-primary' : ($usuario->rol === 'vendedor' ? 'bg-info text-dark' : 'bg-secondary') }} text-uppercase">
                                {{ $usuario->rol }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $usuario->estado === 'activo' ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst($usuario->estado) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex flex-row flex-nowrap gap-2 justify-content-start align-items-center">
                                
                                <!-- 1. EDITAR -->
                                @can('editar usuario')
                                    <button class="btn btn-sm btn-outline-primary shadow-sm fw-bold px-2 px-md-3" data-bs-toggle="modal"
                                        data-bs-target="#modalUsuario" data-id="{{ $usuario->id }}" data-nombre="{{ $usuario->name }}"
                                        data-email="{{ $usuario->email }}" data-cedula="{{ $usuario->cedula }}"
                                        data-telefono="{{ $usuario->telefono }}" data-rol="{{ $usuario->rol }}"
                                        onclick="prepararModalEditar(this)" title="Editar Usuario">
                                        <i class="fa-solid fa-pen"></i> <span class="d-none d-md-inline ms-1">Editar</span>
                                    </button>
                                @endcan

                                @if ($usuario->id !== auth()->id())
                                
                                    <!-- 2. SUSPENDER/ACTIVAR -->
                                    @can('suspender usuario')
                                        <form action="{{ route('usuarios.toggle', $usuario->id) }}" method="POST" class="m-0 p-0 form-estado">
                                            @csrf @method('PATCH')
                                            @if ($usuario->estado === 'activo' || $usuario->estado === null)
                                                <button type="submit" class="btn btn-sm btn-outline-warning shadow-sm fw-bold text-dark px-2 px-md-3" title="Suspender Usuario">
                                                    <i class="fa-solid fa-ban"></i> <span class="d-none d-md-inline ms-1">Suspender</span>
                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-sm btn-outline-success shadow-sm fw-bold px-2 px-md-3" title="Activar Usuario">
                                                    <i class="fa-solid fa-check"></i> <span class="d-none d-md-inline ms-1">Activar</span>
                                                </button>
                                            @endif
                                        </form>
                                    @endcan

                                    <!-- 3. ELIMINAR -->
                                    @can('eliminar usuario')
                                        <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST" class="m-0 p-0 form-eliminar">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm fw-bold px-2 px-md-3" title="Eliminar Usuario">
                                                <i class="fa-solid fa-trash-can"></i> <span class="d-none d-md-inline ms-1">Eliminar</span>
                                            </button>
                                        </form>
                                    @endcan
                                    
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No se encontraron usuarios con esos filtros.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PAGINACIÓN APILADA (Izquierda en PC, Centrada en Móviles) -->
    <div class="mt-4 pt-3 border-top overflow-auto paginacion-apilada" style="scrollbar-width: thin;">
        <style>
            /* 1. Destruimos los botones gigantes de celular */
            .paginacion-apilada nav > .d-sm-none { display: none !important; }
            
            /* 2. Móvil (Por defecto): Apilado y Centrado */
            .paginacion-apilada nav > .d-none.d-sm-flex { 
                display: flex !important; 
                flex-direction: column !important; 
                align-items: center !important; 
                justify-content: center !important; 
                gap: 10px; 
                width: 100%; 
            }
            .paginacion-apilada nav p { 
                margin-bottom: 0 !important; 
                text-align: center !important; 
            }
            
            /* 3. Móviles pequeños: Paginación súper compacta */
            @media (max-width: 575px) {
                .paginacion-apilada .pagination .page-item:not(.active):not(:first-child):not(:last-child) { display: none !important; }
                .paginacion-apilada .pagination .page-link { padding: 0.5rem 1.2rem !important; font-weight: bold; border-radius: 6px; }
                .paginacion-apilada .pagination { gap: 5px; }
            }

            /* 🔥 4. PC y Tablets: Alineado a la izquierda 🔥 */
            @media (min-width: 768px) {
                .paginacion-apilada nav > .d-none.d-sm-flex {
                    align-items: flex-start !important;
                    justify-content: flex-start !important;
                }
                .paginacion-apilada nav p {
                    text-align: left !important;
                }
            }
        </style>
        
        {{ $usuarios->links(data: ['scrollTo' => '#tabla-usuarios']) }} 
    </div>
</div>