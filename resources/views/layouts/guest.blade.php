<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MONIFAS') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="relative min-h-full bg-background antialiased">
        {{-- Dot grid background --}}
        <div class="pointer-events-none fixed inset-0"
             style="background-image: radial-gradient(circle, #e8e8ec 1px, transparent 1px); background-size: 28px 28px; opacity: 0.5;"></div>

        {{-- Minimal top bar --}}
        <div class="relative z-10 flex h-14 items-center border-b border-border bg-background/80 px-6 backdrop-blur-md">
            <a href="/" class="flex items-center gap-2.5">
                <img src="{{ asset('images/monifas-icon-only.svg') }}" alt="MONIFAS" class="h-7 w-7" />
                <span class="font-display text-sm font-bold text-foreground" style="letter-spacing: -0.03em;">MONIFAS</span>
            </a>
        </div>

        {{-- Centered form area --}}
        <div class="relative z-10 flex min-h-[calc(100vh-3.5rem)] items-center justify-center px-4 py-12">
            <div class="w-full max-w-sm">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
