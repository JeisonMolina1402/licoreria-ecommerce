{{-- ALPINE.JS: x-data inicializa un estado reactivo local. 'open: false' significa que el menú móvil empieza cerrado --}}
<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    
    <!-- Navegación Principal (Escritorio) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                
                <!-- Logotipo de la App -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        {{-- COMPONENTE BLADE: Inyecta el logo desde resources/views/components/application-logo.blade.php --}}
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Enlaces de Navegación -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    
                    {{-- COMPONENTE BLADE CON PASO DE PROPIEDADES (PROPS) --}}
                    {{-- Se le pasa la ruta actual (href) y se evalúa si está activo mediante request()->routeIs() --}}
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Menú Dropdown de Ajustes (Perfil de Usuario) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                
                {{-- COMPONENTE DROPDOWN: Maneja la lógica de abrir/cerrar automáticamente --}}
                <x-dropdown align="right" width="48">
                    
                    {{-- SLOT 'trigger': Lo que el usuario presiona para abrir el menú --}}
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <!-- Icono de flecha (SVG) -->
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    {{-- SLOT 'content': El contenido del menú desplegable --}}
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- LOGOUT / CERRAR SESIÓN -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            {{-- TRUCO DE JAVASCRIPT: Un enlace <a> normalmente hace un GET. --}}
                            {{-- Este onclick previene la redirección nativa (preventDefault) y fuerza al formulario padre (closest) a hacer un POST --}}
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Botón de Hamburguesa (Solo Móviles) -->
            <div class="-me-2 flex items-center sm:hidden">
                
                {{-- MAGIA DE ALPINE.JS: @click interviene el DOM. Al hacer clic, invierte el valor de 'open' (de false a true, o viceversa) --}}
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        {{-- BINDING DINÁMICO DE CLASES: Dependiendo del estado de 'open', oculta o muestra las rayas de la hamburguesa o la X para cerrar --}}
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Menú de Navegación Responsivo (Desplegable Móvil) -->
    {{-- Dependiendo de Alpine.js, se despliega ('block') o se oculta ('hidden') --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Opciones de Configuración Responsivas -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- LOGOUT MÓVIL (Misma lógica de POST forzado por JS) -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>