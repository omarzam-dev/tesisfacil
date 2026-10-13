<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading text-2xl text-gray-800">Planes</h2>
        <p class="text-sm text-gray-500 mt-1">Elige el plan que se ajuste a tu ritmo de tesis</p>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 max-w-3xl">
        @if (session('success'))
            <div class="alert-success mb-5">{{ session('success') }}</div>
        @endif

        @php
            // Solo planes visibles, en el orden definido en el panel de administración
            $planesVisibles = $plans->where('activo', true)->sortBy([['orden', 'asc'], ['price', 'asc']]);
        @endphp

        <div class="grid sm:grid-cols-2 gap-5">
            @foreach ($planesVisibles as $plan)
                <div class="card {{ $plan->destacado ? 'ring-2 ring-teal' : '' }} relative">
                    @if ($plan->destacado)
                        <span class="badge badge-teal absolute -top-3 left-6">⭐ Recomendado</span>
                    @endif
                    <p class="font-heading text-lg text-gray-800">{{ $plan->name }}</p>
                    <p class="text-4xl font-heading text-teal mt-2">S/ {{ number_format($plan->price, 0) }}<span class="text-sm text-gray-400 font-sans font-normal"> {{ $plan->periodoTexto() }}</span></p>
                    @if ($ahorro = $plan->ahorro($planesVisibles))
                        <span class="badge badge-verde mt-2 inline-block">Ahorras S/ {{ number_format($ahorro, 0) }}</span>
                    @endif
                    <p class="text-xs text-gray-400 mt-1">{{ $plan->notaPago() }}</p>
                    <ul class="mt-5 space-y-2 text-sm text-gray-600">
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-verde shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Hasta {{ $plan->max_tesis }} tesis</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-verde shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>{{ $plan->max_revisiones }} revisiones APA / mes</li>
                        @if ($plan->max_ia > 0)
                            <li class="flex items-center gap-2"><svg class="w-4 h-4 text-verde shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>{{ $plan->max_ia }} usos de IA / mes</li>
                        @endif
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-verde shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Control de versiones</li>
                    </ul>
                    <a href="{{ route('subscription.checkout', $plan) }}" class="btn-primary w-full justify-center mt-6">
                        Elegir plan
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
