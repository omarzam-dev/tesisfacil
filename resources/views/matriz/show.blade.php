<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading text-2xl text-gray-800">{{ $matriz->titulo ?: 'Revisión de matriz' }}</h2>
        <p class="text-sm text-gray-500 mt-1">
            {{ $matriz->created_at->format('d/m/Y H:i') }} · Enfoque {{ strtolower(\App\Models\MatrizConsistencia::ENFOQUES[$matriz->enfoque] ?? $matriz->enfoque) }}
        </p>
    </x-slot>

    @php
        $r = $matriz->resultado ?? [];
        $problemas  = $matriz->listaProblemas();
        $objetivos  = $matriz->listaObjetivos();
        $hipotesis  = $matriz->listaHipotesis();
        $orden      = ['alta' => 0, 'media' => 1, 'baja' => 2];
        $obs        = collect($r['observaciones'] ?? [])->sortBy(fn ($o) => $orden[$o['severidad'] ?? 'baja'] ?? 3)->values();
        $sevBadge   = ['alta' => 'badge-rosa', 'media' => 'badge-naranja', 'baja' => 'badge-teal'];
        $sevTexto   = ['alta' => 'Importante', 'media' => 'A mejorar', 'baja' => 'Forma'];
        $marca = function ($valor) {
            return match ($valor) {
                'si', true  => '<span class="text-verde font-bold" title="Corresponde">✓</span>',
                'no', false => '<span class="text-rosa font-bold" title="No corresponde">✗</span>',
                default     => '<span class="text-gray-400" title="No aplica">—</span>',
            };
        };
    @endphp

    <div class="py-8 px-4 sm:px-6 max-w-4xl space-y-6">

        {{-- Diagnóstico general --}}
        <div class="card flex flex-col sm:flex-row gap-6 sm:items-center">
            <div class="shrink-0 text-center">
                <p class="font-heading text-5xl text-teal">{{ $matriz->puntaje ?? '—' }}</p>
                <p class="text-xs text-gray-400">de 100</p>
            </div>
            <div class="space-y-2">
                <span class="badge {{ $matriz->veredictoBadge() }}">{{ $matriz->veredictoTexto() }}</span>
                <p class="text-sm text-gray-700">{{ $r['resumen'] ?? '' }}</p>
            </div>
        </div>

        {{-- Alineación problema → objetivo → hipótesis --}}
        @if (! empty($r['alineacion']))
            <div>
                <p class="section-heading">Correspondencia por problema específico</p>
                <div class="card !p-0 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-500 text-left">
                            <tr>
                                <th class="p-3">#</th>
                                <th class="p-3">Problema específico</th>
                                <th class="p-3 text-center">Objetivo</th>
                                <th class="p-3 text-center">Hipótesis</th>
                                <th class="p-3">Comentario</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($r['alineacion'] as $fila)
                                @php $n = (int) ($fila['numero'] ?? 0); @endphp
                                <tr class="align-top">
                                    <td class="p-3 text-gray-500">{{ $n }}</td>
                                    <td class="p-3 text-gray-700">{{ $problemas[$n - 1] ?? '' }}</td>
                                    <td class="p-3 text-center text-lg">{!! $marca($fila['objetivo_ok'] ?? null) !!}</td>
                                    <td class="p-3 text-center text-lg">{!! $marca($fila['hipotesis_ok'] ?? null) !!}</td>
                                    <td class="p-3 text-gray-600">{{ $fila['comentario'] ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="text-xs text-gray-400 mt-2">
                    Enviaste {{ count($problemas) }} problemas, {{ count($objetivos) }} objetivos y {{ count($hipotesis) }} hipótesis específicas.
                </p>
            </div>
        @endif

        {{-- Observaciones --}}
        <div>
            <p class="section-heading">Qué corregir</p>
            <div class="space-y-3">
                @forelse ($obs as $o)
                    @php $sev = $o['severidad'] ?? 'baja'; @endphp
                    <div class="card space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="badge {{ $sevBadge[$sev] ?? 'badge-teal' }}">{{ $sevTexto[$sev] ?? $sev }}</span>
                            <p class="font-semibold text-gray-800 text-sm">{{ $o['elemento'] ?? '' }}</p>
                        </div>
                        <p class="text-sm text-gray-700">{{ $o['problema'] ?? '' }}</p>
                        <p class="text-sm text-gray-600"><span class="font-semibold text-teal">Cómo corregirlo:</span> {{ $o['como_corregir'] ?? '' }}</p>
                    </div>
                @empty
                    <div class="card text-sm text-gray-600">No se encontraron observaciones.</div>
                @endforelse
            </div>
        </div>

        {{-- Fortalezas --}}
        @if (! empty($r['fortalezas']))
            <div>
                <p class="section-heading">Lo que está bien</p>
                <div class="card">
                    <ul class="list-disc pl-5 space-y-1 text-sm text-gray-700">
                        @foreach ($r['fortalezas'] as $f)
                            <li>{{ $f }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('matriz.create', ['desde' => $matriz->id]) }}" class="btn-primary">Corregir y revisar de nuevo</a>
            <a href="{{ route('matriz.index') }}" class="btn-secondary">Volver a mis revisiones</a>
        </div>

        <p class="text-xs text-gray-400">La revisión es orientativa y no reemplaza a tu asesor.</p>
    </div>
</x-app-layout>
