{{-- LLAMADA AL COMPONENTE MAESTRO --}}
{{-- Esta etiqueta le dice a Laravel: "Toma todo lo que hay aquí adentro e inyéctalo en la variable $slot del archivo resources/views/layouts/app.blade.php" --}}
<x-app-layout>
    
    {{-- SLOT CON NOMBRE (HEADER) --}}
    {{-- Esto inyecta el título específicamente en una sección superior (si el layout estuviera configurado para mostrarlo). Utiliza el helper de traducción __() --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <!-- CONTENEDOR PRINCIPAL DEL PERFIL -->
    <div class="py-12">
        
        {{-- space-y-6 es una clase de Tailwind que automáticamente pone un margen vertical entre los tres bloques (partials), manteniendo la separación perfecta sin usar CSS personalizado --}}
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- ========================================== -->
            <!-- BLOQUE 1: ACTUALIZAR DATOS DEL PERFIL      -->
            <!-- ========================================== -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    {{-- DIRECTIVA @include: Llama y renderiza el código exacto del archivo update-profile-information-form.blade.php --}}
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- ========================================== -->
            <!-- BLOQUE 2: ACTUALIZAR CONTRASEÑA            -->
            <!-- ========================================== -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    {{-- DIRECTIVA @include: Llama al formulario de seguridad (PUT) --}}
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- ========================================== -->
            <!-- BLOQUE 3: ELIMINAR CUENTA                  -->
            <!-- ========================================== -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    {{-- DIRECTIVA @include: Llama al formulario de destrucción de cuenta (DELETE) con su respectivo Modal de confirmación --}}
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>