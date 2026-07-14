<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading text-xl text-gray-800">Corrector APA — Nueva revisión</h2>
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

            <form method="POST" action="{{ route('apa.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Documento (.docx)</label>
                    <input type="file" name="documento" accept=".docx" required
                        class="block w-full text-sm border border-gray-300 rounded-lg cursor-pointer focus:outline-none focus:ring-2 focus:ring-teal p-2">
                    <p class="text-xs text-gray-500 mt-1">Máximo 10MB. Solo formato .docx (Word).</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Edición APA</label>
                    <select name="edicion_apa" required
                        class="block w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-teal">
                        <option value="APA7">APA 7ma edición</option>
                        <option value="APA6">APA 6ta edición</option>
                    </select>
                </div>

                <button type="submit"
                    class="w-full bg-teal text-white font-medium py-2.5 rounded-lg hover:opacity-90 transition">
                    Revisar documento
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
