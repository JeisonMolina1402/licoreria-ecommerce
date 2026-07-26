<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <!-- ========================================== -->
    <!-- FORMULARIO 1: REENVÍO DE VERIFICACIÓN      -->
    <!-- ========================================== -->
    {{-- Formulario oculto y aislado. Solo maneja la petición de reenviar el email --}}
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <!-- ========================================== -->
    <!-- FORMULARIO 2: ACTUALIZACIÓN DE PERFIL      -->
    <!-- ========================================== -->
    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        
        {{-- 🛡️ METHOD SPOOFING (PATCH): Semánticamente correcto para una actualización parcial de la base de datos --}}
        @method('patch')

        <!-- CAMPO: NOMBRE -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            
            {{-- HELPER OLD: Funciona como un fallback. Intenta poner el valor anterior si hubo un error. Si no hubo error, trae el nombre actual de la base de datos (Ej: Jeison Molina Vargas). --}}
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- CAMPO: CORREO ELECTRÓNICO -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            {{-- LÓGICA DE VERIFICACIÓN DE CORREO --}}
            {{-- Verifica dos cosas: 1. Si el sistema tiene activada la regla de verificar emails (Interface) y 2. Si el usuario actual AÚN NO lo ha verificado --}}
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        {{-- BOTÓN DESACOPLADO (form="send-verification") --}}
                        {{-- Aunque este botón está físicamente dentro del Formulario 2, este atributo HTML5 le dice al navegador que al hacer clic, debe enviar el Formulario 1 (el oculto de arriba) --}}
                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    {{-- MENSAJE DE ÉXITO DE REENVÍO --}}
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- BOTÓN DE GUARDAR Y NOTIFICACIÓN VISUAL -->
        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            {{-- MAGIA ALPINE.JS: Mismo comportamiento del archivo de contraseña. Muestra "Saved" y lo desvanece a los 2 segundos sin recargar --}}
            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>