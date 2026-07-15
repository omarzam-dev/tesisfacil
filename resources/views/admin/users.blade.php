<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-heading text-2xl text-gray-800">Usuarios</h2>
    </x-slot>

    <div class="card !p-0 overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                <tr>
                    <th class="px-5 py-3 font-semibold">Nombre</th>
                    <th class="px-5 py-3 font-semibold">Email</th>
                    <th class="px-5 py-3 font-semibold">Rol</th>
                    <th class="px-5 py-3 font-semibold">Estado</th>
                    <th class="px-5 py-3 font-semibold">Tesis</th>
                    <th class="px-5 py-3 font-semibold">Registrado</th>
                    <th class="px-5 py-3 font-semibold"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($users as $u)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3.5 font-medium text-gray-800">{{ $u->name }}</td>
                        <td class="px-5 py-3.5 text-gray-500">{{ $u->email }}</td>
                        <td class="px-5 py-3.5">
                            @if ($u->isAdmin())
                                <span class="badge badge-teal">Admin</span>
                            @else
                                <span class="text-xs text-gray-400">Usuario</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">{{ ucfirst($u->subscription_status) }}</td>
                        <td class="px-5 py-3.5">{{ $u->tesis_count }}</td>
                        <td class="px-5 py-3.5 text-gray-500">{{ $u->created_at->format('d/m/Y') }}</td>
                        <td class="px-5 py-3.5">
                            @if ($u->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.toggle-admin', $u) }}"
                                    onsubmit="return confirm('¿{{ $u->isAdmin() ? 'Quitar admin a' : 'Hacer admin a' }} {{ $u->name }}?')">
                                    @csrf @method('PATCH')
                                    <button class="text-xs font-semibold hover:underline {{ $u->isAdmin() ? 'text-rosa' : 'text-teal' }}">
                                        {{ $u->isAdmin() ? 'Quitar admin' : 'Hacer admin' }}
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-300">(tú)</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-admin-layout>
