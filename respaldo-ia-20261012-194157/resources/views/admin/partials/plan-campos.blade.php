{{-- Campos de un plan, compartidos por "editar" y "nuevo plan". $plan es null al crear. --}}
@php
    $periodoActual = $plan?->duration_days ?? 30;
@endphp

<div>
    <label class="block text-xs font-semibold text-gray-500 mb-1">Nombre</label>
    <input type="text" name="name" value="{{ $plan?->name ?? '' }}" maxlength="60" required class="input-field" placeholder="Ej.: Estudiante semestral">
</div>

<div class="grid grid-cols-2 gap-3">
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">Precio (S/)</label>
        <input type="number" step="0.01" min="0" name="price" value="{{ $plan?->price ?? '' }}" required class="input-field">
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">Periodo</label>
        <select name="duration_days" class="input-field">
            @foreach (\App\Models\Plan::PERIODOS as $dias => $texto)
                <option value="{{ $dias }}" @selected((int) $periodoActual === $dias)>{{ $texto }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="grid grid-cols-2 gap-3">
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">Máx. tesis</label>
        <input type="number" min="1" name="max_tesis" value="{{ $plan?->max_tesis ?? '' }}" required class="input-field">
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">Máx. revisiones / mes</label>
        <input type="number" min="1" name="max_revisiones" value="{{ $plan?->max_revisiones ?? '' }}" required class="input-field">
    </div>
</div>

<div class="grid grid-cols-2 gap-3 items-end">
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">Orden en la web</label>
        <input type="number" min="0" max="999" name="orden" value="{{ $plan?->orden ?? 50 }}" required class="input-field">
    </div>
    <div class="space-y-2 pb-1">
        <label class="flex items-center gap-2 text-sm text-gray-700">
            <input type="checkbox" name="activo" value="1" @checked($plan?->activo ?? true)>
            Visible en la web
        </label>
        <label class="flex items-center gap-2 text-sm text-gray-700">
            <input type="checkbox" name="destacado" value="1" @checked($plan?->destacado ?? false)>
            Marcar "Recomendado"
        </label>
    </div>
</div>
