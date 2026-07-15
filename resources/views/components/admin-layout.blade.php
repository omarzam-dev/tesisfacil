<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — TesisFácil</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600|baloo-2:500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <nav class="bg-gray-900 text-white shadow-lg">
            <div class="max-w-6xl mx-auto px-4 flex items-center justify-between h-16">
                <div class="flex items-center gap-8">
                    <span class="font-heading font-semibold text-lg">TesisFácil <span class="text-teal">Admin</span></span>
                    <div class="hidden md:flex items-center gap-1">
                        @php
                            $navItems = [
                                ['route' => 'admin.index', 'label' => 'Dashboard'],
                                ['route' => 'admin.payments', 'label' => 'Pagos'],
                                ['route' => 'admin.plans', 'label' => 'Planes'],
                                ['route' => 'admin.payment-methods', 'label' => 'Cuentas de pago'],
                                ['route' => 'admin.subscriptions', 'label' => 'Suscripciones'],
                                ['route' => 'admin.users', 'label' => 'Usuarios'],
                            ];
                        @endphp
                        @foreach ($navItems as $item)
                            <a href="{{ route($item['route']) }}"
                                class="px-3 py-2 rounded-lg text-sm font-medium transition
                                    {{ request()->routeIs($item['route']) ? 'bg-white/10 text-white' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-400 hover:text-white flex items-center gap-1.5 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Salir del admin
                </a>
            </div>
        </nav>

        <header class="bg-white border-b border-gray-100">
            <div class="max-w-6xl mx-auto px-4 py-6">
                {{ $header ?? '' }}
            </div>
        </header>

        <main class="max-w-6xl mx-auto px-4 py-8">
            @if (session('success'))
                <div class="alert-success mb-5">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert-error mb-5">{{ session('error') }}</div>
            @endif
            {{ $slot }}
        </main>
    </div>
</body>
</html>
