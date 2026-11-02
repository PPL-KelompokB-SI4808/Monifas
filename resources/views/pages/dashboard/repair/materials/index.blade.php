
<x-layouts.dashboard title="Material Perbaikan">
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground">
                    Material Perbaikan
                </h1>
                <p class="mt-0.5 text-xs text-muted-foreground">
                    Kelola penggunaan material dan suku cadang pada proses perbaikan.
                </p>
            </div>

            <x-ui.button
                href="{{ route('repair.materials.create', $repair->id) }}"
                class="w-full gap-2 sm:w-auto"
            >
                <x-lucide-plus class="h-4 w-4" />
                Tambah Material
            </x-ui.button>
        </div>

        @if (session('success'))
            <div class="flex items-center gap-2 rounded-xl border border-primary/20 bg-primary/10 px-4 py-3 text-xs font-medium text-primary">
                <x-lucide-check-circle-2 class="h-4 w-4 shrink-0" />
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="space-y-1 rounded-xl border border-border bg-secondary px-4 py-3 text-xs text-foreground">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <x-repair-materials::material-usage-table
            :materials="$materials"
            :total-material="$totalMaterial"
            :repair="$repair"
        />
    </div>
</x-layouts.dashboard>
