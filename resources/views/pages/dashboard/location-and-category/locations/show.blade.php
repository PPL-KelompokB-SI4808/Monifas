<x-layouts.dashboard title="Detail Lokasi">
    <div class="max-w-4xl mx-auto space-y-6">

        {{-- Page Header --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <a
                    href="{{ route('location-and-category.locations.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-[6px] border border-border bg-background px-3 py-1.5 text-xs font-medium text-muted-foreground transition-all hover:bg-secondary hover:text-foreground hover:-translate-y-px"
                >
                    <x-lucide-arrow-left class="h-3.5 w-3.5" />
                    <span>Kembali</span>
                </a>
                <div>
                    <h1 class="text-xl font-bold text-foreground" style="letter-spacing: -0.03em;">{{ $location->name }}</h1>
                    <p class="mt-0.5 text-xs text-muted-foreground">Kode: <span class="font-mono text-primary font-semibold">{{ $location->code }}</span></p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a
                    href="{{ route('location-and-category.locations.edit', $location) }}"
                    class="inline-flex items-center gap-1.5 rounded-[6px] h-[38px] px-4 text-sm font-medium bg-primary text-primary-foreground transition-all hover:bg-primary-hover hover:shadow-[0_4px_12px_rgba(99,102,241,0.35)] hover:-translate-y-px active:translate-y-0"
                >
                    <x-lucide-pencil class="h-3.5 w-3.5" />
                    <span>Ubah Lokasi</span>
                </a>
                <form
                    action="{{ route('location-and-category.locations.destroy', $location) }}"
                    method="POST"
                    class="inline-block"
                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus atau menonaktifkan lokasi ini?')"
                >
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        class="inline-flex items-center gap-1.5 rounded-[6px] border border-error/20 bg-error/10 h-[38px] px-4 text-sm font-medium text-error transition-all hover:bg-error/20 hover:-translate-y-px cursor-pointer"
                    >
                        <x-lucide-trash-2 class="h-3.5 w-3.5" />
                        <span>Hapus</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- Detail Card --}}
        <div class="rounded-xl border border-border bg-background p-6 space-y-6">
            <div class="border-b border-border pb-3">
                <h3 class="text-sm font-semibold text-foreground flex items-center gap-2">
                    <x-lucide-map-pin class="h-4 w-4 text-primary" />
                    <span>Informasi Spesifikasi Kawasan</span>
                </h3>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <span class="text-xs font-medium text-muted-foreground">Kode Kawasan</span>
                    <p class="mt-1 font-mono text-sm font-semibold text-primary">{{ $location->code }}</p>
                </div>

                <div>
                    <span class="text-xs font-medium text-muted-foreground">Nama Kawasan</span>
                    <p class="mt-1 text-sm font-semibold text-foreground">{{ $location->name }}</p>
                </div>

                <div>
                    <span class="text-xs font-medium text-muted-foreground">Wilayah / Region</span>
                    <p class="mt-1 text-xs text-foreground">{{ $location->region }}</p>
                </div>

                <div>
                    <span class="text-xs font-medium text-muted-foreground">Induk Kawasan</span>
                    <div class="mt-1">
                        @if ($location->parent)
                            <a href="{{ route('location-and-category.locations.show', $location->parent) }}" class="inline-flex items-center gap-1 font-medium text-primary hover:underline text-xs">
                                <x-lucide-corner-down-right class="h-3.5 w-3.5" />
                                <span>{{ $location->parent->name }} ({{ $location->parent->code }})</span>
                            </a>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-[4px] border border-border bg-secondary px-2 py-0.5 text-[11px] font-medium text-muted-foreground">
                                Lokasi Induk Utama (Root)
                            </span>
                        @endif
                    </div>
                </div>

                <div>
                    <span class="text-xs font-medium text-muted-foreground">Status Keaktifan</span>
                    <div class="mt-1">
                        @if ($location->is_active)
                            <span class="inline-flex items-center gap-1 rounded-full border border-success/20 bg-success/10 px-2.5 py-0.5 text-xs font-medium text-success">
                                <span class="h-1.5 w-1.5 rounded-full bg-success"></span>
                                Kawasan Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full border border-error/20 bg-error/10 px-2.5 py-0.5 text-xs font-medium text-error">
                                <span class="h-1.5 w-1.5 rounded-full bg-error"></span>
                                Kawasan Nonaktif
                            </span>
                        @endif
                    </div>
                </div>

                <div>
                    <span class="text-xs font-medium text-muted-foreground">Dibuat Pada</span>
                    <p class="mt-1 text-xs text-foreground">{{ $location->created_at?->format('d M Y, H:i') ?? '-' }}</p>
                </div>
            </div>

            <div>
                <span class="text-xs font-medium text-muted-foreground">Alamat Lengkap</span>
                <p class="mt-1 text-xs text-foreground leading-relaxed bg-secondary/40 p-3 rounded-[8px] border border-border">{{ $location->address }}</p>
            </div>

            @if($location->description)
                <div>
                    <span class="text-xs font-medium text-muted-foreground">Deskripsi / Catatan</span>
                    <p class="mt-1 text-xs text-foreground leading-relaxed">{{ $location->description }}</p>
                </div>
            @endif
        </div>

        {{-- Relasi --}}
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            {{-- Sub-lokasi --}}
            <div class="rounded-xl border border-border bg-background p-5 space-y-3">
                <div class="flex items-center justify-between border-b border-border pb-2">
                    <h4 class="text-xs font-semibold text-foreground flex items-center gap-2">
                        <x-lucide-layers class="h-4 w-4 text-primary" />
                        <span>Sub-Kawasan / Anak Lokasi ({{ $location->children->count() }})</span>
                    </h4>
                </div>
                <div class="space-y-2">
                    @forelse($location->children as $child)
                        <div class="flex items-center justify-between p-2.5 rounded-[8px] border border-border bg-secondary/20 text-xs">
                            <div>
                                <a href="{{ route('location-and-category.locations.show', $child) }}" class="font-medium text-foreground hover:text-primary transition-colors">
                                    {{ $child->name }}
                                </a>
                                <span class="font-mono text-[11px] text-muted-foreground block">{{ $child->code }}</span>
                            </div>
                            <span class="text-[11px] text-muted-foreground">{{ $child->region }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-muted-foreground py-2 text-center">Tidak ada sub-kawasan terdaftar.</p>
                    @endforelse
                </div>
            </div>

            {{-- Gedung --}}
            <div class="rounded-xl border border-border bg-background p-5 space-y-3">
                <div class="flex items-center justify-between border-b border-border pb-2">
                    <h4 class="text-xs font-semibold text-foreground flex items-center gap-2">
                        <x-lucide-building class="h-4 w-4 text-primary" />
                        <span>Gedung di Kawasan Ini ({{ $location->buildings->count() }})</span>
                    </h4>
                </div>
                <div class="space-y-2">
                    @forelse($location->buildings as $bld)
                        <div class="flex items-center justify-between p-2.5 rounded-[8px] border border-border bg-secondary/20 text-xs">
                            <div>
                                <span class="font-medium text-foreground">{{ $bld->name }}</span>
                                <span class="font-mono text-[11px] text-muted-foreground block">{{ $bld->code }}</span>
                            </div>
                            <span class="text-[11px] text-muted-foreground">{{ $bld->total_floors }} Lantai</span>
                        </div>
                    @empty
                        <p class="text-xs text-muted-foreground py-2 text-center">Belum ada gedung terdaftar di kawasan ini.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</x-layouts.dashboard>
