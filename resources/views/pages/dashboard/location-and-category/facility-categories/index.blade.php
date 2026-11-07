<x-layouts.dashboard title="Master Kategori Fasilitas">
    <div class="space-y-6">
        <!-- Tab Switcher Terpadu Kategori & Lokasi -->
        <x-shared.category-tab-switcher active="facility-categories" />

        <!-- Page Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground">Master Kategori Fasilitas</h1>
                <p class="text-xs text-muted-foreground mt-0.5">Klasifikasi aset infrastruktur dan fasilitas kampus (seperti Listrik, HVAC, Plumbing, dan Sipil).</p>
            </div>
            <a
                href="{{ route('location-and-category.facility-categories.create') }}"
                class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-medium text-primary-foreground shadow-2xs hover:bg-primary/90 transition-colors"
            >
                <x-lucide-plus class="h-3.5 w-3.5" />
                <span>Tambah Kategori</span>
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
        <x-location-and-category-facility-categories::facility-category-filter-bar />

        <!-- Table Component -->
        <x-location-and-category-facility-categories::facility-category-table :categories="$categories" />
    </div>
</x-layouts.dashboard>
