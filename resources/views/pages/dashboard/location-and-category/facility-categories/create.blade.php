<x-layouts.dashboard title="Tambah Kategori Fasilitas">
    <div class="space-y-6">
        <!-- Breadcrumb / Back Link -->
        <div class="flex items-center gap-2 text-xs text-muted-foreground">
            <a href="{{ route('location-and-category.facility-categories.index') }}" class="hover:text-foreground inline-flex items-center gap-1 transition-colors">
                <x-lucide-arrow-left class="h-3.5 w-3.5" />
                <span>Kembali ke Daftar Kategori Fasilitas</span>
            </a>
        </div>

        <!-- Page Header -->
        <div>
            <h1 class="text-xl font-bold tracking-tight text-foreground">Tambah Kategori Fasilitas Baru</h1>
            <p class="text-xs text-muted-foreground mt-0.5">Daftarkan jenis kategori fasilitas baru untuk pengelompokan inventaris kampus.</p>
        </div>

        <!-- Flash Messages -->
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

        <!-- Card Form -->
        <div class="rounded-xl border border-border bg-card p-6 shadow-2xs">
            <form action="{{ route('location-and-category.facility-categories.store') }}" method="POST">
                @csrf

                <x-location-and-category-facility-categories::facility-category-form :defaultCategories="$defaultCategories" />

                <div class="mt-8 flex items-center justify-end gap-3 border-t border-border pt-4">
                    <a
                        href="{{ route('location-and-category.facility-categories.index') }}"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-background px-4 py-2 text-xs font-medium text-foreground hover:bg-accent transition-colors"
                    >
                        <span>Batal</span>
                    </a>
                    <button
                        type="submit"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-xs font-medium text-primary-foreground shadow-2xs hover:bg-primary/90 transition-colors"
                    >
                        <x-lucide-check class="h-3.5 w-3.5" />
                        <span>Simpan Kategori</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.dashboard>
