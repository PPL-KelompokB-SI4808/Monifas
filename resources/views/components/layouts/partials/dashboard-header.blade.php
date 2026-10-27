<header class="sticky top-0 z-30 flex h-14 items-center justify-between border-b border-border bg-background/95 px-4 backdrop-blur md:px-6">
    <div class="flex items-center gap-3">
        <!-- Mobile hamburger -->
        <button
            type="button"
            class="rounded-lg p-2 text-muted-foreground hover:bg-secondary hover:text-foreground md:hidden"
            @click="sidebarOpen = true"
            aria-label="Buka menu navigasi"
        >
            <x-lucide-menu class="h-5 w-5" />
        </button>

        <!-- Current Section / Title Indicator -->
        <div class="hidden sm:flex items-center gap-2 text-sm text-muted-foreground">
            <span class="font-medium text-foreground">Dashboard</span>
            <span>/</span>
            <span class="text-xs font-normal">MONIFAS Platform</span>
        </div>
    </div>

    <!-- Right Header Actions -->
    <div class="flex items-center gap-3">
        <!-- Search bar -->
        <div class="relative hidden lg:block w-64">
            <x-lucide-search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
            <input
                type="text"
                placeholder="Cari fasilitas, laporan..."
                class="w-full rounded-lg border border-border bg-muted/40 py-1.5 pl-9 pr-3 text-xs placeholder:text-muted-foreground focus:border-primary focus:bg-background focus:outline-none focus:ring-1 focus:ring-ring"
            />
        </div>

        <!-- Notification Icon -->
        <button
            type="button"
            class="relative rounded-lg p-2 text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
            aria-label="Notifikasi"
        >
            <x-lucide-bell class="h-4 w-4" />
            <span class="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-primary"></span>
        </button>

        <!-- User Profile Dropdown -->
        <div class="relative flex items-center pl-2 border-l border-border" x-data="{ userMenuOpen: false }">
            <button
                type="button"
                @click="userMenuOpen = !userMenuOpen"
                class="flex items-center gap-2.5 rounded-lg p-1 hover:bg-secondary transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring cursor-pointer"
                aria-expanded="false"
                :aria-expanded="userMenuOpen"
            >
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10 text-primary font-semibold text-xs border border-primary/20">
                    {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                </div>
                <div class="hidden md:flex flex-col text-left">
                    <span class="text-xs font-semibold text-foreground leading-tight">{{ auth()->user()->name ?? 'Pengguna' }}</span>
                    <span class="text-[10px] text-muted-foreground">{{ auth()->user()->email ?? 'user@monifas.id' }}</span>
                </div>
                <x-lucide-chevron-down class="h-3.5 w-3.5 text-muted-foreground transition-transform duration-200" ::class="userMenuOpen ? 'rotate-180' : ''" />
            </button>

            <!-- Dropdown Menu -->
            <div
                x-show="userMenuOpen"
                x-cloak
                @click.away="userMenuOpen = false"
                class="absolute right-0 top-11 z-50 w-48 rounded-lg border border-border bg-background py-1 shadow-lg"
            >
                @auth
                    <div class="px-3 py-2 border-b border-border md:hidden">
                        <p class="text-xs font-medium text-foreground truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-muted-foreground truncate">{{ auth()->user()->email }}</p>
                    </div>
                @endauth
                <a
                    href="{{ route('profile.edit') }}"
                    class="flex items-center gap-2 px-3 py-2 text-xs text-foreground hover:bg-secondary transition-colors"
                >
                    <x-lucide-user class="h-3.5 w-3.5 text-muted-foreground" />
                    <span>Profil Akun</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="flex w-full items-center gap-2 px-3 py-2 text-xs text-red-600 dark:text-red-400 hover:bg-secondary transition-colors cursor-pointer"
                    >
                        <x-lucide-log-out class="h-3.5 w-3.5 text-red-500" />
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
