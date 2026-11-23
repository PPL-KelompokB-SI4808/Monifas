@props([
    'title' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-background text-foreground antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title . ' — ' . config('app.name', 'MONIFAS') : config('app.name', 'MONIFAS') . ' — Dashboard' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full overflow-hidden bg-background" x-data="{ sidebarOpen: false }">
    <div class="flex h-full w-full">

        {{-- Mobile sidebar backdrop --}}
        <div
            x-show="sidebarOpen"
            x-cloak
            @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm transition-opacity md:hidden"
        ></div>

        <x-layouts.partials.dashboard-sidebar />

        <div class="flex flex-1 flex-col min-w-0 overflow-hidden">
            <x-layouts.partials.dashboard-header />

            <main class="flex-1 overflow-y-auto bg-secondary/30 p-4 md:p-6 lg:p-8">
                <div class="mx-auto max-w-7xl">
                    {{ $slot }}
                </div>
            </main>
        </div>

    </div>
</body>
</html>
