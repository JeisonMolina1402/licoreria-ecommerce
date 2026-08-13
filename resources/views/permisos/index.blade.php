@extends('layouts.app')

@section('titulo_modulo', 'Administración de Permisos')
@section('subtitulo_modulo', 'Control granular de acciones dentro del sistema')

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
            <h5 class="text-dark mb-0 d-none d-md-block">Lista de Permisos del Sistema</h5>
            <button class="btn btn-primary btn-sm fw-bold shadow-sm px-3" data-bs-toggle="modal"
                data-bs-target="#modalPermiso" onclick="prepararModalPermisoCrear()">
                <i class="fa-solid fa-plus me-1"></i> Nuevo Permiso
            </button>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width: 10%;">ID</th>
                                <th>Nombre del Permiso (Acción Específica)</th>
                                <th class="text-end pe-4" style="width: 20%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($permisos as $permiso)
                                <tr>
                                    <td class="ps-4 text-muted fw-bold">#{{ $permiso->id }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border border-secondary fw-normal px-3 py-2 fs-6">
                                            <i class="fa-solid fa-key text-muted me-2"></i> {{ $permiso->name }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        {{-- Botón Editar --}}
                                        <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal"
                                            data-bs-target="#modalPermiso" 
                                            data-id="{{ $permiso->id }}"
                                            data-name="{{ $permiso->name }}" 
                                            onclick="prepararModalPermisoEditar(this)" title="Editar Permiso">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>

                                        {{-- Botón Eliminar --}}
                                        <form action="{{ route('permisos.destroy', $permiso->id) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('ATENCIÓN: Borrar un permiso lo quitará de todos los roles que lo tengan. ¿Continuar?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar Permiso">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">No hay permisos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL POLIMÓRFICO PARA PERMISOS -->
    <div class="modal fade" id="modalPermiso" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formPermiso" action="{{ route('permisos.store') }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                @csrf
                <div id="metodoPermisoPutContainer"></div>

                <div class="modal-header bg-light border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="modalPermisoTitle"><i class="fa-solid fa-key me-2"></i>Nuevo Permiso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4">
                    <div class="alert alert-info border-0 shadow-sm small py-2 mb-4">
                        <i class="fa-solid fa-circle-info me-1"></i> <strong>Buena Práctica:</strong> Usa nombres descriptivos (ej. "crear usuarios", "ver reportes financieros").
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">ACCIÓN PERMITIDA *</label>
                        <input type="text" class="form-control" name="name" id="permisoName" required placeholder="ej. editar inventario">
                    </div>
                </div>

                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4" id="btnSubmitPermiso">Guardar Permiso</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Variables de entorno para JS --}}
    <script>
        window.urlBasePermisos = "{{ url('/permisos') }}";
    </script>
    {{-- Archivo JS encapsulado y compilado --}}
    @vite(['resources/js/permisos.js'])
@endpush