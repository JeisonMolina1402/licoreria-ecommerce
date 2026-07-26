<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{-- Traducción dinámica del título --}}
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    {{-- FORMULARIO DE ACTUALIZACIÓN --}}
    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        
        {{-- 🛡️ SEGURIDAD CSRF: Obligatorio para proteger la petición contra ataques de origen cruzado --}}
        @csrf
        
        {{-- 🛡️ ARQUITECTURA RESTful: Falsificación del método a PUT (Actualización de recurso existente) --}}
        @method('put')

        <!-- CAMPO: CONTRASEÑA ACTUAL -->
        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            {{-- autocomplete="current-password" ayuda a los gestores de contraseñas del navegador a rellenar el campo --}}
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            
            {{-- ERROR BAG ESPECÍFICO: Captura solo los errores de 'current_password' dentro de la bolsa 'updatePassword' --}}
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <!-- CAMPO: NUEVA CONTRASEÑA -->
        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" />
            {{-- autocomplete="new-password" le dice al navegador que este campo es para una clave nueva (para sugerir claves seguras) --}}
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <!-- CAMPO: CONFIRMAR NUEVA CONTRASEÑA -->
        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- BOTÓN DE GUARDADO Y MENSAJE DE ÉXITO -->
        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            {{-- LÓGICA CONDICIONAL DE SESIÓN: Solo se dibuja si el controlador devolvió el estado 'password-updated' en la sesión (Flash Data) --}}
            @if (session('status') === 'password-updated')
                
                {{-- MAGIA DE ALPINE.JS (Micro-interacción visual) --}}
                <p
                    {{-- x-data: Inicializa la variable 'show' en verdadero --}}
                    x-data="{ show: true }"
                    {{-- x-show: Muestra el elemento basado en la variable 'show' --}}
                    x-show="show"
                    {{-- x-transition: Le da un efecto de desvanecimiento suave (fade) --}}
                    x-transition
                    {{-- x-init: Apenas el elemento se crea en el DOM, inicia un temporizador de JavaScript que cambia 'show' a falso después de 2000 milisegundos (2 segundos), ocultando el texto mágicamente --}}
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>