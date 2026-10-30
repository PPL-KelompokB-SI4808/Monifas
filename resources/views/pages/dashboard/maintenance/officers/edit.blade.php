<x-layouts.dashboard title="Edit Petugas Pemeliharaan">
    <div class="mx-auto max-w-3xl space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground">Edit Petugas</h1>
                <p class="mt-0.5 text-xs text-muted-foreground">Perbarui informasi dan status petugas pemeliharaan.</p>
            </div>
            <x-ui.button href="{{ route('maintenance.officers.index') }}" variant="outline" class="w-full gap-2 sm:w-auto">
                <x-lucide-arrow-left class="h-4 w-4" />
                Kembali
            </x-ui.button>
        </div>

        @if (session('status'))
            <div class="flex items-center gap-2 rounded-xl border border-primary/20 bg-primary/10 px-4 py-3 text-xs font-medium text-primary">
                <x-lucide-check-circle-2 class="h-4 w-4 shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->has('error'))
            <div class="rounded-xl border border-border bg-secondary px-4 py-3 text-xs font-medium text-foreground">
                {{ $errors->first('error') }}
            </div>
        @endif

        <x-maintenance-officers::officer-form
            :officer="$officer"
            :action="route('maintenance.officers.update', $officer)"
            method="PUT"
        />

        <x-maintenance-officers::officer-skill-badges />

        <x-maintenance-officers::officer-history-list />
    </div>
</x-layouts.dashboard>
