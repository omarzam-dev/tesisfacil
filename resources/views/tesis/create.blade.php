<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading text-2xl text-gray-800">Nueva tesis</h2>
    </x-slot>

    <div class="py-8 max-w-2xl mx-auto px-4">
        <div class="card">
            @if ($errors->any())
                <div class="alert-error mb-5">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('tesis.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Título de la tesis</label>
                    <input type="text" name="titulo" required value="{{ old('titulo') }}"
                        class="input-field" placeholder="Ej: Impacto de la gestión educativa en el rendimiento académico...">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Línea de investigación (opcional)</label>
                    <input type="text" name="linea_investigacion" value="{{ old('linea_investigacion') }}"
                        class="input-field" placeholder="Ej: Gestión educativa">
                </div>

                <div class="alert-info flex items-start gap-2.5">
                    <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Se crearán automáticamente los 5 capítulos estándar (Planteamiento, Marco teórico, Metodología, Resultados, Discusión y conclusiones) — podrás renombrarlos o agregar más después.</span>
                </div>

                <button type="submit" class="btn-primary w-full justify-center">
                    Crear tesis
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
