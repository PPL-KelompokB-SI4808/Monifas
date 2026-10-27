<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-background text-foreground antialiased">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MONIFAS') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-full flex flex-col justify-center bg-secondary/30 py-12 px-4 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="flex justify-center">
                <a href="/" class="flex items-center gap-2.5 group">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-primary-foreground text-base font-bold shadow-xs transition-transform group-hover:scale-105">
                        M
                    </span>
                    <div class="flex flex-col text-left">
                        <span class="text-xl font-bold tracking-tight text-foreground leading-none">MONIFAS</span>
                        <span class="text-[11px] text-muted-foreground mt-0.5">Facility Monitoring Platform</span>
                    </div>
                </a>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="rounded-xl border border-border bg-background p-6 shadow-sm sm:p-8">
                {{ $slot }}
            </div>
            <p class="mt-6 text-center text-xs text-muted-foreground">
                &copy; {{ date('Y') }} MONIFAS. Seluruh hak cipta dilindungi.
            </p>
        </div>
    </body>
</html>
