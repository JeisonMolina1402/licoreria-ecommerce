<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="form-cargando">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password con Ojito -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative mt-1">
                <!-- Se agregó 'pr-10' para dar espacio al icono y no tapar el texto -->
                <x-text-input id="password" class="block w-full pr-10" type="password" name="password" required
                    autocomplete="new-password" />
                <button type="button"
                    class="absolute inset-y-0 right-0 px-3 flex items-center toggle-password text-gray-500 hover:text-gray-700"
                    data-target="password">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password con Ojito -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <div class="relative mt-1">
                <x-text-input id="password_confirmation" class="block w-full pr-10" type="password"
                    name="password_confirmation" required autocomplete="new-password" />
                <button type="button"
                    class="absolute inset-y-0 right-0 px-3 flex items-center toggle-password text-gray-500 hover:text-gray-700"
                    data-target="password">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Checkbox de Términos y Condiciones -->
        <div class="block mt-4">
            <label for="terms" class="inline-flex items-center">
                <input id="terms" type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="terms"
                    required>
                <span class="ms-2 text-sm text-gray-600">
                    Acepto los <a href="{{ route('legal.terminos') }}"
                        class="font-bold text-gray-900 hover:underline">Términos y Condiciones</a> y
                    la <a href="{{ route('legal.privacidad') }}" class="font-bold text-gray-900 hover:underline">Política
                        de Privacidad</a>.
                    Privacidad</a>.
                </span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="text-sm font-bold text-gray-600 hover:underline"
                href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <!-- Botón de Registro -->
            <x-primary-button class="ms-4">
                {{ __('REGISTRARSE') }}
            </x-primary-button>
        </div>
    </form>

    <!-- JAVASCRIPT MÁGICO (Versión Tailwind CSS) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Lógica para el Ojito de Contraseña
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

            // 2. Lógica para el Spinner con Tailwind (Prevenir múltiples clics)
            const forms = document.querySelectorAll('.form-cargando');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const btn = this.querySelector('button[type="submit"]');
                    if (btn) {
                        btn.disabled = true;
                        btn.classList.add('opacity-75', 'cursor-not-allowed');
                        // Insertamos un spinner animado SVG compatible con Tailwind
                        btn.innerHTML =
                            `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> PROCESANDO...`;
                    }
                });
            });
        });
    </script>
</x-guest-layout>
