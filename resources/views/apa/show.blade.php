<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading text-2xl text-gray-800">Resultado</h2>
        <p class="text-sm text-gray-500 mt-1">{{ $revision->nombre_archivo }}</p>
    </x-slot>

    <div class="py-8 max-w-2xl mx-auto px-4">

        @if ($revision->estado === 'procesando')
            <div class="card text-center py-14 text-gray-500">
                <svg class="w-8 h-8 mx-auto mb-3 animate-spin text-teal" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Procesando documento...
            </div>
        @elseif ($revision->estado === 'error')
            <div class="card">
                <p class="text-rosa font-semibold">No se pudo procesar el documento.</p>
                <p class="text-sm text-gray-500 mt-1">{{ $revision->error_mensaje }}</p>
            </div>
        @elseif (! $revision->resultado_citas || ! $revision->resultado_referencias)
            <div class="alert-warning mb-5">
                <p class="font-semibold">Esta revisión es de una versión anterior del corrector.</p>
                <p class="mt-1">No tiene datos de citas ni referencias. Sube el documento de nuevo para obtener el análisis completo.</p>
            </div>

            <p class="section-heading">Estructura del documento</p>
            @include('apa._apa_seccion', ['titulo' => 'Márgenes, interlineado, fuente y encabezado', 'resultado' => $revision->resultado_estructura])
        @else
            <p class="section-heading">Estructura del documento</p>
            @include('apa._apa_seccion', ['titulo' => 'Márgenes, interlineado, fuente y encabezado', 'resultado' => $revision->resultado_estructura])

            <p class="section-heading">Citas en el texto</p>
            @include('apa._apa_seccion', ['titulo' => $revision->resultado_citas['total_citas_detectadas'] . ' citas detectadas', 'resultado' => $revision->resultado_citas])

            <p class="section-heading">Lista de referencias</p>
            @include('apa._apa_seccion', ['titulo' => $revision->resultado_referencias['total_referencias_detectadas'] . ' referencias detectadas', 'resultado' => $revision->resultado_referencias])
        @endif

        <a href="{{ route('apa.create') }}" class="inline-flex items-center gap-1.5 mt-2 text-teal hover:underline text-sm font-medium">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Revisar otro documento
        </a>
    </div>
</x-app-layout>
