@php
    $colorResumen = $resultado['resumen']['aprobado'] ? 'text-verde' : 'text-rosa';
@endphp

<div class="bg-white rounded-xl shadow p-6 mb-3">
    <div class="flex items-center justify-between">
        <p class="font-heading text-lg text-gray-800">{{ $titulo }}</p>
        <div class="text-right text-sm text-gray-500">
            <p>{{ $resultado['resumen']['errores'] }} errores</p>
            <p>{{ $resultado['resumen']['advertencias'] }} advertencias</p>
        </div>
    </div>
</div>

<div class="space-y-3 mb-8">
    @forelse ($resultado['checks'] as $check)
        @php
            $color = match($check['nivel']) {
                'ok' => 'border-verde bg-verde/5',
                'advertencia' => 'border-naranja bg-naranja/5',
                default => 'border-rosa bg-rosa/5',
            };
            $etiqueta = $check['nombre'] ?? $check['cita'] ?? '';
        @endphp
        <div class="border-l-4 {{ $color }} rounded-lg p-4">
            <p class="font-medium text-gray-800">{{ $etiqueta }}</p>
            <p class="text-sm text-gray-600 mt-0.5">{{ $check['mensaje'] }}</p>
        </div>
    @empty
        <p class="text-sm text-gray-500">Sin hallazgos en esta sección.</p>
    @endforelse
</div>
