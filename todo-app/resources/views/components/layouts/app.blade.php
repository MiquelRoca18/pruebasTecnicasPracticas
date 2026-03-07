<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'TODO App') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gradient-to-br from-blue-400 to-blue-600 min-h-screen font-sans">

    <main class="container mx-auto px-4 py-10 max-w-7xl">
        {{ $slot }}
    </main>

    <p class="text-center text-white/70 text-sm pb-6 font-semibold tracking-wide">
        Pagina principal
    </p>

    @livewireScripts
</body>
</html>