<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading text-2xl text-gray-800">Planes</h2>
        <p class="text-sm text-gray-500 mt-1">Elige el plan que se ajuste a tu ritmo de tesis</p>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 max-w-3xl">
        @if (session('success'))
            <div class="alert-success mb-5">{{ session('success') }}</div>
        @endif

        <div class="grid sm:grid-cols-2 gap-5">
            @foreach ($plans as $plan)
                <div class="card {{ $loop->first ? 'ring-2 ring-teal' : '' }} relative">
                    @if ($loop->first)
                        <span class="badge badge-teal absolute -top-3 left-6">⭐ Recomendado</span>
                    @endif
                    <p class="font-heading text-lg text-gray-800">{{ $plan->name }}</p>
                    <p class="text-4xl font-heading text-teal mt-2">S/ {{ number_format($plan->price, 0) }}<span class="text-sm text-gray-400 font-sans font-normal"> /mes</span></p>
                    <ul class="mt-5 space-y-2 text-sm text-gray-600">
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-verde shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Hasta {{ $plan->max_tesis }} tesis</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-verde shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>{{ $plan->max_revisiones }} revisiones APA / mes</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-verde shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Control de versiones</li>
                    </ul>
                    <a href="{{ route('subscription.checkout', $plan) }}" class="btn-primary w-full justify-center mt-6">
                        Elegir {{ $plan->name }}
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
