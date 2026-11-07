@props(['locations'])

<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-xl border border-border bg-background shadow-2xs']) }}>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-border text-left text-xs">
            <thead class="bg-secondary/50 text-[11px] font-semibold text-muted-foreground uppercase tracking-wider">
                <tr>
                    <th scope="col" class="px-4 py-3">Kode</th>
                    <th scope="col" class="px-4 py-3">Nama Lokasi</th>
                    <th scope="col" class="px-4 py-3">Wilayah</th>
                    <th scope="col" class="px-4 py-3">Induk Kawasan</th>
                    <th scope="col" class="px-4 py-3">Keterkaitan</th>
                    <th scope="col" class="px-4 py-3">Status</th>
                    <th scope="col" class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border bg-background">
                @forelse($locations as $location)
                    <tr class="hover:bg-secondary/30 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-primary">
                            {{ $location->code }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-foreground">{{ $location->name }}</div>
                            @if ($location->address)
                                <div class="text-[11px] text-muted-foreground line-clamp-1 mt-0.5">{{ $location->address }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-muted-foreground">
                            {{ $location->region }}
                        </td>
                        <td class="px-4 py-3">
                            @if($location->parent)
                                <span class="inline-flex items-center gap-1 rounded-md border border-border bg-secondary/50 px-2 py-0.5 text-[11px] font-medium text-foreground">
                                    <x-lucide-corner-down-right class="h-3 w-3 text-muted-foreground" />
                                    <span>{{ $location->parent->name }}</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-md border border-border bg-secondary px-2 py-0.5 text-[11px] font-medium text-muted-foreground">
                                    <x-lucide-map-pin class="h-3 w-3 text-primary" />
                                    <span>Induk Utama</span>
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2 text-[11px] text-muted-foreground">
                                <span title="Jumlah Sub-lokasi" class="inline-flex items-center gap-1">
                                    <x-lucide-layers class="h-3 w-3" />
                                    {{ $location->children_count ?? $location->children->count() }} Sub
                                </span>
                                <span>•</span>
                                <span title="Jumlah Gedung" class="inline-flex items-center gap-1">
                                    <x-lucide-building class="h-3 w-3" />
                                    {{ $location->buildings_count ?? $location->buildings->count() }} Gedung
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if ($location->is_active)
                                <span class="inline-flex items-center gap-1 rounded-md border border-emerald-500/20 bg-emerald-500/10 px-2 py-0.5 text-[11px] font-medium text-emerald-600 dark:text-emerald-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Aktif</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-md border border-red-500/20 bg-red-500/10 px-2 py-0.5 text-[11px] font-medium text-red-600 dark:text-red-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                    <span>Nonaktif</span>
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a
                                    href="{{ route('location-and-category.locations.show', $location) }}"
                                    class="inline-flex items-center gap-1 rounded-md border border-border bg-background px-2 py-1 text-xs font-medium text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                                    title="Lihat Detail"
                                >
                                    <x-lucide-eye class="h-3.5 w-3.5" />
                                    <span class="sr-only sm:not-sr-only">Detail</span>
                                </a>

                                <a
                                    href="{{ route('location-and-category.locations.edit', $location) }}"
                                    class="inline-flex items-center gap-1 rounded-md border border-border bg-background px-2 py-1 text-xs font-medium text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                                    title="Ubah Data"
                                >
                                    <x-lucide-pencil class="h-3.5 w-3.5" />
                                    <span class="sr-only sm:not-sr-only">Ubah</span>
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
                                        class="inline-flex items-center gap-1 rounded-md border border-red-500/20 bg-red-500/10 px-2 py-1 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-500/20 transition-colors cursor-pointer"
                                        title="Hapus Lokasi"
                                    >
                                        <x-lucide-trash-2 class="h-3.5 w-3.5" />
                                        <span class="sr-only sm:not-sr-only">Hapus</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-xs text-muted-foreground">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <x-lucide-map-pin-off class="h-8 w-8 text-muted-foreground/50" />
                                <p class="font-medium text-foreground">Belum ada data lokasi yang ditemukan</p>
                                <p class="text-[11px] text-muted-foreground">Silakan sesuaikan kata kunci pencarian atau tambahkan lokasi baru.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($locations->hasPages())
        <div class="border-t border-border px-4 py-3">
            {{ $locations->links() }}
        </div>
    @endif
</div>
