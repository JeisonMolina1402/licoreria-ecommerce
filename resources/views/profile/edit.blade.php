@extends('layouts.tienda')

@section('titulo', 'Mi Perfil')

@section('content')
    <div class="container py-5 mt-4">
        <h1 class="titulo-premium mb-4 text-center" style="color: var(--color_primario);">Mi Perfil</h1>

        <div class="row justify-content-center">
            <div class="col-lg-8">

                <!-- TARJETA 1: Información Principal (La que acabamos de mejorar) -->
                <div class="card shadow-sm border-0 mb-4 rounded-4">
                    <div class="card-body p-4 p-md-5">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <!-- TARJETA 2: Contraseña (Opcional, las dejamos por defecto de Breeze) -->
                <div class="card shadow-sm border-0 mb-4 rounded-4">
                    <div class="card-body p-4 p-md-5">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <!-- TARJETA 3: Eliminar Cuenta -->
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4 p-md-5">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SCRIPT PARA SPINNERS, OJITOS Y SWEETALERT  -->
    <!-- ========================================== -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // 1. ALERTAS ELEGANTES AL GUARDAR DATOS (ÉXITO)
            @if (session('status') === 'profile-updated')
                Swal.fire({
                    icon: 'success',
                    title: '¡Perfil Actualizado!',
                    text: 'Tus datos personales y foto se han guardado correctamente.',
                    showConfirmButton: false,
                    timer: 3000,
                    customClass: {
                        popup: 'rounded-4 shadow-lg border-0'
                    }
                });
            @endif

            @if (session('status') === 'password-updated')
                Swal.fire({
                    icon: 'success',
                    title: '¡Contraseña Segura!',
                    text: 'Tu contraseña ha sido actualizada con éxito.',
                    showConfirmButton: false,
                    timer: 3000,
                    customClass: {
                        popup: 'rounded-4 shadow-lg border-0'
                    }
                });
            @endif
            // 2. 🔥 NUEVO: ALERTAS DE ERROR Y AUTO-SCROLL (El definitivo)
            @if ($errors->default->any() || $errors->updatePassword->any() || $errors->userDeletion->any())
                Swal.fire({
                    icon: 'error',
                    title: '¡Oops, faltan detalles!',
                    text: 'Por favor, revisa los campos marcados en rojo en el formulario.',
                    confirmButtonColor: '#1a1a1a',
                    confirmButtonText: 'Entendido',
                    customClass: {
                        popup: 'rounded-4 shadow-lg border-0'
                    },
                    // didClose se ejecuta mágicamente justo cuando la alerta desaparece por completo
                    didClose: () => {
                        // 🔥 EL SECRETO: Buscamos el error DENTRO de las tarjetas (.card), ignorando el menú superior
                        const primerError = document.querySelector('.card .text-danger');
                        if (primerError) {
                            const posicionY = primerError.getBoundingClientRect().top + window.scrollY -
                                280;
                            window.scrollTo({
                                top: posicionY,
                                behavior: 'smooth'
                            });
                        }
                    }
                });
            @endif

            // 3. SPINNERS PARA LOS BOTONES (Anti doble-clic)
            const forms = document.querySelectorAll('.form-cargando');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const btn = this.querySelector('button[type="submit"]');
                    if (btn) {
                        btn.disabled = true;
                        btn.innerHTML =
                            '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Procesando...';
                        btn.classList.add('opacity-75');
                    }
                });
            });

            // 4. LÓGICA PARA EL OJITO DE CONTRASEÑA
            const toggleButtons = document.querySelectorAll('.toggle-password');
            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const input = document.getElementById(this.dataset.target);
                    const icon = this.querySelector('i');

                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            });

        });
    </script>
@endsection
