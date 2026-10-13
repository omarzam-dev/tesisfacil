<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading text-2xl text-gray-800">Corrector APA / ISO</h2>
        <p class="text-sm text-gray-500 mt-1">Sube tu documento y revisa formato, citas y referencias según la norma de tu universidad</p>
    </x-slot>

    <style>[x-cloak] { display: none !important; }</style>

    <div class="py-8 max-w-2xl mx-auto px-4">
        <div class="card">
            @if ($errors->any())
                <div class="rounded-lg border border-rosa/30 bg-rosa/10 text-rosa px-4 py-3 text-sm mb-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('apa.store') }}" enctype="multipart/form-data" class="space-y-5"
                  x-data="{ norma: '{{ old('edicion_apa', 'APA7') }}', perfil: '{{ old('perfil_formato_id', '') }}' }">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Documento (.docx)</label>
                    <input type="file" name="documento" accept=".docx" required
                        class="block w-full text-sm border border-borde rounded-lg cursor-pointer p-2.5
                               file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-celeste-suave file:text-celeste file:text-xs file:font-semibold hover:file:bg-celeste/20">
                    <p class="text-xs text-gray-400 mt-1.5">Máximo 10 MB. Solo formato .docx (Word).</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Norma de citas y referencias</label>
                    <select name="edicion_apa" required class="input-field" x-model="norma">
                        @foreach ($normas as $codigo => $n)
                            <option value="{{ $codigo }}">{{ $n['nombre'] }}</option>
                        @endforeach
                    </select>
                    @foreach ($normas as $codigo => $n)
                        @if (! empty($n['descripcion']))
                            <p class="text-xs text-gray-500 mt-1.5" x-show="norma === '{{ $codigo }}'" x-cloak>{{ $n['descripcion'] }}</p>
                        @endif
                    @endforeach
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Formato de página (márgenes, fuente e interlineado)</label>
                    <select name="perfil_formato_id" class="input-field" x-model="perfil">
                        <option value="" x-text="norma.startsWith('ISO') ? 'No revisar el formato de página' : 'El de la norma APA'"></option>
                        @foreach ($perfiles as $p)
                            <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                    @foreach ($perfiles as $p)
                        <p class="text-xs text-gray-500 mt-1.5" x-show="perfil === '{{ $p->id }}'" x-cloak>{{ $p->resumen() }}</p>
                    @endforeach
                    <div x-show="norma.startsWith('ISO') && perfil === ''" x-cloak
                         class="mt-2 rounded-lg border border-naranja/20 bg-naranja-suave px-3 py-2 text-xs text-gray-700">
                        ISO 690 no define márgenes ni fuente: eso lo pone la guía de cada universidad.
                        @if ($perfiles->isNotEmpty())
                            Elige el perfil de tu universidad para que también se revise el formato.
                        @else
                            Por ahora solo se revisarán las citas y las referencias.
                        @endif
                    </div>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.perfiles.index') }}" class="inline-block mt-2 text-xs text-lavanda font-medium hover:underline">Administrar perfiles de formato</a>
                    @endif
                </div>

                <button type="submit" class="btn-primary w-full justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    Revisar documento
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
