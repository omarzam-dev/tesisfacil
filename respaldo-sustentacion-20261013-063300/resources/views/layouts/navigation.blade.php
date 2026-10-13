<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="h-8 w-auto text-gray-800" />
                    </a>
                </div>

                <div class="hidden space-x-1 sm:-my-px sm:ms-10 sm:flex">
                    <a href="{{ route('dashboard') }}"
                        class="inline-flex items-center px-3 h-16 text-sm font-medium border-b-2 transition
                            {{ request()->routeIs('dashboard') ? 'border-teal text-teal' : 'border-transparent text-gray-500 hover:text-gray-800 hover:border-gray-300' }}">
                        Inicio
                    </a>
                    <a href="{{ route('apa.index') }}"
                        class="inline-flex items-center px-3 h-16 text-sm font-medium border-b-2 transition
                            {{ request()->routeIs('apa.*') ? 'border-teal text-teal' : 'border-transparent text-gray-500 hover:text-gray-800 hover:border-gray-300' }}">
                        Corrector APA
                    </a>
                    <a href="{{ route('tesis.index') }}"
                        class="inline-flex items-center px-3 h-16 text-sm font-medium border-b-2 transition
                            {{ request()->routeIs('tesis.*') || request()->routeIs('capitulos.*') ? 'border-teal text-teal' : 'border-transparent text-gray-500 hover:text-gray-800 hover:border-gray-300' }}">
                        Mis tesis
                    </a>
                    <a href="{{ route('matriz.index') }}"
                        class="inline-flex items-center px-3 h-16 text-sm font-medium border-b-2 transition
                            {{ request()->routeIs('matriz.*') ? 'border-teal text-teal' : 'border-transparent text-gray-500 hover:text-gray-800 hover:border-gray-300' }}">
                        Matriz
                    </a>
                    <a href="{{ route('sustentacion.index') }}"
                        class="inline-flex items-center px-3 h-16 text-sm font-medium border-b-2 transition
                            {{ request()->routeIs('sustentacion.*') ? 'border-teal text-teal' : 'border-transparent text-gray-500 hover:text-gray-800 hover:border-gray-300' }}">
                        Sustentación
                    </a>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div class="ms-3 relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.outside="open = false"
                        class="flex items-center gap-1 text-sm font-medium text-gray-600 hover:text-gray-800 transition">
                        {{ Auth::user()->name }}
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>

                    <div x-show="open" x-transition style="display: none;"
                        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-50">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">Mi perfil</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-gray-100">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="block ps-3 pe-4 py-2 text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-teal bg-teal/5 border-l-4 border-teal' : 'text-gray-500' }}">Inicio</a>
            <a href="{{ route('apa.index') }}" class="block ps-3 pe-4 py-2 text-sm font-medium {{ request()->routeIs('apa.*') ? 'text-teal bg-teal/5 border-l-4 border-teal' : 'text-gray-500' }}">Corrector APA</a>
            <a href="{{ route('tesis.index') }}" class="block ps-3 pe-4 py-2 text-sm font-medium {{ request()->routeIs('tesis.*') ? 'text-teal bg-teal/5 border-l-4 border-teal' : 'text-gray-500' }}">Mis tesis</a>
            <a href="{{ route('matriz.index') }}" class="block ps-3 pe-4 py-2 text-sm font-medium {{ request()->routeIs('matriz.*') ? 'text-teal bg-teal/5 border-l-4 border-teal' : 'text-gray-500' }}">Matriz de consistencia</a>
            <a href="{{ route('sustentacion.index') }}" class="block ps-3 pe-4 py-2 text-sm font-medium {{ request()->routeIs('sustentacion.*') ? 'text-teal bg-teal/5 border-l-4 border-teal' : 'text-gray-500' }}">Simulador de sustentación</a>
        </div>
        <div class="pt-4 pb-1 border-t border-gray-100">
            <div class="px-4"><p class="font-medium text-sm text-gray-800">{{ Auth::user()->name }}</p></div>
            <div class="mt-2 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block ps-3 pe-4 py-2 text-sm text-gray-500">Mi perfil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full text-left block ps-3 pe-4 py-2 text-sm text-gray-500">Cerrar sesión</button>
                </form>
            </div>
        </div>
    </div>
</nav>
