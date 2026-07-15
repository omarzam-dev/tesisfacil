<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <h2 class="font-heading text-2xl text-gray-800">{{ $tesis->titulo }}</h2>
            <a href="{{ route('tesis.descargar-completa', $tesis) }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Descargar tesis completa
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4">

        @if (session('status'))
            <div class="alert-success mb-5">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="alert-error mb-5">{{ session('error') }}</div>
        @endif
        @if (session('warning'))
            <div class="alert-warning mb-5">{{ session('warning') }}</div>
        @endif

        <div class="alert-info mb-6 flex items-start gap-2.5">
            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>
                <strong>Sobre "Descargar tesis completa":</strong> esto NO corrige el formato APA — solo une el último archivo de cada capítulo tal como lo subiste, errores incluidos. Si quieres que cumpla APA, primero revisa cada capítulo en el <a href="{{ route('apa.create') }}" class="text-teal underline font-medium">corrector APA</a>, corrige en Word lo que te marque, y sube esa versión corregida antes de descargar la tesis completa.
                <br><br>
                Además, el texto y formato se combinan bien, pero las <strong>imágenes, gráficos o tablas insertadas como imagen</strong> pueden no aparecer a partir del segundo capítulo. Revisa el resultado antes de entregarlo — cada capítulo sigue disponible para descargar por separado sin este problema.
            </span>
        </div>

        <div class="card-compact mb-6">
            <div class="w-full bg-gray-100 rounded-full h-2.5">
                <div class="bg-verde h-2.5 rounded-full transition-all" style="width: {{ $tesis->progreso() }}%"></div>
            </div>
            <p class="text-xs text-gray-500 mt-1.5 font-medium">{{ $tesis->progreso() }}% de capítulos aprobados</p>
        </div>

        <div class="space-y-4">
            @foreach ($tesis->capitulos as $capitulo)
                @php
                    $badge = match($capitulo->estado) {
                        'aprobado' => ['label' => 'Aprobado', 'class' => 'badge-verde'],
                        'en_revision' => ['label' => 'En revisión', 'class' => 'badge-naranja'],
                        'en_progreso' => ['label' => 'En progreso', 'class' => 'badge-teal'],
                        default => ['label' => 'Pendiente', 'class' => 'badge-gris'],
                    };
                    $ultima = $capitulo->versiones->first();
                @endphp
                <div class="card" x-data="{ editando: false }">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex-1" x-show="!editando">
                            <p class="font-heading text-gray-800">
                                {{ $capitulo->nombre }}
                                <button type="button" @click="editando = true" class="text-gray-300 hover:text-teal ml-1 align-middle" title="Editar nombre">
                                    <svg class="w-4 h-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                            </p>
                        </div>

                        <form method="POST" action="{{ route('capitulos.nombre', $capitulo) }}" x-show="editando" class="flex-1 flex items-center gap-2" @click.outside="editando = false">
                            @csrf @method('PATCH')
                            <input type="text" name="nombre" value="{{ $capitulo->nombre }}" required class="input-field py-1.5">
                            <button type="submit" class="btn-ghost-teal shrink-0">Guardar</button>
                        </form>

                        <div class="flex items-center gap-3 shrink-0">
                            <span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                            <form method="POST" action="{{ route('capitulos.destroy', $capitulo) }}" onsubmit="return confirm('¿Eliminar este capítulo y todas sus versiones?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-ghost-danger">Eliminar</button>
                            </form>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('capitulos.estado', $capitulo) }}" class="mt-3">
                        @csrf
                        @method('PATCH')
                        <select name="estado" onchange="this.form.submit()" class="input-field w-auto text-xs font-medium py-1.5 pr-8">
                            <option value="pendiente" @selected($capitulo->estado === 'pendiente')>Pendiente</option>
                            <option value="en_progreso" @selected($capitulo->estado === 'en_progreso')>En progreso</option>
                            <option value="en_revision" @selected($capitulo->estado === 'en_revision')>En revisión</option>
                            <option value="aprobado" @selected($capitulo->estado === 'aprobado')>Aprobado</option>
                        </select>
                    </form>

                    @if ($ultima)
                        <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between text-sm">
                            <p class="text-gray-500">
                                <span class="font-semibold text-gray-700">v{{ $ultima->numero_version }}</span> — {{ $ultima->nombre_archivo }}
                                <span class="text-gray-400">({{ $ultima->created_at->diffForHumans() }})</span>
                            </p>
                            <a href="{{ route('capitulos.version.download', [$capitulo, $ultima]) }}" class="btn-ghost-teal flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                Descargar
                            </a>
                        </div>
                    @endif

                    @if ($capitulo->versiones->count() > 1)
                        <details class="mt-2">
                            <summary class="text-xs text-gray-400 hover:text-gray-600 cursor-pointer font-medium">Ver historial de versiones ({{ $capitulo->versiones->count() }})</summary>
                            <ul class="mt-2 space-y-1.5 bg-gray-50 rounded-lg p-3">
                                @foreach ($capitulo->versiones as $version)
                                    <li class="text-xs text-gray-500 flex justify-between items-center">
                                        <span>v{{ $version->numero_version }} — {{ $version->nombre_archivo }} ({{ $version->created_at->diffForHumans() }})</span>
                                        <span class="flex items-center gap-3">
                                            <a href="{{ route('capitulos.version.download', [$capitulo, $version]) }}" class="btn-ghost-teal">Descargar</a>
                                            <form method="POST" action="{{ route('capitulos.version.destroy', [$capitulo, $version]) }}" onsubmit="return confirm('¿Eliminar la versión v{{ $version->numero_version }}? Esto no se puede deshacer.')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-ghost-danger">Eliminar</button>
                                            </form>
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </details>
                    @endif

                    <form method="POST" action="{{ route('capitulos.version.store', $capitulo) }}" enctype="multipart/form-data" class="mt-4 flex items-center gap-2">
                        @csrf
                        <input type="file" name="documento" accept=".docx" required
                            class="text-xs border border-gray-300 rounded-lg flex-1 p-2 file:mr-3 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:bg-teal/10 file:text-teal file:text-xs file:font-semibold hover:file:bg-teal/20">
                        <button type="submit" class="btn-primary btn-sm shrink-0">
                            Subir versión
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        <form method="POST" action="{{ route('capitulos.store', $tesis) }}" class="mt-6 card-compact flex items-center gap-2">
            @csrf
            <input type="text" name="nombre" placeholder="Nombre del nuevo capítulo" required class="input-field flex-1">
            <button type="submit" class="btn-secondary btn-sm shrink-0">
                + Agregar capítulo
            </button>
        </form>

        <a href="{{ route('tesis.index') }}" class="inline-flex items-center gap-1.5 mt-6 text-teal hover:underline text-sm font-medium">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Volver a mis tesis
        </a>
    </div>
</x-app-layout>
