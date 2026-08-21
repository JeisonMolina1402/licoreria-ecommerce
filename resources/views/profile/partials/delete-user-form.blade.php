<section>
    <header class="mb-4">
        <h2 class="titulo-premium h4 text-danger mb-1">Borrar Cuenta</h2>
        <p class="text-muted small">Una vez que se elimine tu cuenta, todos tus recursos y datos se eliminarán de forma permanente. Antes de borrar tu cuenta, por favor descarga cualquier dato o información que desees conservar.</p>
    </header>

    <!-- Botón que abre el Modal de Bootstrap -->
    <button type="button" class="btn btn-outline-danger shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
        <i class="fa-solid fa-trash-can me-1"></i> BORRAR CUENTA
    </button>

    <!-- Modal de Bootstrap para Confirmación -->
    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <form method="post" action="{{ route('profile.destroy') }}" class="form-cargando">
                    @csrf
                    @method('delete')
                    
                    <div class="modal-header border-bottom-0 pb-0 mt-2">
                        <h5 class="modal-title titulo-premium text-danger fw-bold" id="confirmUserDeletionModalLabel">¿Estás seguro de querer borrar tu cuenta?</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body">
                        <p class="text-muted small mb-4">
                            Una vez que se elimine tu cuenta, todos tus recursos, reservas y datos se eliminarán permanentemente. Por favor, ingresa tu contraseña para confirmar esta acción irreversible.
                        </p>
                        
                        <div class="mb-3">
                            <label for="password_delete" class="form-label fw-bold small text-muted text-uppercase">Tu Contraseña</label>
                            <div class="position-relative">
                                <input id="password_delete" name="password" type="password" class="form-control shadow-sm" style="padding-right: 2.5rem;" placeholder="Ingresa tu contraseña actual">
                                <button class="btn border-0 position-absolute top-50 end-0 translate-middle-y toggle-password text-secondary" type="button" data-target="password_delete">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                            @if($errors->userDeletion->has('password'))
                                <div class="text-danger small mt-1 fw-bold">{{ $errors->userDeletion->first('password') }}</div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="modal-footer border-top-0 pt-0 mb-2">
                        <button type="button" class="btn btn-light shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger shadow-sm">Borrar Cuenta Definitivamente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Script para Auto-abrir el modal si la contraseña de borrado es incorrecta -->
@if($errors->userDeletion->isNotEmpty())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var myModal = new bootstrap.Modal(document.getElementById('confirmUserDeletionModal'));
        myModal.show();
    });
</script>
@endif