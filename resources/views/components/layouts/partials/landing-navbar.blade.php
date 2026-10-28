<header class="sticky top-0 z-30 border-b border-border bg-background">
    <div class="mx-auto flex h-14 max-w-6xl items-center justify-between px-4">
        <a href="/" class="text-base font-bold">MONIFAS</a>
        <nav class="hidden items-center gap-6 text-sm text-muted-foreground md:flex">
            <a href="#cakupan" class="hover:text-foreground">Fasilitas</a>
            <a href="#alur" class="hover:text-foreground">Cara kerja</a>
        </nav>
        <div class="flex items-center gap-2">
            @auth
                <x-ui.button href="{{ route('dashboard') }}">Dashboard</x-ui.button>
            @else
                <x-ui.button href="{{ route('login') }}">Masuk</x-ui.button>
            @endauth
        </div>
    </div>
</header>
