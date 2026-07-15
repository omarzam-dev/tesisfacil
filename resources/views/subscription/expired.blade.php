<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading text-2xl text-gray-800">Tu acceso venció</h2>
    </x-slot>

    <div class="py-16 px-4 text-center max-w-md mx-auto">
        <div class="w-16 h-16 rounded-full bg-rosa/10 flex items-center justify-center mx-auto mb-5">
            <svg class="w-8 h-8 text-rosa" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
        </div>
        <p class="font-heading text-gray-800 text-xl">Tu período de prueba o plan terminó</p>
        <p class="text-sm text-gray-500 mt-2 mb-6">Elige un plan para seguir usando TesisFácil sin interrupciones.</p>
        <a href="{{ route('subscription.plans') }}" class="btn-primary">
            Ver planes
        </a>
    </div>
</x-app-layout>
