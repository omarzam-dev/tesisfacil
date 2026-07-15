<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-heading text-2xl text-gray-800">Suscripciones</h2>
    </x-slot>

    <div class="card !p-0 overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                <tr>
                    <th class="px-5 py-3 font-semibold">Usuario</th>
                    <th class="px-5 py-3 font-semibold">Plan</th>
                    <th class="px-5 py-3 font-semibold">Estado</th>
                    <th class="px-5 py-3 font-semibold">Vence</th>
                    <th class="px-5 py-3 font-semibold"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($subscriptions as $sub)
                    @php
                        $badge = match($sub->status) {
                            'active' => 'badge-verde',
                            'cancelled', 'expired' => 'badge-rosa',
                            default => 'badge-naranja',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3.5 font-medium text-gray-800">{{ $sub->user->name }}</td>
                        <td class="px-5 py-3.5">{{ $sub->plan->name }}</td>
                        <td class="px-5 py-3.5"><span class="badge {{ $badge }}">{{ ucfirst($sub->status) }}</span></td>
                        <td class="px-5 py-3.5 text-gray-500">{{ $sub->ends_at?->format('d/m/Y') ?? '—' }}</td>
                        <td class="px-5 py-3.5">
                            @if ($sub->status === 'active')
                                <form method="POST" action="{{ route('admin.subscriptions.cancel', $sub) }}" onsubmit="return confirm('¿Cancelar la suscripción de {{ $sub->user->name }}?')">
                                    @csrf @method('PATCH')
                                    <button class="text-xs text-naranja font-semibold hover:underline">Cancelar</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.subscriptions.destroy', $sub) }}" onsubmit="return confirm('¿Eliminar este registro permanentemente?')">
                                    @csrf @method('DELETE')
                                    <button class="btn-ghost-danger">Eliminar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-admin-layout>
