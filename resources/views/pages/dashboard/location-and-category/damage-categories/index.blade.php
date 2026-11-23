<x-layouts.dashboard title="Master Kategori Kerusakan">
    <div class="space-y-6">

        {{-- Page Header --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h1 class="text-xl font-bold text-foreground" style="letter-spacing: -0.03em;">Master Kategori Kerusakan</h1>
                <p class="mt-0.5 text-xs text-muted-foreground">Klasifikasi jenis kerusakan fisik/teknis dan panduan indikasi tingkat urgensi penanganan (severity).</p>
            </div>
            <a
                href="{{ route('location-and-category.damage-categories.create') }}"
                class="inline-flex shrink-0 items-center gap-1.5 rounded-[6px] h-[38px] px-4 text-sm font-medium bg-primary text-primary-foreground transition-all hover:bg-primary-hover hover:shadow-[0_4px_12px_rgba(99,102,241,0.35)] hover:-translate-y-px active:translate-y-0"
            >
                <x-lucide-plus class="h-4 w-4" />
                <span>Tambah Kategori Kerusakan</span>
            </a>
        </div>

        {{-- Flash Messages --}}
        @if (session('success') || session('status'))
            <div class="flex items-center gap-2.5 rounded-[8px] border border-success/20 bg-success/10 px-4 py-3 text-sm font-medium text-success">
                <x-lucide-check-circle-2 class="h-4 w-4 shrink-0" />
                <span>{{ session('success') ?? session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-[8px] border border-error/20 bg-error/10 px-4 py-3 text-sm font-medium text-error space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="flex items-center gap-2">
                        <x-lucide-alert-circle class="h-4 w-4 shrink-0" />
                        <span>{{ $error }}</span>
                    </p>
                @endforeach
            </div>
        @endif

        {{-- Filter Toolbar --}}
        <x-location-and-category-damage-categories::damage-category-filter-bar :severityHints="$severityHints" />

        {{-- Table --}}
        <x-location-and-category-damage-categories::damage-category-table :categories="$categories" />

    </div>
</x-layouts.dashboard>
