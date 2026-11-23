<x-layouts.dashboard title="Manajemen Gedung">
    <div class="space-y-5">

        {{-- Page Header --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-muted-foreground">Facility</p>
                <h1 class="mt-1 text-xl font-bold tracking-tight text-foreground" style="letter-spacing: -0.02em;">
                    Manajemen Gedung
                </h1>
            </div>
            <a href="{{ route('facility.buildings.create') }}"
               class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-xs font-medium text-primary-foreground transition-colors hover:bg-primary/90">
                <x-lucide-plus class="h-3.5 w-3.5" />
                Tambah Gedung
            </a>
        </div>

        {{-- Flash --}}
        @if (session('success') || session('status'))
            <div class="flex items-center gap-2.5 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-xs font-medium text-emerald-600">
                <x-lucide-check-circle-2 class="h-4 w-4 shrink-0" />
                {{ session('success') ?? session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-xs text-red-600 space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="flex items-center gap-2">
                        <x-lucide-alert-circle class="h-3.5 w-3.5 shrink-0" />
                        {{ $error }}
                    </p>
                @endforeach
            </div>
        @endif

        {{-- Summary --}}
        <x-facility-buildings::building-summary-cards />

        {{-- Filter --}}
        <x-facility-buildings::building-filter-bar :locations="$locations" />

        {{-- Table --}}
        <x-facility-buildings::building-table :buildings="$buildings" />

    </div>
</x-layouts.dashboard>
