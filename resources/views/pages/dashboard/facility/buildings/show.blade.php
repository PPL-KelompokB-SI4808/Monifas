<x-layouts.dashboard title="Detail Gedung">
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <a
                    href="{{ route('facility.buildings.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-background px-3 py-1.5 text-xs font-medium text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                >
                    <x-lucide-arrow-left class="h-3.5 w-3.5" />
                    <span>Kembali</span>
                </a>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-foreground">{{ $building->name }}</h1>
                    <p class="text-xs text-muted-foreground mt-0.5">Kode: {{ $building->code }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a
                    href="{{ route('facility.buildings.edit', $building) }}"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-medium text-primary-foreground shadow-2xs hover:bg-primary/90 transition-colors"
                >
                    <x-lucide-pencil class="h-3.5 w-3.5" />
                    <span>Ubah Gedung</span>
                </a>
                <form
                    action="{{ route('facility.buildings.destroy', $building) }}"
                    method="POST"
                    class="inline-block"
                    onsubmit="return confirm('Yakin ingin menghapus atau menonaktifkan gedung ini?')"
                >
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-red-500/20 bg-red-500/10 px-3.5 py-2 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-500/20 transition-colors cursor-pointer"
                    >
                        <x-lucide-trash-2 class="h-3.5 w-3.5" />
                        <span>Hapus</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Detail Card -->
        <div class="rounded-xl border border-border bg-background p-6 shadow-2xs space-y-6">
            <div class="border-b border-border pb-3">
                <h3 class="text-sm font-semibold text-foreground flex items-center gap-2">
                    <x-lucide-building-2 class="h-4 w-4 text-primary" />
                    <span>Informasi Spesifikasi Gedung</span>
                </h3>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <span class="text-xs font-medium text-muted-foreground">Kode Gedung</span>
                    <p class="mt-1 font-mono text-sm font-semibold text-primary">{{ $building->code }}</p>
                </div>

                <div>
                    <span class="text-xs font-medium text-muted-foreground">Nama Gedung</span>
                    <p class="mt-1 text-sm font-semibold text-foreground">{{ $building->name }}</p>
                </div>

                <div>
                    <span class="text-xs font-medium text-muted-foreground">Lokasi Kawasan</span>
                    <p class="mt-1 text-sm font-medium text-foreground">{{ $building->location->name ?? '-' }}</p>
                </div>

                <div>
                    <span class="text-xs font-medium text-muted-foreground">Zona Area</span>
                    <p class="mt-1 text-sm font-medium text-foreground">{{ $building->areaZone->name ?? '-' }}</p>
                </div>

                <div>
                    <span class="text-xs font-medium text-muted-foreground">Total Lantai</span>
                    <p class="mt-1 text-sm font-semibold text-foreground">{{ $building->total_floors }} Lantai</p>
                </div>

                <div>
                    <span class="text-xs font-medium text-muted-foreground">Status Operasional</span>
                    <div class="mt-1">
                        @if ($building->is_active)
                            <span class="inline-flex items-center gap-1.5 rounded-md border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-1 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                <span>Aktif (Dapat Digunakan)</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-md border border-red-500/20 bg-red-500/10 px-2.5 py-1 text-xs font-medium text-red-600 dark:text-red-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                <span>Nonaktif</span>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <span class="text-xs font-medium text-muted-foreground">Deskripsi Gedung</span>
                    <p class="mt-1 text-xs leading-relaxed text-foreground whitespace-pre-line">{{ $building->description ?: 'Tidak ada deskripsi gedung.' }}</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.dashboard>
