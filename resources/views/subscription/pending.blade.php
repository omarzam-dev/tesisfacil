<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading text-2xl text-gray-800">Pago en revisión</h2>
    </x-slot>

    <div class="py-16 px-4 text-center max-w-md mx-auto">
        <div class="w-16 h-16 rounded-full bg-naranja/10 flex items-center justify-center mx-auto mb-5">
            <svg class="w-8 h-8 text-naranja" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <p class="font-heading text-gray-800 text-xl">Estamos revisando tu comprobante</p>
        <p class="text-sm text-gray-500 mt-2">Tu plan se activa apenas confirmemos el pago, normalmente en menos de 24 horas.</p>
    </div>
</x-app-layout>
