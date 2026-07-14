<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading text-xl text-gray-800">{{ $tesis->titulo }}</h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4">

        @if (session('status'))
            <div class="mb-4 p-3 bg-verde/10 text-verde rounded-lg text-sm">{{ session('status') }}</div>
        @endif

        <div class="bg-white rounded-xl shadow p-5 mb-6">
            <div class="w-full bg-gray-100 rounded-full h-2">
                <div class="bg-verde h-2 rounded-full" style="width: {{ $tesis->progreso() }}%"></div>
            </div>
            <p class="text-xs text-gray-500 mt-1">{{ $tesis->progreso() }}% de capítulos aprobados</p>
        </div>

        <div class="space-y-4">
            @foreach ($tesis->capitulos as $capitulo)
                @php
                    $badge = match($capitulo->estado) {
                        'aprobado' => ['label' => 'Aprobado', 'color' => 'bg-verde/10 text-verde'],
                        'en_revision' => ['label' => 'En revisión', 'color' => 'bg-naranja/10 text-naranja'],
                        'en_progreso' => ['label' => 'En progreso', 'color' => 'bg-teal/10 text-teal'],
                        default => ['label' => 'Pendiente', 'color' => 'bg-gray-100 text-gray-500'],
                    };
                    $ultima = $capitulo->versiones->first();
                @endphp
                <div class="bg-white rounded-xl shadow p-5">
                    <div class="flex items-center justify-between">
                        <p class="font-medium text-gray-800">{{ $capitulo->nombre }}</p>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $badge['color'] }}">{{ $badge['label'] }}</span>
                            <form method="POST" action="{{ route('capitulos.destroy', $capitulo) }}" onsubmit="return confirm('¿Eliminar este capítulo y todas sus versiones?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-rosa hover:underline">Eliminar</button>
                            </form>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('capitulos.estado', $capitulo) }}" class="mt-2">
                        @csrf
                        <select name="estado" onchange="this.form.submit()"
                            class="text-xs border-gray-300 rounded-lg focus:ring-2 focus:ring-teal">
                            <option value="pendiente" @selected($capitulo->estado === 'pendiente')>Pendiente</option>
                            <option value="en_progreso" @selected($capitulo->estado === 'en_progreso')>En progreso</option>
                            <option value="en_revision" @selected($capitulo->estado === 'en_revision')>En revisión</option>
                            <option value="aprobado" @selected($capitulo->estado === 'aprobado')>Aprobado</option>
                        </select>
                    </form>

                    @if ($ultima)
                        <p class="text-sm text-gray-500 mt-3">
                            Última versión: <span class="font-medium">v{{ $ultima->numero_version }}</span> — {{ $ultima->nombre_archivo }} ({{ $ultima->created_at->diffForHumans() }})
                            <a href="{{ route('capitulos.version.download', [$capitulo, $ultima]) }}" class="text-teal hover:underline ml-2">Descargar</a>
                        </p>
                    @endif

                    @if ($capitulo->versiones->count() > 1)
                        <details class="mt-2">
                            <summary class="text-xs text-gray-400 cursor-pointer">Ver historial de versiones ({{ $capitulo->versiones->count() }})</summary>
                            <ul class="mt-2 space-y-1">
                                @foreach ($capitulo->versiones as $version)
                                    <li class="text-xs text-gray-500 flex justify-between">
                                        <span>v{{ $version->numero_version }} — {{ $version->nombre_archivo }} ({{ $version->created_at->diffForHumans() }})</span>
                                        <a href="{{ route('capitulos.version.download', [$capitulo, $version]) }}" class="text-teal hover:underline">Descargar</a>
                                    </li>
                                @endforeach
                            </ul>
                        </details>
                    @endif

                    <form method="POST" action="{{ route('capitulos.version.store', $capitulo) }}" enctype="multipart/form-data" class="mt-4 flex items-center gap-2">
                        @csrf
                        <input type="file" name="documento" accept=".docx,.pdf" required
                            class="text-xs border border-gray-300 rounded-lg flex-1 p-1.5">
                        <button type="submit" class="bg-teal text-white text-xs font-medium px-3 py-1.5 rounded-lg hover:opacity-90">
                            Subir versión
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        <form method="POST" action="{{ route('capitulos.store', $tesis) }}" class="mt-6 bg-white rounded-xl shadow p-5 flex items-center gap-2">
            @csrf
            <input type="text" name="nombre" placeholder="Nombre del nuevo capítulo" required
                class="flex-1 text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-teal">
            <button type="submit" class="bg-gray-100 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-200">
                + Agregar capítulo
            </button>
        </form>

        <a href="{{ route('tesis.index') }}" class="inline-block mt-6 text-teal hover:underline text-sm">
            &larr; Volver a mis tesis
        </a>
    </div>
</x-app-layout>
