<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading text-2xl text-gray-800">{{ $simulacion->titulo }}</h2>
        <p class="text-sm text-gray-500 mt-1">
            Simulación de sustentación · Jurado {{ strtolower(\App\Models\SimulacionSustentacion::TONOS[$simulacion->tono] ?? $simulacion->tono) }}
            · {{ $simulacion->created_at->format('d/m/Y H:i') }}
        </p>
    </x-slot>

    @php
        $M = \App\Models\SimulacionSustentacion::class;
        $preguntas  = $simulacion->preguntas ?? [];
        $respuestas = $simulacion->respuestas ?? [];
        $actual     = $simulacion->indiceActual();
        $total      = $simulacion->totalPreguntas();
    @endphp

    <div class="py-8 px-4 sm:px-6 max-w-3xl space-y-6">

        {{-- Resultado final --}}
        @if ($simulacion->finalizada())
            <div class="card flex flex-col sm:flex-row gap-6 sm:items-center">
                <div class="shrink-0 text-center">
                    <p class="font-heading text-5xl {{ $M::colorNota($simulacion->nota_final) }}">{{ number_format($simulacion->nota_final, 1) }}</p>
                    <p class="text-xs text-gray-400">de 20</p>
                </div>
                <div class="space-y-2">
                    <p class="font-heading text-xl text-gray-800">{{ $M::calificacion($simulacion->nota_final) }}</p>
                    <p class="text-sm text-gray-600">Revisa abajo la evaluación de cada respuesta. Lo que más te ayudará es practicar las preguntas con nota más baja.</p>
                </div>
            </div>
        @else
            {{-- Progreso --}}
            <div>
                <div class="flex justify-between text-xs text-gray-500 mb-1">
                    <span>Pregunta {{ $actual + 1 }} de {{ $total }}</span>
                    <span>{{ count($respuestas) }} respondidas</span>
                </div>
                <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                    <div class="h-full bg-teal rounded-full" style="width: {{ $total ? round(count($respuestas) / $total * 100) : 0 }}%"></div>
                </div>
            </div>
        @endif

        {{-- Preguntas ya respondidas --}}
        @foreach ($respuestas as $i => $r)
            @php $p = $preguntas[$i] ?? []; @endphp
            <div class="card space-y-3">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold text-teal uppercase tracking-wide">Pregunta {{ $i + 1 }} · {{ $p['que_evalua'] ?? '' }}</p>
                        <p class="font-semibold text-gray-800 mt-1">{{ $p['pregunta'] ?? '' }}</p>
                    </div>
                    <div class="shrink-0 text-right">
                        <span class="font-heading text-2xl {{ $M::colorNota($r['nota']) }}">{{ $r['nota'] }}</span><span class="text-xs text-gray-400">/20</span>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-3 text-sm text-gray-700">
                    <p class="text-xs text-gray-400 mb-1">Tu respuesta</p>
                    <p class="whitespace-pre-line">{{ $r['respuesta'] }}</p>
                </div>

                <p class="text-sm text-gray-700"><span class="font-semibold">Jurado:</span> {{ $r['comentario'] }}</p>

                @if (! empty($r['que_falto']))
                    <div class="text-sm text-gray-700">
                        <p class="font-semibold">Qué faltó mencionar:</p>
                        <ul class="list-disc pl-5 mt-1 space-y-0.5">
                            @foreach ($r['que_falto'] as $f)
                                <li>{{ $f }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (! empty($r['consejo']))
                    <p class="text-sm text-gray-600"><span class="font-semibold text-teal">Consejo:</span> {{ $r['consejo'] }}</p>
                @endif
            </div>
        @endforeach

        {{-- Pregunta actual --}}
        @if (! is_null($actual))
            @php $p = $preguntas[$actual] ?? []; @endphp
            <div id="pregunta-actual" class="card ring-2 ring-teal space-y-4">
                <div>
                    <p class="text-xs font-semibold text-teal uppercase tracking-wide">Pregunta {{ $actual + 1 }} · {{ $p['que_evalua'] ?? '' }}</p>
                    <p class="font-heading text-lg text-gray-800 mt-1">{{ $p['pregunta'] ?? '' }}</p>
                </div>

                @if ($errors->any())
                    <div class="rounded-lg border border-rosa/30 bg-rosa/10 text-rosa px-4 py-3 text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('sustentacion.responder', $simulacion) }}" class="space-y-3"
                      onsubmit="const b=this.querySelector('[type=submit]'); b.disabled=true; b.textContent='El jurado está evaluando tu respuesta…';">
                    @csrf
                    <textarea name="respuesta" rows="6" maxlength="3000" required class="input-field"
                              placeholder="Responde como lo harías frente al jurado: claro, ordenado y con datos de tu tesis.">{{ old('respuesta') }}</textarea>
                    <div class="flex justify-end">
                        <button type="submit" class="btn-primary">Enviar respuesta</button>
                    </div>
                </form>
            </div>
        @endif

        <div class="flex flex-wrap gap-3">
            @if ($simulacion->finalizada())
                <a href="{{ route('sustentacion.create', ['desde' => $simulacion->id]) }}" class="btn-primary">Repetir con preguntas nuevas</a>
            @endif
            <a href="{{ route('sustentacion.index') }}" class="btn-secondary">Volver a mis simulaciones</a>
        </div>

        <p class="text-xs text-gray-400">La simulación es una práctica orientativa y no reemplaza la evaluación de tu jurado real.</p>
    </div>
</x-app-layout>
