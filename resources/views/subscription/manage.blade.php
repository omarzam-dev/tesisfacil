<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading text-2xl text-gray-800">Mi suscripción</h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 max-w-2xl">
        @if (session('success'))
            <div class="alert-success mb-5">{{ session('success') }}</div>
        @endif

        <div class="card mb-6">
            <p class="text-sm text-gray-500">Estado actual</p>
            <p class="font-heading text-xl text-gray-800 mt-1">
                @if ($user->onTrial())
                    Prueba gratuita — vence {{ $user->trial_ends_at->format('d/m/Y') }}
                @elseif ($activeSubscription)
                    Plan {{ $activeSubscription->plan->name }} — vence {{ $activeSubscription->ends_at->format('d/m/Y') }}
                @else
                    Sin plan activo
                @endif
            </p>

            <div class="mt-5 grid grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 font-medium">Tesis usadas</p>
                    <p class="font-heading text-2xl text-gray-800 mt-1">{{ $user->tesisUsadas() }}<span class="text-sm text-gray-400 font-sans"> / {{ $user->limiteTesis() }}</span></p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 font-medium">Revisiones este mes</p>
                    <p class="font-heading text-2xl text-gray-800 mt-1">{{ $user->revisionesUsadasEsteMes() }}<span class="text-sm text-gray-400 font-sans"> / {{ $user->limiteRevisiones() }}</span></p>
                </div>
            </div>

            @if ($activeSubscription)
                <form method="POST" action="{{ route('subscription.cancel') }}" onsubmit="return confirm('¿Cancelar tu suscripción?')" class="mt-5">
                    @csrf
                    <button type="submit" class="btn-ghost-danger">Cancelar suscripción</button>
                </form>
            @endif
        </div>

        <p class="section-heading">Historial de pagos</p>
        <div class="card !p-0 divide-y divide-gray-100 overflow-hidden">
            @forelse ($paymentProofs as $proof)
                @php
                    $badge = match($proof->status) {
                        'approved' => 'badge-verde',
                        'rejected' => 'badge-rosa',
                        default => 'badge-naranja',
                    };
                @endphp
                <div class="p-4 flex justify-between items-center text-sm">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $proof->plan->name }} — {{ strtoupper($proof->method) }}</p>
                        <p class="text-gray-500 text-xs mt-0.5">{{ $proof->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <span class="badge {{ $badge }}">{{ ucfirst($proof->status) }}</span>
                </div>
            @empty
                <p class="p-6 text-sm text-gray-500 text-center">Sin pagos registrados todavía.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
