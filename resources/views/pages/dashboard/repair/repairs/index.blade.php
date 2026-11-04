<x-layouts.dashboard title="Riwayat Perbaikan">
    <div class="space-y-5">
        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="flex items-center gap-2 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-xs font-medium text-emerald-600 shadow-2xs dark:text-emerald-400">
                <x-lucide-circle-check class="size-4 shrink-0" />
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-center gap-2 rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-xs font-medium text-red-600 shadow-2xs dark:text-red-400">
                <x-lucide-circle-x class="size-4 shrink-0" />
                {{ session('error') }}
            </div>
        @endif

        {{-- Page Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground">Riwayat Perbaikan</h1>
                <p class="mt-0.5 text-xs text-muted-foreground">Daftar pelaksanaan dan riwayat perbaikan fasilitas.</p>
            </div>

            <a href="{{ route('repair.repairs.create') }}"
               class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-medium text-primary-foreground shadow-2xs transition-colors hover:bg-primary/90">
                <x-lucide-plus class="size-3.5" />
                Tambah Perbaikan
            </a>
        </div>

        @include('pages.dashboard.repair.repairs.partials.repair-filter-bar')

        @include('pages.dashboard.repair.repairs.partials.repair-history-table')
    </div>
</x-layouts.dashboard>