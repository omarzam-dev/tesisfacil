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
<body class="font-sans antialiased bg-lienzo">
    <div class="lg:flex min-h-screen">
        @include('layouts.sidebar')

        <div class="flex-1 min-w-0">
            @isset($header)
                <header class="bg-gradient-to-r from-teal-suave via-white to-lavanda-suave border-b border-borde">
                    {{-- Franja de 4 colores de la marca ZSystem --}}
                    <div class="flex h-1">
                        <span class="flex-1 bg-verde"></span><span class="flex-1 bg-teal"></span><span class="flex-1 bg-naranja"></span><span class="flex-1 bg-rosa"></span>
                    </div>
                    <div class="px-4 sm:px-6 py-5">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
