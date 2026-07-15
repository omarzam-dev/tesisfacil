<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-heading text-2xl text-gray-800">Pagos pendientes de aprobar</h2>
    </x-slot>

    <div class="card !p-0 divide-y divide-gray-100 overflow-hidden">
        @forelse ($pendingPayments as $proof)
            <div class="p-5 flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <a href="{{ Storage::url($proof->proof_image) }}" target="_blank" title="Ver comprobante completo">
                        <img src="{{ Storage::url($proof->proof_image) }}" alt="Comprobante" class="w-16 h-16 object-cover rounded-lg border border-gray-200 hover:opacity-80 hover:scale-105 transition-all">
                    </a>
                    <div>
                        <p class="font-semibold text-gray-800">{{ $proof->user->name }} — {{ $proof->plan->name }}</p>
                        <p class="text-sm text-gray-500 mt-0.5">{{ strtoupper($proof->method) }} · {{ $proof->transaction_ref ?: 'sin N° de operación' }} · {{ $proof->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="flex gap-2 shrink-0">
                    <form method="POST" action="{{ route('admin.payments.approve', $proof) }}">
                        @csrf
                        <button class="btn-primary btn-sm !bg-verde hover:!bg-[#268f52]">Aprobar</button>
                    </form>
                    <form method="POST" action="{{ route('admin.payments.reject', $proof) }}">
                        @csrf
                        <button class="btn-danger btn-sm">Rechazar</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="p-14 text-center text-gray-400">
                <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                No hay pagos pendientes.
            </div>
        @endforelse
    </div>
</x-admin-layout>
