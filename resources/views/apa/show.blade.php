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
        @elseif (! $revision->resultado_citas || ! $revision->resultado_referencias)
            <div class="bg-white rounded-xl shadow p-6 mb-4">
                <p class="text-naranja font-medium">Esta revisión es de una versión anterior del corrector.</p>
                <p class="text-sm text-gray-500 mt-1">No tiene datos de citas ni referencias. Sube el documento de nuevo para obtener el análisis completo.</p>
            </div>

            <h3 class="font-heading text-teal text-sm uppercase tracking-wide mb-2">Estructura del documento</h3>
            @include('apa._apa_seccion', ['titulo' => 'Márgenes, interlineado, fuente y encabezado', 'resultado' => $revision->resultado_estructura])
        @else
            <h3 class="font-heading text-teal text-sm uppercase tracking-wide mb-2">Estructura del documento</h3>
            @include('apa._apa_seccion', ['titulo' => 'Márgenes, interlineado, fuente y encabezado', 'resultado' => $revision->resultado_estructura])

            <h3 class="font-heading text-teal text-sm uppercase tracking-wide mb-2">Citas en el texto</h3>
            @include('apa._apa_seccion', ['titulo' => $revision->resultado_citas['total_citas_detectadas'] . ' citas detectadas', 'resultado' => $revision->resultado_citas])

            <h3 class="font-heading text-teal text-sm uppercase tracking-wide mb-2">Lista de referencias</h3>
            @include('apa._apa_seccion', ['titulo' => $revision->resultado_referencias['total_referencias_detectadas'] . ' referencias detectadas', 'resultado' => $revision->resultado_referencias])
        @endif

        <a href="{{ route('apa.create') }}" class="inline-block mt-2 text-teal hover:underline text-sm">
            &larr; Revisar otro documento
        </a>
    </div>
</x-app-layout>
