<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading text-2xl text-gray-800">Nueva simulación de sustentación</h2>
        <p class="text-sm text-gray-500 mt-1">Resume tu tesis. Con esto el jurado prepara 5 preguntas sobre tu trabajo.</p>
    </x-slot>

    @php
        $v = fn ($campo, $defecto = '') => old($campo, $previa[$campo] ?? $defecto);
    @endphp

    <div class="py-8 px-4 sm:px-6 max-w-3xl">
        @if ($errors->any())
            <div class="rounded-lg border border-rosa/30 bg-rosa/10 text-rosa px-4 py-3 text-sm mb-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if ($matrices->isNotEmpty())
            <form method="GET" action="{{ route('sustentacion.create') }}" class="card mb-5 flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[220px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1">¿Ya revisaste tu matriz? Úsala para llenar el planteamiento</label>
                    <select name="matriz" class="input-field">
                        @foreach ($matrices as $m)
                            <option value="{{ $m->id }}">{{ $m->titulo ?: 'Matriz sin título' }} · {{ $m->created_at->format('d/m/Y') }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-secondary">Usar esta matriz</button>
            </form>
        @endif

        <form method="POST" action="{{ route('sustentacion.store') }}" class="card space-y-5"
              onsubmit="const b=this.querySelector('[type=submit]'); b.disabled=true; b.textContent='El jurado está preparando sus preguntas…';">
            @csrf

            <div class="grid sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Título de la tesis</label>
                    <input type="text" name="titulo" value="{{ $v('titulo') }}" maxlength="255" required class="input-field">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Jurado</label>
                    <select name="tono" class="input-field">
                        @foreach (\App\Models\SimulacionSustentacion::TONOS as $valor => $texto)
                            <option value="{{ $valor }}" @selected($v('tono', 'comprensivo') === $valor)>{{ $texto }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Carrera (opcional)</label>
                <input type="text" name="carrera" value="{{ $v('carrera') }}" maxlength="120" class="input-field" placeholder="Ej.: Educación, Enfermería, Administración">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Planteamiento: problema, objetivos e hipótesis</label>
                <textarea name="planteamiento" rows="7" maxlength="5000" required class="input-field">{{ $v('planteamiento') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Metodología: enfoque, tipo, diseño, población, muestra e instrumentos</label>
                <textarea name="metodologia" rows="4" maxlength="3000" required class="input-field">{{ $v('metodologia') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Resultados principales (con cifras si las tienes)</label>
                <textarea name="resultados" rows="4" maxlength="3000" required class="input-field">{{ $v('resultados') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Conclusiones (opcional)</label>
                <textarea name="conclusiones" rows="3" maxlength="2000" class="input-field">{{ $v('conclusiones') }}</textarea>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                <p class="text-xs text-gray-500">
                    Te quedan {{ max(0, $user->limiteIa() - $user->iaUsadaEsteMes()) }} usos de IA este mes. La simulación completa usa 1.
                </p>
                <button type="submit" class="btn-primary">Empezar sustentación</button>
            </div>
        </form>
    </div>
</x-app-layout>
