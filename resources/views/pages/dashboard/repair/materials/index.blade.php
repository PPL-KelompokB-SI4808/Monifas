
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

            @isset($repair)
                <x-ui.button
                    href="{{ route('repair.materials.create', $repair->id) }}"
                    class="w-full gap-2 sm:w-auto"
                >
                    <x-lucide-plus class="h-4 w-4" />
                    Tambah Material
                </x-ui.button>
            @else
                <x-ui.button
                    href="{{ route('repair.repairs.index') }}"
                    class="w-full gap-2 sm:w-auto"
                >
                    <x-lucide-wrench class="h-4 w-4" />
                    Pilih Perbaikan
                </x-ui.button>
            @endisset
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

        @isset($repair)
            <x-repair-materials::material-usage-table
                :materials="$materials ?? collect()"
                :total-material="$totalMaterial ?? 0"
                :repair="$repair"
            />
        @else
            <x-ui.card class="flex flex-col items-center justify-center p-10 text-center">
                <x-lucide-package class="h-8 w-8 text-muted-foreground" />
                <h2 class="mt-3 text-sm font-semibold text-foreground">Pilih Data Perbaikan Terlebih Dahulu</h2>
                <p class="mt-1 max-w-md text-xs text-muted-foreground">
                    Penggunaan material dicatat per pekerjaan perbaikan. Silakan buka daftar perbaikan lalu pilih perbaikan yang ingin dikelola materialnya.
                </p>
                <x-ui.button href="{{ route('repair.repairs.index') }}" size="sm" class="mt-4">
                    Buka Daftar Perbaikan
                </x-ui.button>
            </x-ui.card>
        @endisset
    </div>
</x-layouts.dashboard>
