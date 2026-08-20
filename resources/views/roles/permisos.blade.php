@extends('layouts.app')

@section('titulo_modulo', 'Asignar Permisos')
@section('subtitulo_modulo', 'Configurando accesos para el rol: ' . strtoupper($rol->name))

@section('content')
    <style>
        /* Efecto premium para las tarjetas de permisos */
        .permiso-card {
            transition: all 0.2s ease-in-out;
            border: 1px solid rgba(0,0,0,0.05) !important;
        }
        .permiso-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
            border-color: rgba(13, 110, 253, 0.3) !important;
        }
    </style>

    <div class="container-fluid bg-light p-4" style="min-height: 100vh;">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver a Roles
            </a>
            <h5 class="fw-bold text-dark m-0">Rol: <span class="text-primary text-uppercase">{{ $rol->name }}</span></h5>
        </div>

        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-white pt-4 pb-3 border-bottom text-center">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-check-double me-2 text-success"></i>Selecciona las acciones permitidas</h6>
                <p class="text-muted small mt-1 mb-0">Marca las casillas de las funciones a las que este rol tendrá acceso.</p>
            </div>
            
            <form action="{{ route('roles.permisos.update', $rol->id) }}" method="POST" class="form-cargando">
                @csrf
                <div class="card-body p-4 bg-light">
                    
                    <div class="row g-3">
                        @forelse($permissions as $permiso)
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                
                                <div class="card bg-white shadow-sm h-100 permiso-card" style="border-radius: 12px; cursor: pointer;"
                                     onclick="document.getElementById('permiso_{{ $permiso->id }}').click();">
                                    
                                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                                        <label class="form-check-label fw-bold text-dark mb-0" for="permiso_{{ $permiso->id }}" 
                                               style="cursor: pointer; user-select: none; line-height: 1.2;">
                                            {{ ucfirst($permiso->name) }}
                                        </label>
                                        
                                        <div class="form-check form-switch m-0 p-0 ps-2 d-flex align-items-center">
                                            <input class="form-check-input fs-4 m-0 shadow-none float-none" type="checkbox" role="switch" 
                                                name="permissions[]" 
                                                value="{{ $permiso->name }}" 
                                                id="permiso_{{ $permiso->id }}"
                                                {{ in_array($permiso->id, $rolePermissions) ? 'checked' : '' }}
                                                onclick="event.stopPropagation();"
                                                style="cursor: pointer;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted py-5">
                                No hay permisos creados en el sistema. Ve a la sección de Permisos primero.
                            </div>
                        @endforelse
                    </div>

                </div>
                
                <div class="card-footer bg-white border-top text-end p-4">
                    <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm rounded-pill fw-bold">
                        <i class="fa-solid fa-floppy-disk me-2"></i> Guardar Configuración
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection