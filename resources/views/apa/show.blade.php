<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading text-xl text-gray-800">Resultado — {{ $revision->nombre_archivo }}</h2>
    </x-slot>

    <div class="py-8 max-w-2xl mx-auto px-4">

        @if ($revision->estado === 'procesando')
            <div class="bg-white rounded-xl shadow p-6 text-center text-gray-500">
                Procesando documento...
            </div>
        @elseif ($revision->estado === 'error')
            <div class="bg-white rounded-xl shadow p-6">
                <p class="text-rosa font-medium">No se pudo procesar el documento.</p>
                <p class="text-sm text-gray-500 mt-1">{{ $revision->error_mensaje }}</p>
            </div>
        @else
            @php $r = $revision->resultado_estructura; @endphp

            <div class="bg-white rounded-xl shadow p-6 mb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Edición: {{ $r['edicion'] }}</p>
                        <p class="text-2xl font-heading {{ $r['resumen']['aprobado'] ? 'text-verde' : 'text-rosa' }}">
                            {{ $r['resumen']['aprobado'] ? 'Estructura correcta' : 'Se encontraron errores' }}
                        </p>
                    </div>
                    <div class="text-right text-sm text-gray-500">
                        <p>{{ $r['resumen']['errores'] }} errores</p>
                        <p>{{ $r['resumen']['advertencias'] }} advertencias</p>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                @foreach ($r['checks'] as $check)
                    @php
                        $color = match($check['nivel']) {
                            'ok' => 'border-verde bg-verde/5',
                            'advertencia' => 'border-naranja bg-naranja/5',
                            default => 'border-rosa bg-rosa/5',
                        };
                    @endphp
                    <div class="border-l-4 {{ $color }} rounded-lg p-4">
                        <p class="font-medium text-gray-800">{{ $check['nombre'] }}</p>
                        <p class="text-sm text-gray-600 mt-0.5">{{ $check['mensaje'] }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        <a href="{{ route('apa.create') }}" class="inline-block mt-6 text-teal hover:underline text-sm">
            &larr; Revisar otro documento
        </a>
    </div>
</x-app-layout>
