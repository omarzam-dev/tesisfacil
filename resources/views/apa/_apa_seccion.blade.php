{{-- Resultado de una parte de la revisión (estructura, citas o referencias) --}}
@php
    $checks = collect($resultado['checks'] ?? []);
    $problemas = $checks->filter(fn ($c) => ($c['nivel'] ?? 'ok') !== 'ok')
        ->sortBy(fn ($c) => $c['nivel'] === 'error' ? 0 : 1)
        ->values();
    $correctos = $checks->filter(fn ($c) => ($c['nivel'] ?? 'ok') === 'ok')->values();
    $errores = $resultado['resumen']['errores'] ?? 0;
    $advertencias = $resultado['resumen']['advertencias'] ?? 0;
@endphp

<div class="card mb-6 space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-2">
        <p class="font-semibold text-gray-800">{{ $titulo }}</p>
        <div class="flex flex-wrap gap-1.5">
            @if ($errores)
                <span class="badge badge-rosa">{{ $errores }} {{ $errores === 1 ? 'error' : 'errores' }}</span>
            @endif
            @if ($advertencias)
                <span class="badge badge-naranja">{{ $advertencias }} {{ $advertencias === 1 ? 'advertencia' : 'advertencias' }}</span>
            @endif
            @if (! $errores && ! $advertencias)
                <span class="badge badge-verde">Todo correcto</span>
            @endif
        </div>
    </div>

    @if ($problemas->isNotEmpty())
        <ul class="space-y-2">
            @foreach ($problemas as $c)
                @php $esError = $c['nivel'] === 'error'; @endphp
                <li class="flex gap-3 rounded-xl border p-3 text-sm {{ $esError ? 'border-rosa/20 bg-rosa-suave/60' : 'border-naranja/20 bg-naranja-suave/60' }}">
                    <span class="mt-0.5 w-6 h-6 rounded-full flex items-center justify-center shrink-0 {{ $esError ? 'bg-rosa text-white' : 'bg-naranja text-white' }}">
                        @if ($esError)
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        @else
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v5m0 3h.01" /></svg>
                        @endif
                    </span>
                    <div class="min-w-0 text-gray-700">
                        @if (! empty($c['nombre']))
                            <p class="font-semibold text-gray-800">{{ $c['nombre'] }}</p>
                        @endif
                        <p class="break-words">{{ $c['mensaje'] }}</p>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif

    @if ($correctos->isNotEmpty())
        <details class="group" @if ($problemas->isEmpty()) open @endif>
            <summary class="cursor-pointer select-none text-sm font-medium text-verde hover:underline">
                {{ $correctos->count() }} {{ $correctos->count() === 1 ? 'punto correcto' : 'puntos correctos' }}
            </summary>
            <ul class="mt-3 space-y-2">
                @foreach ($correctos as $c)
                    <li class="flex gap-3 text-sm text-gray-600">
                        <span class="mt-0.5 w-5 h-5 rounded-full bg-verde-suave text-verde flex items-center justify-center shrink-0">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        </span>
                        <p class="min-w-0 break-words">
                            @if (! empty($c['nombre']))
                                <span class="font-semibold text-gray-700">{{ $c['nombre'] }}:</span>
                            @endif
                            {{ $c['mensaje'] }}
                        </p>
                    </li>
                @endforeach
            </ul>
        </details>
    @endif
</div>
