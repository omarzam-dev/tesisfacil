<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading text-2xl text-gray-800">Revisar matriz de consistencia</h2>
        <p class="text-sm text-gray-500 mt-1">Copia cada parte de tu matriz. En los campos de "específicos", escribe uno por línea y en el mismo orden.</p>
    </x-slot>

    @php
        $v = fn ($campo) => old($campo, $previa?->{$campo} ?? '');
    @endphp

    <div class="py-8 px-4 sm:px-6 max-w-3xl">
        @if ($errors->any())
            <div class="rounded-lg border border-rosa/30 bg-rosa/10 text-rosa px-4 py-3 text-sm mb-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('matriz.store') }}" class="card space-y-5"
              onsubmit="const b=this.querySelector('[type=submit]'); b.disabled=true; b.textContent='Revisando… puede tardar unos segundos';">
            @csrf

            <div class="grid sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Título de la tesis (opcional)</label>
                    <input type="text" name="titulo" value="{{ $v('titulo') }}" maxlength="200" class="input-field">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Enfoque</label>
                    <select name="enfoque" class="input-field">
                        @foreach (\App\Models\MatrizConsistencia::ENFOQUES as $valor => $texto)
                            <option value="{{ $valor }}" @selected($v('enfoque') === $valor)>{{ $texto }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Problema general</label>
                    <textarea name="problema_general" rows="3" maxlength="1500" required class="input-field">{{ $v('problema_general') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Objetivo general</label>
                    <textarea name="objetivo_general" rows="3" maxlength="1500" required class="input-field">{{ $v('objetivo_general') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Problemas específicos (uno por línea)</label>
                    <textarea name="problemas_especificos" rows="5" maxlength="4000" required class="input-field">{{ $v('problemas_especificos') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Objetivos específicos (uno por línea)</label>
                    <textarea name="objetivos_especificos" rows="5" maxlength="4000" required class="input-field">{{ $v('objetivos_especificos') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Hipótesis general</label>
                    <textarea name="hipotesis_general" rows="3" maxlength="1500" class="input-field">{{ $v('hipotesis_general') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Hipótesis específicas (una por línea)</label>
                    <textarea name="hipotesis_especificas" rows="3" maxlength="4000" class="input-field">{{ $v('hipotesis_especificas') }}</textarea>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Variables o categorías (con sus dimensiones, si las tienes)</label>
                <textarea name="variables" rows="3" maxlength="2000" class="input-field">{{ $v('variables') }}</textarea>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                <p class="text-xs text-gray-500">
                    Te quedan {{ max(0, $user->limiteIa() - $user->iaUsadaEsteMes()) }} usos de IA este mes. Esta revisión usa 1.
                </p>
                <button type="submit" class="btn-primary">Revisar matriz</button>
            </div>
        </form>

        <p class="text-xs text-gray-400 mt-4">
            La revisión es orientativa y no reemplaza a tu asesor. La IA no redacta tu tesis: te señala qué ajustar para que tú lo corrijas.
        </p>
    </div>
</x-app-layout>
