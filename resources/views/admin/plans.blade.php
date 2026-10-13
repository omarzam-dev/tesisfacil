<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-heading text-2xl text-gray-800">Planes</h2>
        <p class="text-sm text-gray-500 mt-1">Lo que cambies aquí se ve al instante en la página de precios. La prueba gratuita no se edita aquí.</p>
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

    <div class="card mb-6 flex flex-wrap gap-8">
        <div>
            <p class="text-xs text-gray-500 font-medium">Usos de IA este mes</p>
            <p class="font-heading text-2xl text-gray-800">{{ $usosMes }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-medium">Gasto de IA este mes</p>
            <p class="font-heading text-2xl text-gray-800">US$ {{ number_format($gastoMes, 2) }}</p>
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        @foreach ($plans as $plan)
            <form method="POST" action="{{ route('admin.plans.update', $plan) }}" class="card space-y-4 {{ $plan->activo ? '' : 'opacity-60' }}">
                @csrf @method('PUT')

                <div class="flex items-center justify-between gap-2">
                    <p class="font-heading text-lg text-gray-800">{{ $plan->name }}</p>
                    <div class="flex gap-1">
                        @if ($plan->destacado)
                            <span class="badge badge-teal">Recomendado</span>
                        @endif
                        @unless ($plan->activo)
                            <span class="badge badge-naranja">Oculto</span>
                        @endunless
                    </div>
                </div>

                @include('admin.partials.plan-campos', ['plan' => $plan])

                <button class="btn-primary btn-sm">Guardar cambios</button>
            </form>
        @endforeach
    </div>

    <p class="section-heading mt-10">Nuevo plan</p>
    <form method="POST" action="{{ route('admin.plans.store') }}" class="card space-y-4 max-w-xl">
        @csrf
        @include('admin.partials.plan-campos', ['plan' => null])
        <button class="btn-primary btn-sm">Crear plan</button>
    </form>

    <p class="text-xs text-gray-500 mt-6 max-w-xl">
        Los planes no se eliminan porque guardan el historial de pagos y suscripciones.
        Para retirar uno, desmarca "Visible" y deja de mostrarse en la web.
        Cambiar el precio no altera las suscripciones que ya están activas; solo afecta los pagos nuevos.
    </p>
</x-admin-layout>
