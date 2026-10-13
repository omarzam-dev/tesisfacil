<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading text-2xl text-gray-800">Revisor de matriz de consistencia</h2>
        <p class="text-sm text-gray-500 mt-1">La IA revisa que tus problemas, objetivos, hipótesis y variables sean coherentes entre sí.</p>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 max-w-4xl space-y-6">
        <div class="card flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs text-gray-500 font-medium">Usos de IA este mes</p>
                <p class="font-heading text-2xl text-gray-800">
                    {{ $user->iaUsadaEsteMes() }}<span class="text-sm text-gray-400 font-sans"> / {{ $user->limiteIa() }}</span>
                </p>
            </div>
            @if ($user->puedeUsarIa())
                <a href="{{ route('matriz.create') }}" class="btn-primary">Revisar una matriz</a>
            @else
                <a href="{{ route('subscription.plans') }}" class="btn-secondary">Ver planes con más usos</a>
            @endif
        </div>

        <p class="section-heading">Tus revisiones</p>
        <div class="card !p-0 divide-y divide-gray-100 overflow-hidden">
            @forelse ($revisiones as $r)
                <a href="{{ route('matriz.show', $r) }}" class="p-4 flex justify-between items-center gap-4 text-sm hover:bg-gray-50">
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800 truncate">{{ $r->titulo ?: 'Matriz sin título' }}</p>
                        <p class="text-gray-500 text-xs mt-0.5">{{ $r->created_at->format('d/m/Y H:i') }} · {{ \App\Models\MatrizConsistencia::ENFOQUES[$r->enfoque] ?? $r->enfoque }}</p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        @if (! is_null($r->puntaje))
                            <span class="font-heading text-lg text-gray-800">{{ $r->puntaje }}<span class="text-xs text-gray-400 font-sans">/100</span></span>
                        @endif
                        <span class="badge {{ $r->veredictoBadge() }}">{{ $r->veredictoTexto() }}</span>
                    </div>
                </a>
            @empty
                <p class="p-6 text-sm text-gray-500 text-center">Todavía no revisaste ninguna matriz.</p>
            @endforelse
        </div>

        {{ $revisiones->links() }}
    </div>
</x-app-layout>
