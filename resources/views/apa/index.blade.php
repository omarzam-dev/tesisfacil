<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading text-2xl text-gray-800">Corrector APA</h2>
                <p class="text-sm text-gray-500 mt-1">Historial de tus revisiones</p>
            </div>
            <a href="{{ route('apa.create') }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                Nueva revisión
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4">
        <div class="card !p-0 divide-y divide-gray-100 overflow-hidden">
            @forelse ($revisiones as $revision)
                @php
                    $badge = match($revision->estado) {
                        'completado' => ($revision->resultado_estructura['resumen']['aprobado'] ?? false) && ($revision->resultado_citas['resumen']['aprobado'] ?? true) && ($revision->resultado_referencias['resumen']['aprobado'] ?? true)
                            ? ['label' => 'Aprobado', 'class' => 'badge-verde']
                            : ['label' => 'Con errores', 'class' => 'badge-rosa'],
                        'error' => ['label' => 'Error al procesar', 'class' => 'badge-rosa'],
                        default => ['label' => 'Procesando', 'class' => 'badge-naranja'],
                    };
                @endphp
                <a href="{{ route('apa.show', $revision) }}" class="flex items-center justify-between p-5 hover:bg-gray-50 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-teal/10 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M9 8h1m5 12H7a2 2 0 01-2-2V6a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V18a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 text-sm">{{ $revision->nombre_archivo }}</p>
                            <p class="text-xs text-gray-500">{{ $revision->edicion_apa }} · {{ $revision->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                </a>
            @empty
                <div class="p-14 text-center text-gray-500">
                    <div class="w-14 h-14 rounded-full bg-teal/10 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M9 8h1m5 12H7a2 2 0 01-2-2V6a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V18a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="font-heading text-gray-800 mb-1">Aún no has revisado ningún documento</p>
                    <p class="text-sm text-gray-500 mb-5">Sube tu primer documento y en segundos verás qué corregir.</p>
                    <a href="{{ route('apa.create') }}" class="btn-primary inline-flex">Sube tu primer documento</a>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $revisiones->links() }}
        </div>
    </div>
</x-app-layout>
