<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-heading text-2xl text-gray-800">Todos los pagos</h2>
    </x-slot>

    <div class="card !p-0 overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                <tr>
                    <th class="px-5 py-3 font-semibold">Usuario</th>
                    <th class="px-5 py-3 font-semibold">Plan</th>
                    <th class="px-5 py-3 font-semibold">Método</th>
                    <th class="px-5 py-3 font-semibold">Comprobante</th>
                    <th class="px-5 py-3 font-semibold">Estado</th>
                    <th class="px-5 py-3 font-semibold">Fecha</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($proofs as $proof)
                    @php
                        $badge = match($proof->status) {
                            'approved' => 'badge-verde',
                            'rejected' => 'badge-rosa',
                            default => 'badge-naranja',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3.5 font-medium text-gray-800">{{ $proof->user->name }}</td>
                        <td class="px-5 py-3.5">{{ $proof->plan->name }}</td>
                        <td class="px-5 py-3.5">{{ strtoupper($proof->method) }}</td>
                        <td class="px-5 py-3.5"><a href="{{ Storage::url($proof->proof_image) }}" target="_blank" class="btn-ghost-teal">Ver</a></td>
                        <td class="px-5 py-3.5"><span class="badge {{ $badge }}">{{ ucfirst($proof->status) }}</span></td>
                        <td class="px-5 py-3.5 text-gray-500">{{ $proof->created_at->format('d/m/Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-admin-layout>
