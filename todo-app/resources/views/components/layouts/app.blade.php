<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: false }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'TODO App') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gradient-to-br from-blue-400 to-blue-600 dark:from-gray-900 dark:to-gray-800 min-h-screen font-sans transition-colors duration-300">

    {{-- Dark mode toggle --}}
    <div class="fixed top-4 right-4 z-50">
        <button
            @click="darkMode = !darkMode"
            class="flex items-center gap-2 px-3 py-2 rounded-full bg-white/20 backdrop-blur text-white text-sm font-medium hover:bg-white/30 transition"
        >
            <span x-show="!darkMode">🌙 Dark mode</span>
            <span x-show="darkMode">☀️ Light mode</span>
        </button>
    </div>

    {{-- Toasts --}}
    <div
        x-data="toastManager()"
        x-on:toast.window="show($event.detail)"
        class="fixed top-4 left-1/2 -translate-x-1/2 z-50 flex flex-col gap-2 pointer-events-none"
    >
        <template x-for="toast in toasts" :key="toast.id">
            <div
                x-show="toast.visible"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                :class="{
                    'bg-green-500': toast.type === 'success',
                    'bg-red-500':   toast.type === 'error',
                    'bg-blue-500':  toast.type === 'info',
                }"
                class="pointer-events-auto px-5 py-3 rounded-lg text-white text-sm font-medium shadow-lg"
                x-text="toast.message"
            ></div>
        </template>
    </div>

    <main class="container mx-auto px-4 py-10 max-w-7xl">
        {{ $slot }}
    </main>

    <p class="text-center text-white/70 text-sm pb-6 font-semibold tracking-wide">
        Pagina principal
    </p>

    @livewireScripts

    <script>
        function toastManager() {
            return {
                toasts: [],
                show({ message, type = 'success' }) {
                    const id = Date.now();
                    this.toasts.push({ id, message, type, visible: true });
                    setTimeout(() => {
                        const toast = this.toasts.find(t => t.id === id);
                        if (toast) toast.visible = false;
                        setTimeout(() => {
                            this.toasts = this.toasts.filter(t => t.id !== id);
                        }, 300);
                    }, 3000);
                }
            }
        }
    </script>
</body>
</html>