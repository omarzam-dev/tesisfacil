<aside x-data="{ open: false }" class="lg:w-60 lg:shrink-0">
    <!-- Topbar móvil -->
    <div class="lg:hidden flex items-center justify-between h-16 px-4 bg-white border-b border-borde">
        <a href="{{ route('dashboard') }}">
            <x-application-logo class="h-7 w-auto text-gray-800" />
        </a>
        <button @click="open = !open" class="p-2 text-gray-500">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <!-- Sidebar -->
    <div :class="open ? 'block' : 'hidden'" class="lg:block bg-white border-r border-borde lg:h-screen lg:sticky lg:top-0 flex flex-col">
        <div class="hidden lg:flex items-center h-16 px-5 border-b border-borde">
            <a href="{{ route('dashboard') }}">
                <x-application-logo class="h-7 w-auto text-gray-800" />
            </a>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1">
            @php
                $items = [
                    ['route' => 'dashboard', 'match' => 'dashboard', 'label' => 'Inicio', 'icon' => 'home', 'tono' => 'teal'],
                    ['route' => 'apa.index', 'match' => 'apa.*', 'label' => 'Corrector APA / ISO', 'icon' => 'document', 'tono' => 'celeste'],
                    ['route' => 'tesis.index', 'match' => 'tesis.*|capitulos.*', 'label' => 'Mis tesis', 'icon' => 'book', 'tono' => 'verde'],
                    ['route' => 'matriz.index', 'match' => 'matriz.*', 'label' => 'Matriz de consistencia', 'icon' => 'matriz', 'tono' => 'naranja', 'chip' => 'IA'],
                    ['route' => 'sustentacion.index', 'match' => 'sustentacion.*', 'label' => 'Simulador de sustentación', 'icon' => 'sustentacion', 'tono' => 'rosa', 'chip' => 'IA'],
                ];
                $icons = [
                    'home' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                    'document' => 'M9 12h6m-6 4h6M9 8h1m5 12H7a2 2 0 01-2-2V6a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V18a2 2 0 01-2 2z',
                    'matriz' => 'M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',
                    'sustentacion' => 'M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z',
                    'book' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                    'regla' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4',
                    'admin' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                ];
                // Clases completas por color (Tailwind necesita verlas escritas enteras)
                $tonos = [
                    'teal'    => ['fila' => 'bg-teal-suave text-teal',       'hover' => 'hover:bg-teal-suave/60',    'chip' => 'bg-teal-suave text-teal',       'chipOn' => 'bg-teal text-white'],
                    'celeste' => ['fila' => 'bg-celeste-suave text-celeste', 'hover' => 'hover:bg-celeste-suave/60', 'chip' => 'bg-celeste-suave text-celeste', 'chipOn' => 'bg-celeste text-white'],
                    'verde'   => ['fila' => 'bg-verde-suave text-verde',     'hover' => 'hover:bg-verde-suave/60',   'chip' => 'bg-verde-suave text-verde',     'chipOn' => 'bg-verde text-white'],
                    'naranja' => ['fila' => 'bg-naranja-suave text-naranja', 'hover' => 'hover:bg-naranja-suave/60', 'chip' => 'bg-naranja-suave text-naranja', 'chipOn' => 'bg-naranja text-white'],
                    'rosa'    => ['fila' => 'bg-rosa-suave text-rosa',       'hover' => 'hover:bg-rosa-suave/60',    'chip' => 'bg-rosa-suave text-rosa',       'chipOn' => 'bg-rosa text-white'],
                    'lavanda' => ['fila' => 'bg-lavanda-suave text-lavanda', 'hover' => 'hover:bg-lavanda-suave/60', 'chip' => 'bg-lavanda-suave text-lavanda', 'chipOn' => 'bg-lavanda text-white'],
                ];
                if (Auth::user()->isAdmin()) {
                    $items[] = ['route' => 'admin.index', 'match' => 'admin.index|admin.payments*|admin.plans*|admin.payment-methods*|admin.subscriptions*|admin.users*', 'label' => 'Panel admin', 'icon' => 'admin', 'tono' => 'lavanda', 'separado' => true];
                    $items[] = ['route' => 'admin.perfiles.index', 'match' => 'admin.perfiles.*', 'label' => 'Perfiles de formato', 'icon' => 'regla', 'tono' => 'lavanda'];
                }
            @endphp

            @foreach ($items as $item)
                @php
                    $activo = request()->routeIs(explode('|', $item['match']));
                    $t = $tonos[$item['tono']];
                @endphp
                @if (! empty($item['separado']))
                    <div class="pt-3 mt-3 border-t border-borde"></div>
                @endif
                <a href="{{ route($item['route']) }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                        {{ $activo ? $t['fila'] . ' font-semibold' : 'text-gray-600 hover:text-gray-800 ' . $t['hover'] }}">
                    <span class="w-7 h-7 rounded-md flex items-center justify-center shrink-0 {{ $activo ? $t['chipOn'] . ' shadow-sm' : $t['chip'] }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$item['icon']] }}" />
                        </svg>
                    </span>
                    <span class="flex-1">{{ $item['label'] }}</span>
                    @isset($item['chip'])
                        <span class="text-[10px] font-semibold uppercase tracking-wide bg-white/80 px-1.5 py-0.5 rounded">{{ $item['chip'] }}</span>
                    @endisset
                </a>

                {{-- "Detector de IA" (próximamente) va justo después del simulador --}}
                @if ($item['route'] === 'sustentacion.index')
                    <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed" title="Próximo lanzamiento">
                        <span class="w-7 h-7 rounded-md flex items-center justify-center shrink-0 bg-gray-100 text-gray-300">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </span>
                        <span class="flex-1">Detector de IA</span>
                        <span class="text-[10px] font-semibold uppercase tracking-wide bg-gray-100 text-gray-400 px-1.5 py-0.5 rounded">Próximo</span>
                    </div>
                @endif
            @endforeach
        </nav>

        <div class="p-3 border-t border-borde" x-data="{ open: false }">
            <button @click="open = !open" @click.outside="open = false"
                class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition">
                <span class="w-7 h-7 rounded-full bg-lavanda-suave text-lavanda flex items-center justify-center text-xs font-semibold shrink-0">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </span>
                <span class="flex-1 text-left truncate">{{ Auth::user()->name }}</span>
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>

            <div x-show="open" x-transition style="display: none;" class="mt-1 bg-white rounded-lg shadow-lg border border-gray-100 py-1">
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-50">Mi perfil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left block px-3 py-2 text-sm text-gray-600 hover:bg-gray-50">Cerrar sesión</button>
                </form>
            </div>
        </div>
    </div>
</aside>
