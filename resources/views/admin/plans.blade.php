<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-heading text-2xl text-gray-800">Planes</h2>
    </x-slot>

    <div class="grid sm:grid-cols-2 gap-5">
        @foreach ($plans as $plan)
            <form method="POST" action="{{ route('admin.plans.update', $plan) }}" class="card space-y-4">
                @csrf @method('PUT')
                <p class="font-heading text-lg text-gray-800">{{ $plan->name }}</p>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Precio mensual (S/)</label>
                    <input type="number" step="0.01" name="price" value="{{ $plan->price }}" class="input-field">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Máx. tesis</label>
                    <input type="number" name="max_tesis" value="{{ $plan->max_tesis }}" class="input-field">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Máx. revisiones / mes</label>
                    <input type="number" name="max_revisiones" value="{{ $plan->max_revisiones }}" class="input-field">
                </div>

                <button class="btn-primary btn-sm">Guardar cambios</button>
            </form>
        @endforeach
    </div>
</x-admin-layout>
