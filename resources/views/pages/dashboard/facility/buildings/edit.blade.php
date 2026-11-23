<x-layouts.dashboard title="Edit Gedung">
    <div class="mx-auto max-w-2xl space-y-5">

        {{-- Page Header --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('facility.buildings.index') }}"
               class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-border bg-background text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground">
                <x-lucide-arrow-left class="h-4 w-4" />
            </a>
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-muted-foreground">Facility › Gedung</p>
                <h1 class="mt-0.5 text-xl font-bold tracking-tight text-foreground" style="letter-spacing: -0.02em;">
                    Edit — {{ $building->name }}
                </h1>
            </div>
        </div>

        {{-- Form Card --}}
        <div class="rounded-xl border border-border bg-background p-6">
            <form action="{{ route('facility.buildings.update', $building->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="flex items-center gap-2 border-b border-border pb-4">
                    <x-lucide-building class="h-4 w-4 text-primary" />
                    <h2 class="text-sm font-semibold text-foreground">Perbarui Informasi Gedung</h2>
                </div>

                <x-facility-buildings::building-form
                    :building="$building"
                    :locations="$locations"
                    :area-zones="$areaZones"
                />

                <div class="flex items-center justify-end gap-2 border-t border-border pt-5">
                    <a href="{{ route('facility.buildings.index') }}"
                       class="inline-flex h-9 items-center rounded-lg border border-border bg-background px-4 text-xs font-medium text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-primary px-4 text-xs font-medium text-primary-foreground transition-colors hover:bg-primary/90 cursor-pointer">
                        <x-lucide-save class="h-3.5 w-3.5" />
                        Perbarui Gedung
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-layouts.dashboard>
