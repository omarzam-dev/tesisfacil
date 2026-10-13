<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-heading text-2xl text-gray-800">Perfiles de formato</h2>
        <p class="text-sm text-gray-500 mt-1">Márgenes, fuente e interlineado de cada universidad. El tesista elige su perfil al revisar su documento. Sirve para ISO 690 (que no define formato) y para universidades que cambian el formato APA.</p>
    </x-slot>

    @if (session('success'))
        <div class="alert-success mb-5">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="rounded-lg border border-rosa/30 bg-rosa/10 text-rosa px-4 py-3 text-sm mb-5 space-y-1">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if ($perfiles->isEmpty())
        <div class="card mb-6 text-sm text-gray-600">
            Todavía no hay perfiles. Crea el primero con los datos de la guía de tesis de la universidad (suelen estar en el capítulo de "Aspectos de forma" o "Formato de presentación").
        </div>
    @else
        <div class="grid sm:grid-cols-2 gap-5 mb-10">
            @foreach ($perfiles as $perfil)
                <form method="POST" action="{{ route('admin.perfiles.update', $perfil) }}" class="card space-y-4 {{ $perfil->activo ? '' : 'opacity-60' }}">
                    @csrf @method('PUT')
                    <div class="flex items-center justify-between gap-2">
                        <p class="font-heading text-lg text-gray-800">{{ $perfil->nombre }}</p>
                        @unless ($perfil->activo)
                            <span class="badge badge-naranja">Oculto</span>
                        @endunless
                    </div>
                    @include('admin.partials.perfil-campos', ['perfil' => $perfil])
                    <button class="btn-primary btn-sm">Guardar cambios</button>
                </form>
            @endforeach
        </div>
    @endif

    <p class="section-heading">Nuevo perfil</p>
    <form method="POST" action="{{ route('admin.perfiles.store') }}" class="card space-y-4 max-w-xl">
        @csrf
        @include('admin.partials.perfil-campos', ['perfil' => null])
        <button class="btn-primary btn-sm">Crear perfil</button>
    </form>

    <p class="text-xs text-gray-500 mt-6 max-w-xl">
        Los perfiles no se eliminan porque las revisiones anteriores los mencionan. Para retirar uno, desmarca "Visible para los tesistas".
    </p>
</x-admin-layout>
