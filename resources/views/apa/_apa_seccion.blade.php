@php
    $colorResumen = $resultado['resumen']['aprobado'] ? 'text-verde' : 'text-rosa';
@endphp

<div class="card-compact mb-3">
    <div class="flex items-center justify-between">
        <p class="font-heading text-gray-800">{{ $titulo }}</p>
        <div class="text-right text-xs">
            <span class="font-semibold {{ $resultado['resumen']['errores'] > 0 ? 'text-rosa' : 'text-gray-400' }}">{{ $resultado['resumen']['errores'] }} errores</span>
            <span class="text-gray-300 mx-1">·</span>
            <span class="font-semibold {{ $resultado['resumen']['advertencias'] > 0 ? 'text-naranja' : 'text-gray-400' }}">{{ $resultado['resumen']['advertencias'] }} advertencias</span>
        </div>
    </div>
</div>

<div class="space-y-2.5 mb-8">
    @forelse ($resultado['checks'] as $check)
        @php
            $estilo = match($check['nivel']) {
                'ok' => ['border' => 'border-verde', 'bg' => 'bg-verde/5', 'icon' => 'text-verde', 'path' => 'M5 13l4 4L19 7'],
                'advertencia' => ['border' => 'border-naranja', 'bg' => 'bg-naranja/5', 'icon' => 'text-naranja', 'path' => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z'],
                default => ['border' => 'border-rosa', 'bg' => 'bg-rosa/5', 'icon' => 'text-rosa', 'path' => 'M6 18L18 6M6 6l12 12'],
            };
            $etiqueta = $check['nombre'] ?? $check['cita'] ?? '';
        @endphp
        <div class="border-l-4 {{ $estilo['border'] }} {{ $estilo['bg'] }} rounded-lg p-4 flex items-start gap-3">
            <svg class="w-4 h-4 {{ $estilo['icon'] }} shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $estilo['path'] }}" />
            </svg>
            <div>
                <p class="font-semibold text-gray-800 text-sm">{{ $etiqueta }}</p>
                <p class="text-sm text-gray-600 mt-0.5">{{ $check['mensaje'] }}</p>
            </div>
        </div>
    @empty
        <p class="text-sm text-gray-400 px-1">Sin hallazgos en esta sección.</p>
    @endforelse
</div>
