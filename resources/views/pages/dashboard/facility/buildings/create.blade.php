<x-layouts.dashboard title="Tambah Gedung Baru">
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Page Header -->
        <div class="flex items-center gap-3">
            <a
                href="{{ route('facility.buildings.index') }}"
                class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-background px-3 py-1.5 text-xs font-medium text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
            >
                <x-lucide-arrow-left class="h-3.5 w-3.5" />
                <span>Kembali</span>
            </a>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground">Tambah Gedung Baru</h1>
                <p class="text-xs text-muted-foreground mt-0.5">Lengkapi formulir berikut untuk mendaftarkan gedung baru ke sistem.</p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="rounded-xl border border-border bg-background p-6 shadow-2xs">
            <form action="{{ route('facility.buildings.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="border-b border-border pb-3">
                    <h3 class="text-sm font-semibold text-foreground flex items-center gap-2">
                        <x-lucide-building class="h-4 w-4 text-primary" />
                        <span>Informasi Gedung</span>
                    </h3>
                </div>

                <x-facility-buildings::building-form
                    :locations="$locations"
                    :area-zones="$areaZones"
                />

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-border">
                    <a
                        href="{{ route('facility.buildings.index') }}"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-background px-4 py-2 text-xs font-medium text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                    >
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-xs font-medium text-primary-foreground shadow-2xs hover:bg-primary/90 transition-colors cursor-pointer"
                    >
                        <x-lucide-save class="h-3.5 w-3.5" />
                        <span>Simpan Gedung</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.dashboard>
