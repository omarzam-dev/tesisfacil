<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading text-2xl text-gray-800">Simulador de sustentación</h2>
        <p class="text-sm text-gray-500 mt-1">Practica con un jurado de IA: 5 preguntas sobre tu tesis, nota de 0 a 20 y consejos para mejorar.</p>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 max-w-4xl space-y-6">
        <div class="card flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs text-gray-500 font-medium">Usos de IA este mes</p>
                <p class="font-heading text-2xl text-gray-800">
                    {{ $user->iaUsadaEsteMes() }}<span class="text-sm text-gray-400 font-sans"> / {{ $user->limiteIa() }}</span>
                </p>
                <p class="text-xs text-gray-400 mt-1">Cada simulación completa usa 1.</p>
            </div>
            @if ($user->puedeUsarIa())
                <a href="{{ route('sustentacion.create') }}" class="btn-primary">Nueva simulación</a>
            @else
                <a href="{{ route('subscription.plans') }}" class="btn-secondary">Ver planes con más usos</a>
            @endif
        </div>

        <p class="section-heading">Tus simulaciones</p>
        <div class="card !p-0 divide-y divide-gray-100 overflow-hidden">
            @forelse ($simulaciones as $s)
                <a href="{{ route('sustentacion.show', $s) }}" class="p-4 flex justify-between items-center gap-4 text-sm hover:bg-gray-50">
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800 truncate">{{ $s->titulo }}</p>
                        <p class="text-gray-500 text-xs mt-0.5">
                            {{ $s->created_at->format('d/m/Y H:i') }} · Jurado {{ strtolower(\App\Models\SimulacionSustentacion::TONOS[$s->tono] ?? $s->tono) }}
                        </p>
                    </div>
                    <div class="shrink-0 text-right">
                        @if ($s->finalizada())
                            <span class="font-heading text-xl {{ \App\Models\SimulacionSustentacion::colorNota($s->nota_final) }}">{{ number_format($s->nota_final, 1) }}</span>
                            <span class="text-xs text-gray-400">/20</span>
                        @else
                            <span class="badge badge-naranja">En curso · {{ $s->respondidas() }}/{{ $s->totalPreguntas() }}</span>
                        @endif
                    </div>
                </a>
            @empty
                <p class="p-6 text-sm text-gray-500 text-center">Todavía no hiciste ninguna simulación.</p>
            @endforelse
        </div>

        {{ $simulaciones->links() }}
    </div>
</x-app-layout>
