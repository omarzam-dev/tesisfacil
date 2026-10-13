<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TesisFácil') }}</title>

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600|baloo-2:500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen lg:flex">

        <!-- Panel izquierdo 70% -->
        <div class="hidden lg:flex lg:w-[70%] relative overflow-hidden">
            {{--
                Reemplaza este placeholder con una foto real:
                pon una imagen en public/images/auth-bg.jpg (una foto libre de
                Unsplash/Pexels, buscando "university student studying") y
                descomenta la siguiente linea (borra el div con el gradiente):
            --}}
            <img src="{{ asset('images/auth-bg.jpg') }}" alt="" class="absolute inset-0 w-full h-full object-cover">
            

            <!-- Overlay: oscurece la parte inferior para que el texto resalte sobre la foto -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>

            <div class="relative z-10 flex flex-col justify-end p-12 text-white">
                <p class="font-heading text-3xl leading-tight max-w-md drop-shadow-sm">
                    Escribe tu tesis sin errores de formato APA.
                </p>
                <p class="text-gray-200 mt-3 max-w-sm drop-shadow-sm">
                    Corrector automático, gestor de capítulos y control de versiones — todo en un solo lugar.
                </p>
            </div>
        </div>

        <!-- Panel derecho 30% -->
        <div class="w-full lg:w-[30%] flex flex-col justify-center px-6 sm:px-10 py-10 bg-lienzo">
            <div class="mb-6">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-teal transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver al inicio
                </a>
            </div>

            <a href="{{ route('home') }}" class="mb-8">
                <x-application-logo class="h-9 w-auto text-gray-800" />
            </a>

            <div class="w-full bg-white shadow-sm border border-borde rounded-xl p-8">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
