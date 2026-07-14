<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-heading text-xl text-gray-800">Corrector APA — Mis revisiones</h2>
            <a href="{{ route('apa.create') }}" class="bg-teal text-white text-sm font-medium px-4 py-2 rounded-lg hover:opacity-90">
                + Nueva revisión
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4">
        <div class="bg-white rounded-xl shadow divide-y">
            @forelse ($revisiones as $revision)
                @php
                    $badge = match($revision->estado) {
                        'completado' => ($revision->resultado_estructura['resumen']['aprobado'] ?? false) && ($revision->resultado_citas['resumen']['aprobado'] ?? true) && ($revision->resultado_referencias['resumen']['aprobado'] ?? true)
                            ? ['label' => 'Aprobado', 'color' => 'bg-verde/10 text-verde']
                            : ['label' => 'Con errores', 'color' => 'bg-rosa/10 text-rosa'],
                        'error' => ['label' => 'Error al procesar', 'color' => 'bg-rosa/10 text-rosa'],
                        default => ['label' => 'Procesando', 'color' => 'bg-naranja/10 text-naranja'],
                    };
                @endphp
                <a href="{{ route('apa.show', $revision) }}" class="flex items-center justify-between p-4 hover:bg-gray-50">
                    <div>
                        <p class="font-medium text-gray-800">{{ $revision->nombre_archivo }}</p>
                        <p class="text-sm text-gray-500">{{ $revision->edicion_apa }} · {{ $revision->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $badge['color'] }}">
                        {{ $badge['label'] }}
                    </span>
                </a>
            @empty
                <div class="p-8 text-center text-gray-500">
                    <p>Aún no has revisado ningún documento.</p>
                    <a href="{{ route('apa.create') }}" class="text-teal hover:underline text-sm">Sube tu primer documento</a>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $revisiones->links() }}
        </div>
    </div>
</x-app-layout>
