<div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
    <!-- Card 1: Total Gedung -->
    <div class="rounded-xl border border-border bg-card p-5 shadow-xs transition hover:shadow-md">
        <div class="flex items-start justify-between">
            <span class="text-xs font-medium text-muted-foreground">Total Gedung</span>
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary/10 text-primary border border-primary/20">
                <x-lucide-building-2 class="h-4 w-4" />
            </div>
        </div>
        <div class="mt-2">
            <div class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">{{ \App\Models\Building::count() }}</div>
        </div>
        <p class="mt-2 text-[11px] text-muted-foreground">Seluruh gedung terdaftar di sistem</p>
    </div>

    <!-- Card 2: Gedung Aktif -->
    <div class="rounded-xl border border-border bg-card p-5 shadow-xs transition hover:shadow-md">
        <div class="flex items-start justify-between">
            <span class="text-xs font-medium text-muted-foreground">Gedung Aktif</span>
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                <x-lucide-check-circle-2 class="h-4 w-4" />
            </div>
        </div>
        <div class="mt-2">
            <div class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">{{ \App\Models\Building::where('is_active', true)->count() }}</div>
        </div>
        <p class="mt-2 text-[11px] text-emerald-600 dark:text-emerald-400">Siap digunakan operasional</p>
    </div>

    <!-- Card 3: Total Lantai -->
    <div class="rounded-xl border border-border bg-card p-5 shadow-xs transition hover:shadow-md">
        <div class="flex items-start justify-between">
            <span class="text-xs font-medium text-muted-foreground">Total Lantai</span>
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                <x-lucide-layers class="h-4 w-4" />
            </div>
        </div>
        <div class="mt-2">
            <div class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">{{ \App\Models\Building::sum('total_floors') }}</div>
        </div>
        <p class="mt-2 text-[11px] text-muted-foreground">Akumulasi lantai seluruh gedung</p>
    </div>
</div>
