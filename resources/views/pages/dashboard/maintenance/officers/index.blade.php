<x-layouts.dashboard title="Petugas Pemeliharaan">
    <div class="space-y-6">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-foreground">Petugas Pemeliharaan</h1>
            <p class="mt-0.5 text-xs text-muted-foreground">Kelola profil, kapasitas, dan ketersediaan petugas.</p>
        </div>

        @if (session('status'))
            <div class="flex items-center gap-2 rounded-xl border border-primary/20 bg-primary/10 px-4 py-3 text-xs font-medium text-primary">
                <x-lucide-check-circle-2 class="h-4 w-4 shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="space-y-1 rounded-xl border border-border bg-secondary px-4 py-3 text-xs font-medium text-foreground">
                @foreach ($errors->all() as $error)
                    <p class="flex items-center gap-2">
                        <x-lucide-alert-circle class="h-4 w-4 shrink-0 text-muted-foreground" />
                        <span>{{ $error }}</span>
                    </p>
                @endforeach
            </div>
        @endif

        <x-maintenance-officers::officer-directory-table :officers="$officers" />
    </div>
</x-layouts.dashboard>
