<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading text-2xl text-gray-800">Hola, {{ Auth::user()->name }} 👋</h2>
        <p class="text-sm text-gray-500 mt-1">¿Qué quieres hacer hoy?</p>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 max-w-4xl">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <a href="{{ route('apa.index') }}" class="card card-hover group">
                <div class="w-12 h-12 rounded-xl bg-teal/10 flex items-center justify-center mb-4 group-hover:bg-teal group-hover:scale-105 transition-all">
                    <svg class="w-6 h-6 text-teal group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M9 8h1m5 12H7a2 2 0 01-2-2V6a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V18a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <p class="font-heading text-gray-800 text-lg group-hover:text-teal transition">Corrector APA</p>
                <p class="text-sm text-gray-500 mt-1">Revisa estructura, citas y referencias de tu documento.</p>
                <p class="text-xs font-semibold text-teal mt-4 flex items-center gap-1">
                    Empezar
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                </p>
            </a>

            <a href="{{ route('tesis.index') }}" class="card card-hover group">
                <div class="w-12 h-12 rounded-xl bg-verde/10 flex items-center justify-center mb-4 group-hover:bg-verde group-hover:scale-105 transition-all">
                    <svg class="w-6 h-6 text-verde group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <p class="font-heading text-gray-800 text-lg group-hover:text-verde transition">Mis tesis</p>
                <p class="text-sm text-gray-500 mt-1">Gestiona capítulos y controla versiones de tu tesis.</p>
                <p class="text-xs font-semibold text-verde mt-4 flex items-center gap-1">
                    Ver mis tesis
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                </p>
            </a>

            <a href="{{ route('matriz.index') }}" class="card card-hover group relative">
                <span class="absolute top-5 right-5 badge badge-teal">IA</span>
                <div class="w-12 h-12 rounded-xl bg-naranja/10 flex items-center justify-center mb-4 group-hover:bg-naranja group-hover:scale-105 transition-all">
                    <svg class="w-6 h-6 text-naranja group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <p class="font-heading text-gray-800 text-lg group-hover:text-naranja transition">Matriz de consistencia</p>
                <p class="text-sm text-gray-500 mt-1">La IA revisa que tus problemas, objetivos e hipótesis calcen entre sí.</p>
                <p class="text-xs font-semibold text-naranja mt-4 flex items-center gap-1">
                    Revisar mi matriz
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                </p>
            </a>

            <a href="{{ route('sustentacion.index') }}" class="card card-hover group relative">
                <span class="absolute top-5 right-5 badge badge-teal">IA</span>
                <div class="w-12 h-12 rounded-xl bg-rosa/10 flex items-center justify-center mb-4 group-hover:bg-rosa group-hover:scale-105 transition-all">
                    <svg class="w-6 h-6 text-rosa group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                    </svg>
                </div>
                <p class="font-heading text-gray-800 text-lg group-hover:text-rosa transition">Simulador de sustentación</p>
                <p class="text-sm text-gray-500 mt-1">Practica con un jurado de IA y recibe nota y consejos por cada respuesta.</p>
                <p class="text-xs font-semibold text-rosa mt-4 flex items-center gap-1">
                    Practicar ahora
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                </p>
            </a>

            <div class="card opacity-60 relative">
                <span class="absolute top-5 right-5 badge badge-gris">Próximo</span>
                <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                </div>
                <p class="font-heading text-gray-500 text-lg">Detector de IA</p>
                <p class="text-sm text-gray-400 mt-1">Próximo lanzamiento.</p>
            </div>
        </div>
    </div>
</x-app-layout>
