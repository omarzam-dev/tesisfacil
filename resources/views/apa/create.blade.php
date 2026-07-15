<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading text-2xl text-gray-800">Corrector APA</h2>
        <p class="text-sm text-gray-500 mt-1">Sube tu documento y revisa estructura, citas y referencias</p>
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

            <form method="POST" action="{{ route('apa.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Documento (.docx)</label>
                    <input type="file" name="documento" accept=".docx" required
                        class="block w-full text-sm border border-gray-300 rounded-lg cursor-pointer p-2.5
                               file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-teal/10 file:text-teal file:text-xs file:font-semibold hover:file:bg-teal/20">
                    <p class="text-xs text-gray-400 mt-1.5">Máximo 10MB. Solo formato .docx (Word).</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Edición APA</label>
                    <select name="edicion_apa" required class="input-field">
                        <option value="APA7">APA 7ma edición</option>
                        <option value="APA6">APA 6ta edición</option>
                    </select>
                </div>

                <button type="submit" class="btn-primary w-full justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    Revisar documento
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
