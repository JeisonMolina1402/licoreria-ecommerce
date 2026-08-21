<section>
    <header class="mb-4">
        <h2 class="titulo-premium h4 text-dark mb-1">Actualizar Contraseña</h2>
        <p class="text-muted small">Asegúrate de que tu cuenta use una contraseña larga y aleatoria para mantenerte seguro.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="form-cargando">
        @csrf
        @method('put')

        <div class="row">
            <!-- CONTRASEÑA ACTUAL -->
            <div class="col-md-12 mb-3">
                <label for="update_password_current_password" class="form-label fw-bold small text-muted text-uppercase">Contraseña Actual</label>
                <div class="position-relative">
                    <input id="update_password_current_password" name="current_password" type="password" class="form-control shadow-sm" style="padding-right: 2.5rem;" autocomplete="current-password">
                    <button class="btn border-0 position-absolute top-50 end-0 translate-middle-y toggle-password text-secondary" type="button" data-target="update_password_current_password">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
                @if($errors->updatePassword->has('current_password'))
                    <div class="text-danger small mt-1 fw-bold">{{ $errors->updatePassword->first('current_password') }}</div>
                @endif
            </div>

            <!-- NUEVA CONTRASEÑA -->
            <div class="col-md-6 mb-3">
                <label for="update_password_password" class="form-label fw-bold small text-muted text-uppercase">Nueva Contraseña</label>
                <div class="position-relative">
                    <input id="update_password_password" name="password" type="password" class="form-control shadow-sm" style="padding-right: 2.5rem;" autocomplete="new-password">
                    <button class="btn border-0 position-absolute top-50 end-0 translate-middle-y toggle-password text-secondary" type="button" data-target="update_password_password">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
                @if($errors->updatePassword->has('password'))
                    <div class="text-danger small mt-1 fw-bold">{{ $errors->updatePassword->first('password') }}</div>
                @endif
            </div>

            <!-- CONFIRMAR NUEVA CONTRASEÑA -->
            <div class="col-md-6 mb-3">
                <label for="update_password_password_confirmation" class="form-label fw-bold small text-muted text-uppercase">Confirmar Contraseña</label>
                <div class="position-relative">
                    <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control shadow-sm" style="padding-right: 2.5rem;" autocomplete="new-password">
                    <button class="btn border-0 position-absolute top-50 end-0 translate-middle-y toggle-password text-secondary" type="button" data-target="update_password_password_confirmation">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
                @if($errors->updatePassword->has('password_confirmation'))
                    <div class="text-danger small mt-1 fw-bold">{{ $errors->updatePassword->first('password_confirmation') }}</div>
                @endif
            </div>
        </div>

        <div class="d-flex align-items-center gap-3 mt-3">
            <button type="submit" class="btn btn-black"><i class="fa-solid fa-lock me-1"></i> Guardar Contraseña</button>
        </div>
    </form>
</section>