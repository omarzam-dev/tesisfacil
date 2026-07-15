<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading text-2xl text-gray-800">Mis tesis</h2>
                <p class="text-sm text-gray-500 mt-1">Organiza tu avance capítulo por capítulo</p>
            </div>
            <a href="{{ route('tesis.create') }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                Nueva tesis
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4">

        @if (session('error'))
            <div class="alert-error mb-5">{{ session('error') }}</div>
        @endif

        <div class="space-y-4">
            @forelse ($tesis as $t)
                @php
                    $badge = match($t->estado) {
                        'aprobada' => ['label' => 'Aprobada', 'class' => 'badge-verde'],
                        'en_revision' => ['label' => 'En revisión', 'class' => 'badge-naranja'],
                        default => ['label' => 'En progreso', 'class' => 'badge-teal'],
                    };
                @endphp
                <a href="{{ route('tesis.show', $t) }}" class="card card-hover block">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-heading text-lg text-gray-800">{{ $t->titulo }}</p>
                            @if ($t->linea_investigacion)
                                <p class="text-sm text-gray-500 mt-0.5">{{ $t->linea_investigacion }}</p>
                            @endif
                        </div>
                        <span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                    </div>
                    <div class="mt-4">
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="bg-verde h-2.5 rounded-full transition-all" style="width: {{ $t->progreso() }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1.5 font-medium">{{ $t->progreso() }}% de capítulos aprobados</p>
                    </div>
                </a>
            @empty
                <div class="card text-center py-14">
                    <div class="w-14 h-14 rounded-full bg-teal/10 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <p class="font-heading text-gray-800 mb-1">Aún no has creado ninguna tesis</p>
                    <p class="text-sm text-gray-500 mb-5">Empieza en segundos — te armamos los 5 capítulos estándar automáticamente.</p>
                    <a href="{{ route('tesis.create') }}" class="btn-primary inline-flex">Crea tu primera tesis</a>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
