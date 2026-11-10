<x-layouts.dashboard title="Manajemen Lokasi Kawasan">
    <div class="space-y-6">
        <!-- Tab Switcher Terpadu Kategori & Lokasi -->
        <x-shared.category-tab-switcher active="locations" />

        <!-- Page Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground">Master Lokasi Kawasan</h1>
                <p class="text-xs text-muted-foreground mt-0.5">Kelola data master wilayah, hierarki kawasan induk-anak, dan zonasi infrastruktur kampus.</p>
            </div>
            <a
                href="{{ route('location-and-category.locations.create') }}"
                class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-medium text-primary-foreground shadow-2xs hover:bg-primary/90 transition-colors"
            >
                <x-lucide-plus class="h-3.5 w-3.5" />
                <span>Tambah Lokasi</span>
            </a>
        </div>

        <!-- Flash Messages -->
        @if (session('success') || session('status'))
            <div class="flex items-center gap-2 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-xs font-medium text-emerald-600 dark:text-emerald-400 shadow-2xs">
                <x-lucide-check-circle-2 class="h-4 w-4 shrink-0" />
                <span>{{ session('success') ?? session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-xs font-medium text-red-600 dark:text-red-400 shadow-2xs space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="flex items-center gap-2">
                        <x-lucide-alert-circle class="h-4 w-4 shrink-0" />
                        <span>{{ $error }}</span>
                    </p>
                @endforeach
            </div>
        @endif

        <!-- Filter Toolbar -->
        <x-location-and-category-locations::location-filter-bar
            :parents="$parents"
            :regions="$regions"
        />

        <!-- Table Component -->
        <x-location-and-category-locations::location-table
            :locations="$locations"
        />
    </div>
</x-layouts.dashboard>
