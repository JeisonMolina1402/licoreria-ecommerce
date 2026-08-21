<section>
    <header class="mb-4">
        <h2 class="titulo-premium h4 text-dark mb-1">Información de la Cuenta</h2>
        <p class="text-muted small">Actualiza tus datos de contacto y foto de perfil para agilizar tus reservas.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <!-- 🔥 IMPORTANTE: enctype permite el envío de archivos (fotos) -->
    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="form-cargando">
        @csrf
        @method('patch')

        <!-- ZONA DE FOTO DE PERFIL -->
        <div class="mb-4 text-center">
            <div class="position-relative d-inline-block">
                <!-- Imagen Actual o Placeholder -->
                @if($user->avatar)
                    <img id="avatar-preview" src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="rounded-circle object-fit-cover shadow-sm" style="width: 130px; height: 130px; border: 3px solid var(--color_primario);">
                @else
                    <div id="avatar-placeholder" class="rounded-circle d-flex align-items-center justify-content-center shadow-sm bg-light text-secondary" style="width: 130px; height: 130px; border: 3px solid var(--color_primario); font-size: 3.5rem;">
                        <i class="fa-regular fa-user"></i>
                    </div>
                    <img id="avatar-preview" src="#" alt="Avatar" class="rounded-circle object-fit-cover shadow-sm d-none" style="width: 130px; height: 130px; border: 3px solid var(--color_primario);">
                @endif
                
                <!-- Botón de Cámara superpuesto -->
                <label for="avatar" class="position-absolute bottom-0 end-0 bg-dark text-white rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 40px; height: 40px; cursor: pointer; transition: all 0.3s ease;" title="Cambiar foto">
                    <i class="fa-solid fa-camera"></i>
                </label>
                <!-- Input real oculto -->
                <input type="file" id="avatar" name="avatar" class="d-none" accept="image/png, image/jpeg, image/jpg, image/webp" onchange="previewImage(event)">
            </div>
            @error('avatar')
                <div class="text-danger small mt-2 fw-bold">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <!-- NOMBRE -->
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label fw-bold small text-muted text-uppercase">Nombre Completo</label>
                <input type="text" class="form-control shadow-sm" id="name" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name">
                @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <!-- CORREO -->
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label fw-bold small text-muted text-uppercase">Correo Electrónico</label>
                <input type="email" class="form-control shadow-sm" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
                @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <!-- CÉDULA -->
            <div class="col-md-6 mb-3">
                <label for="cedula" class="form-label fw-bold small text-muted text-uppercase">Cédula</label>
                <input type="text" class="form-control shadow-sm" id="cedula" name="cedula" value="{{ old('cedula', $user->cedula) }}" placeholder="Ej: 17xxxxxx">
                @error('cedula') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <!-- TELÉFONO -->
            <div class="col-md-6 mb-3">
                <label for="telefono" class="form-label fw-bold small text-muted text-uppercase">Teléfono / WhatsApp</label>
                <input type="text" class="form-control shadow-sm" id="telefono" name="telefono" value="{{ old('telefono', $user->telefono) }}" placeholder="Ej: 09xxxxxx">
                @error('telefono') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <!-- DIRECCIÓN -->
            <div class="col-12 mb-4">
                <label for="direccion" class="form-label fw-bold small text-muted text-uppercase">Dirección (Opcional)</label>
                <textarea class="form-control shadow-sm" id="direccion" name="direccion" rows="2" placeholder="Sector, calle principal y número de casa">{{ old('direccion', $user->direccion) }}</textarea>
                @error('direccion') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="d-flex align-items-center gap-3 mt-2">
            <button type="submit" class="btn btn-black"><i class="fa-regular fa-floppy-disk me-1"></i> Guardar Cambios</button>
        </div>
    </form>
</section>

<!-- Script para mostrar la foto apenas la seleccionas -->
<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const preview = document.getElementById('avatar-preview');
            const placeholder = document.getElementById('avatar-placeholder');
            
            preview.src = reader.result;
            preview.classList.remove('d-none');
            if(placeholder) placeholder.classList.add('d-none');
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>