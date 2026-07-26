@extends('layouts.app')

<!-- Enviamos el título, subtítulo y el botón a la plantilla maestra -->
@section('titulo_modulo', ' Administración de Usuarios')
@section('subtitulo_modulo', 'Gestiona el personal, clientes y niveles de acceso')

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
                <strong>¡Error!</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- agregar nuevo usuario       -->

        <div class="d-flex justify-content-between align-items-end mb-3">
            <h5 class="text-dark mb-0 d-none d-md-block">Lista de Usuarios</h5>
            <button class="btn btn-primary btn-sm fw-bold shadow-sm px-3" data-bs-toggle="modal"
                data-bs-target="#modalUsuario" onclick="prepararModalCrear()">
                <i class="fa-solid fa-user-plus me-1"></i> Nuevo Usuario
            </button>
        </div>

        <!-- SISTEMA DE PESTAÑAS (TABS) -->
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white pt-4 pb-0 border-bottom-0">
                <ul class="nav nav-tabs border-bottom" id="usuariosTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold text-dark" id="personal-tab" data-bs-toggle="tab"
                            data-bs-target="#personal" type="button" role="tab"
                            style="border-top: 3px solid var(--color_primario);">
                            <i class="fa-solid fa-user-tie me-2 text-primary"></i> Personal (Admins y Vendedores)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-muted" id="clientes-tab" data-bs-toggle="tab"
                            data-bs-target="#clientes" type="button" role="tab">
                            <i class="fa-solid fa-users me-2 text-secondary"></i> Clientes Registrados
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-0">
                <div class="tab-content" id="usuariosTabsContent">

                    <!-- TAB 1: PERSONAL -->
                    <div class="tab-pane fade show active p-4" id="personal" role="tabpanel">
                        <div class="table-responsive">
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
                                    @forelse($personal as $usuario)
                                        <tr>
                                            <td class="fw-bold">{{ $usuario->name }}</td>
                                            <td>
                                                <div class="small"><i class="fa-solid fa-envelope text-muted me-1"></i>
                                                    {{ $usuario->email }}</div>
                                                <div class="small"><i class="fa-solid fa-phone text-muted me-1"></i>
                                                    {{ $usuario->telefono ?? 'N/A' }}</div>
                                                <div class="small"><i class="fa-solid fa-id-card text-muted me-1"></i>
                                                    {{ $usuario->cedula ?? 'N/A' }}</div>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge {{ $usuario->rol === 'admin' ? 'bg-primary' : 'bg-info text-dark' }} text-uppercase">
                                                    {{ $usuario->rol }}
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge {{ $usuario->estado === 'activo' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ ucfirst($usuario->estado) }}
                                                </span>
                                            </td>
                                            <td>
                                                <!-- Botón Editar -->
                                                <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal"
                                                    data-bs-target="#modalUsuario" data-id="{{ $usuario->id }}"
                                                    data-nombre="{{ $usuario->name }}" data-email="{{ $usuario->email }}"
                                                    data-cedula="{{ $usuario->cedula }}"
                                                    data-telefono="{{ $usuario->telefono }}"
                                                    data-rol="{{ $usuario->rol }}" onclick="prepararModalEditar(this)"
                                                    title="Editar Usuario">
                                                    <i class="fa-solid fa-pen"></i>
                                                </button>

                                                <!-- Formulario Activar/Desactivar -->
                                                @if ($usuario->id !== auth()->id())
                                                    <form action="{{ route('usuarios.toggle', $usuario->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('¿Seguro que deseas cambiar el estado de este usuario?')">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                            class="btn btn-sm {{ $usuario->estado === 'activo' ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                                            title="{{ $usuario->estado === 'activo' ? 'Desactivar Cuenta' : 'Activar Cuenta' }}">
                                                            <i
                                                                class="fa-solid {{ $usuario->estado === 'activo' ? 'fa-ban' : 'fa-check' }}"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">No hay personal
                                                registrado.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 2: CLIENTES -->
                    <div class="tab-pane fade p-4" id="clientes" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <!-- Estructura de tabla idéntica, pero iterando sobre la variable $clientes -->
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
                                    @forelse($clientes as $cliente)
                                        <tr>
                                            <td class="fw-bold">{{ $cliente->name }}</td>
                                            <td>
                                                <div class="small"><i class="fa-solid fa-envelope text-muted me-1"></i>
                                                    {{ $cliente->email }}</div>
                                                <div class="small"><i class="fa-solid fa-phone text-muted me-1"></i>
                                                    {{ $cliente->telefono ?? 'N/A' }}</div>
                                            </td>
                                            <td><span class="badge bg-secondary text-uppercase">{{ $cliente->rol }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge {{ $cliente->estado === 'activo' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ ucfirst($cliente->estado) }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal"
                                                    data-bs-target="#modalUsuario" data-id="{{ $cliente->id }}"
                                                    data-nombre="{{ $cliente->name }}"
                                                    data-email="{{ $cliente->email }}"
                                                    data-cedula="{{ $cliente->cedula }}"
                                                    data-telefono="{{ $cliente->telefono }}"
                                                    data-rol="{{ $cliente->rol }}" onclick="prepararModalEditar(this)"><i
                                                        class="fa-solid fa-pen"></i></button>

                                                <form action="{{ route('usuarios.toggle', $cliente->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('¿Seguro que deseas cambiar el estado de este cliente?')">
                                                    @csrf @method('PATCH')
                                                    <button type="submit"
                                                        class="btn btn-sm {{ $cliente->estado === 'activo' ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                                        <i
                                                            class="fa-solid {{ $cliente->estado === 'activo' ? 'fa-ban' : 'fa-check' }}"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">No hay clientes
                                                registrados en el sistema.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL POLIMÓRFICO (CREAR / EDITAR)         -->
    <!-- ========================================== -->
    <div class="modal fade" id="modalUsuario" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formUsuario" action="{{ route('usuarios.store') }}" method="POST"
                class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                @csrf

                <!-- Contenedor inyectado por JS para el método PUT -->
                <div id="metodoPutContainer"></div>

                <div class="modal-header bg-light border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="modalUsuarioTitle">👤 Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">NOMBRE COMPLETO *</label>
                        <input type="text" class="form-control" name="name" id="userName" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">CORREO ELECTRÓNICO *</label>
                        <input type="email" class="form-control" name="email" id="userEmail" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">CÉDULA</label>
                            <input type="text" class="form-control" name="cedula" id="userCedula">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">TELÉFONO</label>
                            <input type="text" class="form-control" name="telefono" id="userTelefono">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">NIVEL DE ACCESO (ROL) *</label>
                            <select class="form-select" name="rol" id="userRol" required>
                                <option value="vendedor">Vendedor (POS y Pedidos)</option>
                                <option value="admin">Administrador (Control Total)</option>
                                <option value="cliente">Cliente (Solo Catálogo)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">CONTRASEÑA</label>
                            <input type="password" class="form-control" name="password" id="userPassword">
                            <small class="text-muted d-none" id="helpPassword" style="font-size: 0.7rem;">Dejar en blanco
                                para mantener la actual.</small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4" id="btnSubmitUsuario">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>

@endsection

<!-- Al final de resources/views/usuarios/index.blade.php -->
@push('scripts')
    {{-- Inyectamos la URL base de forma segura al objeto window de JS --}}
    <script>
        window.urlBaseUsuarios = "{{ url('/usuarios') }}";
    </script>

    {{-- Llamamos a nuestro archivo externo compilado --}}
    @vite(['resources/js/usuarios.js'])
@endpush
