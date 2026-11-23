<header class="sticky top-0 z-30 border-b border-border bg-background/80 backdrop-blur-md">
    <div class="mx-auto flex h-14 max-w-6xl items-center justify-between px-6">
        <a href="/" class="text-sm font-bold tracking-tight text-foreground">MONIFAS</a>
        <nav class="hidden items-center gap-6 md:flex">
            <a href="#cakupan" class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground">Fasilitas</a>
            <a href="#alur" class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground">Cara kerja</a>
        </nav>
        <div class="flex items-center gap-2">
            @auth
                <x-ui.button href="{{ route('dashboard') }}" class="h-9 px-4 text-sm">Dashboard</x-ui.button>
            @else
                <x-ui.button href="{{ route('login') }}" variant="outline" class="h-9 px-4 text-sm">Masuk</x-ui.button>
                <x-ui.button href="{{ route('register') }}" class="h-9 px-4 text-sm">Daftar</x-ui.button>
            @endauth
        </div>
    </div>
</header>
