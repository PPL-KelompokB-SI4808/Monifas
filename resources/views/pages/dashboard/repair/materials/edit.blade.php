
<x-layouts.dashboard title="Edit Material Perbaikan">
    <div class="mx-auto max-w-3xl space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground">
                    Edit Material Perbaikan
                </h1>
                <p class="mt-0.5 text-xs text-muted-foreground">
                    Perbarui informasi material atau suku cadang yang digunakan.
                </p>
            </div>

            <x-ui.button
                href="{{ route('repair.materials.index', $material->repair_id) }}"
                variant="outline"
                class="w-full gap-2 sm:w-auto"
            >
                <x-lucide-arrow-left class="h-4 w-4" />
                Kembali
            </x-ui.button>
        </div>

        @if ($errors->any())
            <div class="space-y-1 rounded-xl border border-border bg-secondary px-4 py-3 text-xs text-foreground">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <x-repair-materials::material-edit-form
            :material="$material"
            :action="route('repair.materials.update', $material)"
        />
    </div>
</x-layouts.dashboard>
