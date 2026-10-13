<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading text-2xl text-gray-800">Hola, {{ Auth::user()->name }}</h2>
        <p class="text-sm text-gray-500 mt-1">¿Qué quieres hacer hoy?</p>
    </x-slot>

    @php
        // Cada tarjeta con su color pastel (clases escritas completas para Tailwind)
        $tarjetas = [
            [
                'ruta' => 'apa.index', 'titulo' => 'Corrector APA / ISO', 'accion' => 'Empezar',
                'texto' => 'Revisa formato, citas y referencias en APA o ISO 690.',
                'caja' => 'bg-celeste-suave border-celeste/20 hover:border-celeste/50',
                'icono' => 'text-celeste group-hover:bg-celeste group-hover:text-white', 'color' => 'text-celeste',
                'svg' => 'M9 12h6m-6 4h6M9 8h1m5 12H7a2 2 0 01-2-2V6a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V18a2 2 0 01-2 2z',
            ],
            [
                'ruta' => 'tesis.index', 'titulo' => 'Mis tesis', 'accion' => 'Ver mis tesis',
                'texto' => 'Gestiona capítulos y controla versiones de tu tesis.',
                'caja' => 'bg-verde-suave border-verde/20 hover:border-verde/50',
                'icono' => 'text-verde group-hover:bg-verde group-hover:text-white', 'color' => 'text-verde',
                'svg' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
            ],
            [
                'ruta' => 'matriz.index', 'titulo' => 'Matriz de consistencia', 'accion' => 'Revisar mi matriz', 'ia' => true,
                'texto' => 'La IA revisa que tus problemas, objetivos e hipótesis calcen entre sí.',
                'caja' => 'bg-naranja-suave border-naranja/20 hover:border-naranja/50',
                'icono' => 'text-naranja group-hover:bg-naranja group-hover:text-white', 'color' => 'text-naranja',
                'svg' => 'M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',
            ],
            [
                'ruta' => 'sustentacion.index', 'titulo' => 'Simulador de sustentación', 'accion' => 'Practicar ahora', 'ia' => true,
                'texto' => 'Practica con un jurado de IA y recibe nota y consejos por cada respuesta.',
                'caja' => 'bg-rosa-suave border-rosa/20 hover:border-rosa/50',
                'icono' => 'text-rosa group-hover:bg-rosa group-hover:text-white', 'color' => 'text-rosa',
                'svg' => 'M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z',
            ],
        ];
    @endphp

    <div class="py-8 px-4 sm:px-6 max-w-5xl">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($tarjetas as $t)
                <a href="{{ route($t['ruta']) }}" class="card card-hover group relative {{ $t['caja'] }}">
                    @if (! empty($t['ia']))
                        <span class="absolute top-5 right-5 text-[10px] font-semibold uppercase tracking-wide bg-white/80 {{ $t['color'] }} px-2 py-0.5 rounded-full">IA</span>
                    @endif
                    <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center mb-4 group-hover:scale-105 transition-all {{ $t['icono'] }}">
                        <svg class="w-6 h-6 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $t['svg'] }}" />
                        </svg>
                    </div>
                    <p class="font-heading text-gray-800 text-lg">{{ $t['titulo'] }}</p>
                    <p class="text-sm text-gray-600 mt-1">{{ $t['texto'] }}</p>
                    <p class="text-xs font-semibold mt-4 flex items-center gap-1 {{ $t['color'] }}">
                        {{ $t['accion'] }}
                        <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                    </p>
                </a>
            @endforeach

            <div class="card relative bg-lavanda-suave/60 border-lavanda/20 opacity-70">
                <span class="absolute top-5 right-5 text-[10px] font-semibold uppercase tracking-wide bg-white/80 text-lavanda px-2 py-0.5 rounded-full">Próximo</span>
                <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center mb-4 text-lavanda">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                </div>
                <p class="font-heading text-gray-700 text-lg">Detector de IA</p>
                <p class="text-sm text-gray-500 mt-1">Próximo lanzamiento.</p>
            </div>
        </div>
    </div>
</x-app-layout>
