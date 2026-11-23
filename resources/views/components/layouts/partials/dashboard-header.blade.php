<header class="sticky top-0 z-30 flex h-14 items-center justify-between border-b border-border bg-background/80 px-4 backdrop-blur-md md:px-6">

    {{-- Left: hamburger + mobile brand --}}
    <div class="flex items-center gap-3">
        <button
            type="button"
            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-border text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground md:hidden"
            @click="sidebarOpen = true"
            aria-label="Buka navigasi"
        >
            <x-lucide-menu class="h-4 w-4" />
        </button>

        {{-- Mobile logo (shown only when sidebar is hidden) --}}
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 md:hidden">
            <img src="{{ asset('images/monifas-icon-only.svg') }}" alt="MONIFAS" class="h-6 w-6" />
            <span class="text-sm font-bold tracking-tight text-foreground" style="letter-spacing: -0.02em;">MONIFAS</span>
        </a>
    </div>

    {{-- Right: search + notifications + user --}}
    <div class="flex items-center gap-2">

        {{-- Global search --}}
        <div class="relative hidden lg:block">
            <x-lucide-search class="pointer-events-none absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground" />
            <input
                type="text"
                placeholder="Cari fasilitas, laporan…"
                class="h-8 w-56 rounded-lg border border-border bg-secondary/50 pl-9 pr-3 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:bg-background focus:outline-none focus:ring-2 focus:ring-primary/10 transition-colors"
            />
        </div>

        {{-- Notifications --}}
        <x-shared.notification-dropdown />

        {{-- Divider --}}
        <div class="h-5 w-px bg-border"></div>

        {{-- User menu --}}
        <div class="relative" x-data="{ userMenuOpen: false }">
            <button
                type="button"
                @click="userMenuOpen = !userMenuOpen"
                class="flex items-center gap-2 rounded-lg px-2 py-1 transition-colors hover:bg-secondary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring cursor-pointer"
                :aria-expanded="userMenuOpen"
            >
                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-primary/10 text-xs font-semibold text-primary ring-1 ring-primary/20">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                </div>
                <div class="hidden flex-col text-left md:flex">
                    <span class="text-xs font-semibold leading-tight text-foreground">
                        {{ auth()->user()->name ?? 'Pengguna' }}
                    </span>
                    <span class="text-[10px] leading-tight text-muted-foreground">
                        {{ auth()->user()->email ?? '' }}
                    </span>
                </div>
                <x-lucide-chevron-down
                    class="h-3 w-3 text-muted-foreground transition-transform duration-150"
                    ::class="userMenuOpen ? 'rotate-180' : ''"
                />
            </button>

            {{-- Dropdown --}}
            <div
                x-show="userMenuOpen"
                x-cloak
                @click.away="userMenuOpen = false"
                class="absolute right-0 top-10 z-50 w-48 overflow-hidden rounded-xl border border-border bg-background py-1"
                style="box-shadow: 0 8px 30px rgba(0,0,0,0.08);"
            >
                @auth
                    <div class="border-b border-border px-3 py-2.5 md:hidden">
                        <p class="truncate text-xs font-semibold text-foreground">{{ auth()->user()->name }}</p>
                        <p class="truncate text-[10px] text-muted-foreground">{{ auth()->user()->email }}</p>
                    </div>
                @endauth

                <a
                    href="{{ route('profile.edit') }}"
                    class="flex items-center gap-2 px-3 py-2 text-xs text-foreground transition-colors hover:bg-secondary"
                >
                    <x-lucide-user class="h-3.5 w-3.5 text-muted-foreground" />
                    Profil Akun
                </a>

                <div class="my-1 h-px bg-border"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="flex w-full items-center gap-2 px-3 py-2 text-xs text-red-600 transition-colors hover:bg-secondary cursor-pointer"
                    >
                        <x-lucide-log-out class="h-3.5 w-3.5" />
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>

</header>
