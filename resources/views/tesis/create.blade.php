<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading text-xl text-gray-800">Nueva tesis</h2>
    </x-slot>

    <div class="py-8 max-w-2xl mx-auto px-4">
        <div class="bg-white rounded-xl shadow p-6">
            @if ($errors->any())
                <div class="mb-4 p-3 bg-rosa/10 text-rosa rounded-lg text-sm">
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título de la tesis</label>
                    <input type="text" name="titulo" required value="{{ old('titulo') }}"
                        class="block w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-teal">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Línea de investigación (opcional)</label>
                    <input type="text" name="linea_investigacion" value="{{ old('linea_investigacion') }}"
                        class="block w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-teal">
                </div>

                <p class="text-xs text-gray-500">
                    Se crearán automáticamente los 5 capítulos estándar (Planteamiento, Marco teórico, Metodología, Resultados, Discusión y conclusiones) — podrás editarlos después.
                </p>

                <button type="submit"
                    class="w-full bg-teal text-white font-medium py-2.5 rounded-lg hover:opacity-90 transition">
                    Crear tesis
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
