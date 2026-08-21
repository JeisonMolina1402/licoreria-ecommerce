@extends('layouts.app')

@section('titulo_modulo', 'Administración de Roles')
@section('subtitulo_modulo', 'Gestiona los niveles de acceso del sistema')

@section('content')
    <div class="container-fluid bg-light p-4" style="min-height: 100vh;">

        <div class="d-flex justify-content-between align-items-end mb-3">
            <h5 class="text-dark mb-0 d-none d-md-block">Lista de Roles</h5>
            <button class="btn btn-primary btn-sm fw-bold shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#modalRol"
                onclick="prepararModalRolCrear()">
                <i class="fa-solid fa-plus me-1"></i> Nuevo Rol
            </button>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Nombre del Rol</th>
                            <th>Permisos Asignados</th>
                            <!-- El título está alineado a la derecha -->
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-secondary text-uppercase fs-6">{{ $role->name }}</span>
                                </td>
                                <td>
                                    @if (strtolower($role->name) === 'admin')
                                        <span class="badge bg-success">Todos los permisos</span>
                                    @else
                                        <span class="text-muted small">
                                            {{ $role->permissions->count() }} permisos
                                        </span>
                                    @endif
                                </td>
                                
                                <!-- Alineamos la celda a la derecha para que coincida con el título -->
                                <td class="text-end pe-4">
                                    <div class="d-flex flex-row flex-nowrap gap-2 justify-content-end align-items-center">
                                        
                                        <!-- BOTÓN PERMISOS -->
                                        @can('asignar permisos')
                                            @if($role->id !== 1)
                                                <a href="{{ route('roles.permisos', $role->id) }}" class="btn btn-sm btn-outline-info shadow-sm fw-bold px-2 px-md-3" title="Gestionar Permisos">
                                                    <i class="fa-solid fa-key"></i> <span class="d-none d-md-inline ms-1">Permisos</span>
                                                </a>
                                            @endif
                                        @endcan

                                        <!-- BOTÓN EDITAR -->
                                        @can('editar rol')
                                            <button class="btn btn-sm btn-outline-primary shadow-sm fw-bold px-2 px-md-3" data-bs-toggle="modal"
                                                data-bs-target="#modalRol" data-id="{{ $role->id }}" data-name="{{ $role->name }}" 
                                                onclick="prepararModalRolEditar(this)" title="Editar Rol">
                                                <i class="fa-solid fa-pen"></i> <span class="d-none d-md-inline ms-1">Editar</span>
                                            </button>
                                        @endcan

                                        <!-- BOTÓN ELIMINAR -->
                                        @can('eliminar rol')
                                            @if(strtolower($role->name) !== 'admin')
                                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="m-0 p-0 form-eliminar">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm fw-bold px-2 px-md-3" title="Eliminar Rol">
                                                        <i class="fa-solid fa-trash-can"></i> <span class="d-none d-md-inline ms-1">Eliminar</span>
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">No hay roles registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>

    <div class="modal fade" id="modalRol" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formRol" action="{{ route('roles.store') }}" method="POST"
                class="modal-content border-0 shadow-lg form-cargando" style="border-radius: 12px;">
                @csrf
                <div id="metodoRolPutContainer"></div>

                <div class="modal-header bg-light border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="modalRolTitle"><i class="fa-solid fa-shield me-2"></i>Nuevo Rol</h5>
                    <button type="button" id="btnCerrarModalFantasmaRol" data-bs-dismiss="modal" class="d-none"></button>
                    <button type="button" class="btn-close" onclick="confirmarCancelacionRol()"></button>
                </div>

                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">NOMBRE DEL ROL *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                            id="rolName" required placeholder="ej. supervisor" value="{{ old('name') }}">
                        @error('name')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light" onclick="confirmarCancelacionRol()">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4" id="btnSubmitRol">Guardar Rol</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.urlBaseRoles = "{{ url('/roles') }}";
    </script>
    @vite(['resources/js/roles.js'])

    @if ($errors->any())
        <button type="button" id="btnAutoOpenModalRol" data-bs-toggle="modal" data-bs-target="#modalRol"
            class="d-none"></button>
        <script>
            window.addEventListener('DOMContentLoaded', (event) => {
                setTimeout(() => {
                    // Simulamos un clic humano para forzar la apertura del modal
                    document.getElementById('btnAutoOpenModalRol').click();
                }, 150);
            });
        </script>
    @endif
@endpush
