<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading text-2xl text-gray-800">Pagar {{ $plan->name }}</h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 max-w-lg">
        <div class="card">
            <p class="text-sm text-gray-600 mb-4">
                Plan <strong>{{ $plan->name }}</strong> — S/ {{ number_format($plan->price, 0) }}/mes.
                Realiza el pago a cualquiera de estas cuentas y sube tu comprobante. Tu suscripción se activa apenas lo aprobemos.
            </p>

            <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-600 mb-5 space-y-1.5 border border-gray-100">
                @forelse ($paymentMethods as $method)
                    <p class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-teal"></span><strong>{{ $method->type }}:</strong> {{ $method->account_info }} <span class="text-gray-400">({{ $method->label }})</span></p>
                @empty
                    <p class="text-rosa">Aún no hay cuentas de pago configuradas. Contacta al administrador.</p>
                @endforelse
            </div>

            @if ($errors->any())
                <div class="alert-error mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('subscription.upload-proof', $plan) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Método de pago</label>
                    <select name="method" required class="input-field">
                        @foreach ($paymentMethods as $method)
                            <option value="{{ $method->label }}">{{ $method->label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Comprobante (captura de pantalla)</label>
                    <input type="file" name="proof_image" accept="image/*" required
                        class="block w-full text-sm border border-gray-300 rounded-lg p-2.5
                               file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-teal/10 file:text-teal file:text-xs file:font-semibold hover:file:bg-teal/20">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">N° de operación (opcional)</label>
                    <input type="text" name="transaction_ref" class="input-field">
                </div>

                <button type="submit" class="btn-primary w-full justify-center">Enviar comprobante</button>
            </form>
        </div>
    </div>
</x-app-layout>
