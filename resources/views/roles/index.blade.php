@extends('layouts.app')

@section('titulo_modulo', 'Administración de Roles')
@section('subtitulo_modulo', 'Gestiona los niveles de acceso del sistema')

@section('content')
    <div class="container-fluid bg-light p-4" style="min-height: 100vh;">

        <!-- ALERTAS -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <strong>¡Éxito!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <strong>¡Error!</strong> {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-end mb-3">
            <h5 class="text-dark mb-0 d-none d-md-block">Lista de Roles</h5>
            <button class="btn btn-primary btn-sm fw-bold shadow-sm px-3" data-bs-toggle="modal"
                data-bs-target="#modalRol" onclick="prepararModalRolCrear()">
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
                                        @if($role->name === 'admin')
                                            <span class="badge bg-success">Todos los permisos</span>
                                        @else
                                            <span class="text-muted small">
                                                {{ $role->permissions->count() }} permisos
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        
                                        {{-- Botón para asignar Permisos (Iremos a esta vista después) --}}
                                        @if($role->name !== 'admin')
                                        <a href="{{ route('roles.permisos', $role->id) }}" class="btn btn-sm btn-outline-info me-1" title="Asignar Permisos">
                                            <i class="fa-solid fa-key"></i> Permisos
                                        </a>
                                        @endif

                                        {{-- Botón Editar --}}
                                        <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal"
                                            data-bs-target="#modalRol" 
                                            data-id="{{ $role->id }}"
                                            data-name="{{ $role->name }}" 
                                            onclick="prepararModalRolEditar(this)" title="Editar Rol">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>

                                        {{-- Botón Eliminar --}}
                                        @if(!in_array($role->name, ['admin', 'cliente']))
                                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('¿Seguro que deseas eliminar este rol definitivamente?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar Rol">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
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

    <!-- MODAL POLIMÓRFICO PARA ROLES -->
    <div class="modal fade" id="modalRol" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formRol" action="{{ route('roles.store') }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                @csrf
                <div id="metodoRolPutContainer"></div>

                <div class="modal-header bg-light border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="modalRolTitle"><i class="fa-solid fa-shield me-2"></i>Nuevo Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">NOMBRE DEL ROL *</label>
                        <input type="text" class="form-control" name="name" id="rolName" required placeholder="ej. supervisor">
                    </div>
                </div>

                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4" id="btnSubmitRol">Guardar Rol</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Variables de entorno para JS --}}
    <script>
        window.urlBaseRoles = "{{ url('/roles') }}";
    </script>
    {{-- Archivo JS encapsulado y compilado --}}
    @vite(['resources/js/roles.js'])
@endpush