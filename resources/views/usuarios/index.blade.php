@extends('layouts.app')

@section('titulo_modulo', 'Administración de Usuarios')
@section('subtitulo_modulo', 'Gestiona el personal, clientes y niveles de acceso')

@section('content')
    <div class="container-fluid bg-light p-4" style="min-height: 100vh;">

        <div class="d-flex justify-content-between align-items-end mb-3">
            <h5 class="text-dark mb-0 d-none d-md-block">Lista de Usuarios</h5>
            <button class="btn btn-primary btn-sm fw-bold shadow-sm px-3" data-bs-toggle="modal"
                data-bs-target="#modalUsuario" onclick="prepararModalCrear()">
                <i class="fa-solid fa-user-plus me-1"></i> Nuevo Usuario
            </button>
        </div>

        <!-- AQUÍ INYECTAMOS LA MAGIA DE LIVEWIRE (La tabla unificada con sus filtros) -->
        @livewire('usuarios-table')

    </div>

    <!-- MODAL DE USUARIO -->
    <div class="modal fade" id="modalUsuario" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formUsuario" action="{{ route('usuarios.store') }}" method="POST"
                class="modal-content border-0 shadow-lg form-cargando" style="border-radius: 12px;">
                @csrf

                <div id="metodoPutContainer"></div>

                <div class="modal-header bg-light border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="modalUsuarioTitle">👤 Nuevo Usuario</h5>
                    <button type="button" id="btnCerrarModalFantasmaUsuario" data-bs-dismiss="modal" class="d-none"></button>
                    <button type="button" class="btn-close" onclick="confirmarCancelacionUsuario()"></button>
                </div>

                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">NOMBRE COMPLETO *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               name="name" id="userName" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">CORREO ELECTRÓNICO *</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" id="userEmail" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">CÉDULA</label>
                            <input type="text" class="form-control @error('cedula') is-invalid @enderror" 
                                   name="cedula" id="userCedula" value="{{ old('cedula') }}">
                            @error('cedula')
                                <div class="invalid-feedback fw-bold">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">TELÉFONO</label>
                            <input type="text" class="form-control @error('telefono') is-invalid @enderror" 
                                   name="telefono" id="userTelefono" value="{{ old('telefono') }}">
                            @error('telefono')
                                <div class="invalid-feedback fw-bold">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                       <div class="col-6">
                            <label class="form-label fw-bold small text-muted">NIVEL DE ACCESO (ROL) *</label>
                            <select class="form-select @error('rol') is-invalid @enderror" name="rol" id="userRol" required>
                                <option value="">Seleccione un rol...</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('rol') == $role->name ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('rol')
                                <div class="invalid-feedback fw-bold">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">CONTRASEÑA</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   name="password" id="userPassword">
                            <small class="text-muted d-none" id="helpPassword" style="font-size: 0.7rem;">Dejar en blanco para mantener la actual.</small>
                            @error('password')
                                <div class="invalid-feedback fw-bold">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light" onclick="confirmarCancelacionUsuario()">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4" id="btnSubmitUsuario">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.urlBaseUsuarios = "{{ url('/usuarios') }}";
    </script>

    @if ($errors->any())
        <button type="button" id="btnAutoOpenModal" data-bs-toggle="modal" data-bs-target="#modalUsuario" class="d-none"></button>
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => {
                    document.getElementById('btnAutoOpenModal').click();
                }, 150);
            });
        </script>
    @endif

    @vite(['resources/js/usuarios.js'])
@endpush