
<x-layouts.dashboard title="Tambah Material Perbaikan">
    <div class="mx-auto max-w-4xl space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground">
                    Tambah Material Perbaikan
                </h1>
                <p class="mt-0.5 text-xs text-muted-foreground">
                    Catat material dan suku cadang yang digunakan dalam proses perbaikan.
                </p>
            </div>

            <x-ui.button
                href="{{ route('repair.materials.index', $repair->id) }}"
                variant="outline"
                class="w-full gap-2 sm:w-auto"
            >
                <x-lucide-arrow-left class="h-4 w-4" />
                Kembali
            </x-ui.button>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-border bg-secondary px-4 py-3 text-xs text-foreground">
                <p class="font-semibold">Periksa kembali data yang dimasukkan.</p>
                <ul class="mt-2 list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <x-repair-materials::material-repeater-form
            :repair="$repair"
            :action="route('repair.materials.store')"
        />
    </div>
</x-layouts.dashboard>
