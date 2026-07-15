<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-heading text-2xl text-gray-800">Cuentas de pago</h2>
    </x-slot>

    <div class="card mb-6">
        <p class="font-heading text-sm text-gray-700 mb-4">Agregar cuenta nueva</p>
        <form method="POST" action="{{ route('admin.payment-methods.store') }}" class="grid sm:grid-cols-4 gap-3">
            @csrf
            <input type="text" name="label" placeholder="Etiqueta (ej: Yape - Omar)" required class="input-field">
            <input type="text" name="type" placeholder="Tipo (Yape, BCP...)" required class="input-field">
            <input type="text" name="account_info" placeholder="Número / cuenta" required class="input-field">
            <button class="btn-primary">+ Agregar</button>
        </form>
    </div>

    <div class="card !p-0 divide-y divide-gray-100 overflow-hidden">
        @forelse ($methods as $method)
            <form method="POST" action="{{ route('admin.payment-methods.update', $method) }}" class="p-4 flex items-center gap-3">
                @csrf @method('PUT')
                <input type="text" name="label" value="{{ $method->label }}" class="input-field flex-1">
                <input type="text" name="type" value="{{ $method->type }}" class="input-field w-32">
                <input type="text" name="account_info" value="{{ $method->account_info }}" class="input-field w-48">
                <label class="flex items-center gap-1.5 text-sm text-gray-600 shrink-0">
                    <input type="checkbox" name="active" value="1" @checked($method->active) class="rounded text-teal focus:ring-teal">
                    Activa
                </label>
                <button class="btn-secondary btn-sm shrink-0">Guardar</button>
            </form>
        @empty
            <p class="p-8 text-sm text-gray-500 text-center">No hay cuentas de pago registradas.</p>
        @endforelse
    </div>

    <p class="text-xs text-gray-400 mt-3">Solo las cuentas marcadas "Activa" aparecen en el checkout para el tesista.</p>
</x-admin-layout>
