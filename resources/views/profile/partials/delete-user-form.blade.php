<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{-- HELPER DE TRADUCCIÓN: La función __() busca este texto en los archivos de idioma (lang/). Si tienes el sistema en español, lo cambiará automáticamente sin tocar este código --}}
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    {{-- BOTÓN DISPARADOR (ALPINE.JS) --}}
    {{-- x-on:click.prevent evita que el botón recargue la página. $dispatch emite un evento global (como un grito) que dice "¡Abran el modal llamado 'confirm-user-deletion'!" --}}
    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Delete Account') }}</x-danger-button>

    {{-- COMPONENTE MODAL DE BREEZE --}}
    {{-- El atributo :show tiene lógica PHP inyectada. Si el servidor devolvió un error específico de "userDeletion" (ej. contraseña incorrecta), el modal se fuerza a mantenerse abierto --}}
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        
        {{-- FORMULARIO DE ELIMINACIÓN --}}
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            
            {{-- 🛡️ SEGURIDAD CSRF: Obligatorio en todo formulario POST/PUT/PATCH/DELETE --}}
            @csrf
            
            {{-- 🛡️ METHOD SPOOFING: Transforma este POST en un DELETE para el Router de Laravel --}}
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-6">
                {{-- Etiqueta oculta para lectores de pantalla (Accesibilidad web) --}}
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                {{-- INPUT DE CONTRASEÑA (Re-autenticación) --}}
                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Password') }}"
                />

                {{-- COMPONENTE DE ERROR: Solo se dibuja si la variable $errors trae un fallo para 'password' en la bolsa 'userDeletion' --}}
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                
                {{-- BOTÓN CANCELAR (ALPINE.JS) --}}
                {{-- Emite el evento '$dispatch('close')' para ocultar la ventana sin hacer nada más --}}
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                {{-- BOTÓN CONFIRMAR: Al no tener type="button", actúa como type="submit" y envía el formulario al servidor --}}
                <x-danger-button class="ms-3">
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>