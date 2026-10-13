@php
    $v = fn ($campo, $defecto) => old($campo, $perfil?->{$campo} ?? $defecto);
    $num = fn ($x) => rtrim(rtrim(number_format((float) $x, 2, '.', ''), '0'), '.');
@endphp

<div>
    <label class="block text-xs font-semibold text-gray-500 mb-1">Nombre (universidad o guía)</label>
    <input type="text" name="nombre" value="{{ $v('nombre', '') }}" maxlength="120" required class="input-field" placeholder="Ej.: UNPRG - Escuela de Posgrado">
</div>

<div>
    <p class="text-xs font-semibold text-gray-500 mb-1">Márgenes (cm)</p>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
        @foreach (['margen_superior' => ['Superior', 2.5], 'margen_inferior' => ['Inferior', 2.5], 'margen_izquierdo' => ['Izquierdo', 3], 'margen_derecho' => ['Derecho', 2.5]] as $campo => [$texto, $defecto])
            <label class="block">
                <span class="block text-[11px] text-gray-400 mb-0.5">{{ $texto }}</span>
                <input type="number" name="{{ $campo }}" value="{{ $num($v($campo, $defecto)) }}" step="0.01" min="0.5" max="6" required class="input-field">
            </label>
        @endforeach
    </div>
</div>

<div class="grid grid-cols-2 gap-3">
    <label class="block">
        <span class="block text-xs font-semibold text-gray-500 mb-1">Interlineado</span>
        <select name="interlineado" class="input-field">
            @foreach (['1' => 'Sencillo (1,0)', '1.15' => '1,15', '1.5' => '1,5 líneas', '2' => 'Doble (2,0)'] as $valor => $texto)
                <option value="{{ $valor }}" @selected(abs((float) $v('interlineado', 1.5) - (float) $valor) < 0.01)>{{ $texto }}</option>
            @endforeach
        </select>
    </label>
    <label class="block">
        <span class="block text-xs font-semibold text-gray-500 mb-1">Tamaño de fuente (pt)</span>
        <input type="number" name="tamano_fuente" value="{{ $num($v('tamano_fuente', 12)) }}" step="0.5" min="8" max="16" required class="input-field">
    </label>
</div>

<div>
    <label class="block text-xs font-semibold text-gray-500 mb-1">Fuentes permitidas (separadas por coma)</label>
    <input type="text" name="fuentes" value="{{ $v('fuentes', 'Arial, Times New Roman') }}" maxlength="255" required class="input-field">
</div>

<label class="flex items-center gap-2 text-sm text-gray-700">
    <input type="checkbox" name="activo" value="1" class="rounded border-borde text-lavanda focus:ring-lavanda" @checked(old('activo', $perfil?->activo ?? true))>
    Visible para los tesistas
</label>
