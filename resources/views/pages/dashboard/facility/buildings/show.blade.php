<x-layouts.dashboard title="Detail Gedung">
    <div class="mx-auto max-w-2xl space-y-5">

        {{-- Page Header --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('facility.buildings.index') }}"
                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-border bg-background text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground">
                    <x-lucide-arrow-left class="h-4 w-4" />
                </a>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-muted-foreground">Facility › Gedung</p>
                    <h1 class="mt-0.5 text-xl font-bold tracking-tight text-foreground" style="letter-spacing: -0.02em;">
                        {{ $building->name }}
                    </h1>
                </div>
            </div>

            <div class="flex shrink-0 items-center gap-2">
                <a href="{{ route('facility.buildings.edit', $building) }}"
                   class="inline-flex h-8 items-center gap-1.5 rounded-lg bg-primary px-3.5 text-xs font-medium text-primary-foreground transition-colors hover:bg-primary/90">
                    <x-lucide-pencil class="h-3.5 w-3.5" />
                    Ubah
                </a>
                <form action="{{ route('facility.buildings.destroy', $building) }}"
                      method="POST"
                      class="inline-block"
                      onsubmit="return confirm('Yakin ingin menghapus gedung ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex h-8 items-center gap-1.5 rounded-lg border border-red-500/20 bg-red-500/10 px-3.5 text-xs font-medium text-red-600 transition-colors hover:bg-red-500/20 cursor-pointer">
                        <x-lucide-trash-2 class="h-3.5 w-3.5" />
                        Hapus
                    </button>
                </form>
            </div>
        </div>

        {{-- Detail Card --}}
        <div class="rounded-xl border border-border bg-background p-6">
            <div class="flex items-center gap-2 border-b border-border pb-4">
                <x-lucide-building-2 class="h-4 w-4 text-primary" />
                <h2 class="text-sm font-semibold text-foreground">Spesifikasi Gedung</h2>
            </div>

            <dl class="mt-5 grid grid-cols-1 gap-x-8 gap-y-5 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-medium text-muted-foreground">Kode Gedung</dt>
                    <dd class="mt-1 font-mono text-sm font-semibold text-primary">{{ $building->code }}</dd>
                </div>

                <div>
                    <dt class="text-xs font-medium text-muted-foreground">Nama Gedung</dt>
                    <dd class="mt-1 text-sm font-semibold text-foreground">{{ $building->name }}</dd>
                </div>

                <div>
                    <dt class="text-xs font-medium text-muted-foreground">Lokasi Kawasan</dt>
                    <dd class="mt-1 text-sm text-foreground">{{ $building->location->name ?? '—' }}</dd>
                </div>

                <div>
                    <dt class="text-xs font-medium text-muted-foreground">Zona Area</dt>
                    <dd class="mt-1 text-sm text-foreground">{{ $building->areaZone->name ?? '—' }}</dd>
                </div>

                <div>
                    <dt class="text-xs font-medium text-muted-foreground">Total Lantai</dt>
                    <dd class="mt-1 text-sm font-semibold text-foreground">{{ $building->total_floors }} Lantai</dd>
                </div>

                <div>
                    <dt class="text-xs font-medium text-muted-foreground">Status Operasional</dt>
                    <dd class="mt-1">
                        @if ($building->is_active)
                            <span class="inline-flex items-center gap-1 rounded-full border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-0.5 text-xs font-medium text-emerald-600">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full border border-border bg-secondary px-2.5 py-0.5 text-xs font-medium text-muted-foreground">
                                <span class="h-1.5 w-1.5 rounded-full bg-muted-foreground/50"></span>
                                Nonaktif
                            </span>
                        @endif
                    </dd>
                </div>

                <div class="sm:col-span-2">
                    <dt class="text-xs font-medium text-muted-foreground">Deskripsi</dt>
                    <dd class="mt-1 text-sm leading-relaxed text-foreground whitespace-pre-line">
                        {{ $building->description ?: '—' }}
                    </dd>
                </div>
            </dl>
        </div>

    </div>
</x-layouts.dashboard>
