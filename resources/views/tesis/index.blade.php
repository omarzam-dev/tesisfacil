<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-heading text-xl text-gray-800">Mis tesis</h2>
            <a href="{{ route('tesis.create') }}" class="bg-teal text-white text-sm font-medium px-4 py-2 rounded-lg hover:opacity-90">
                + Nueva tesis
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4">

        @if (session('error'))
            <div class="mb-4 p-3 bg-rosa/10 text-rosa rounded-lg text-sm">{{ session('error') }}</div>
        @endif

        <div class="space-y-3">
            @forelse ($tesis as $t)
                @php
                    $badge = match($t->estado) {
                        'aprobada' => ['label' => 'Aprobada', 'color' => 'bg-verde/10 text-verde'],
                        'en_revision' => ['label' => 'En revisión', 'color' => 'bg-naranja/10 text-naranja'],
                        default => ['label' => 'En progreso', 'color' => 'bg-teal/10 text-teal'],
                    };
                @endphp
                <a href="{{ route('tesis.show', $t) }}" class="block bg-white rounded-xl shadow p-5 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-heading text-gray-800">{{ $t->titulo }}</p>
                            @if ($t->linea_investigacion)
                                <p class="text-sm text-gray-500">{{ $t->linea_investigacion }}</p>
                            @endif
                        </div>
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $badge['color'] }}">{{ $badge['label'] }}</span>
                    </div>
                    <div class="mt-3">
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-verde h-2 rounded-full" style="width: {{ $t->progreso() }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $t->progreso() }}% de capítulos aprobados</p>
                    </div>
                </a>
            @empty
                <div class="bg-white rounded-xl shadow p-8 text-center text-gray-500">
                    <p>Aún no has creado ninguna tesis.</p>
                    <a href="{{ route('tesis.create') }}" class="text-teal hover:underline text-sm">Crea tu primera tesis</a>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
