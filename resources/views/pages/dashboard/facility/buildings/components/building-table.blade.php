@props(['buildings'])

<div class="overflow-hidden rounded-xl border border-border bg-background shadow-2xs">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-border text-left text-xs">
            <thead class="bg-secondary/50 text-[11px] font-semibold text-muted-foreground uppercase tracking-wider">
                <tr>
                    <th scope="col" class="px-4 py-3">Kode</th>
                    <th scope="col" class="px-4 py-3">Nama Gedung</th>
                    <th scope="col" class="px-4 py-3">Lokasi / Kawasan</th>
                    <th scope="col" class="px-4 py-3">Zona Area</th>
                    <th scope="col" class="px-4 py-3">Total Lantai</th>
                    <th scope="col" class="px-4 py-3">Status</th>
                    <th scope="col" class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border bg-background">
                @forelse($buildings as $building)
                    <tr class="hover:bg-secondary/30 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-primary">
                            {{ $building->code }}
                        </td>
                        <td class="px-4 py-3 font-medium text-foreground">
                            {{ $building->name }}
                        </td>
                        <td class="px-4 py-3 text-muted-foreground">
                            {{ $building->location->name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-muted-foreground">
                            {{ $building->areaZone->name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-foreground">
                            {{ $building->total_floors }} Lantai
                        </td>
                        <td class="px-4 py-3">
                            @if ($building->is_active)
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
                            <div class="flex items-center justify-end gap-2">
                                <a
                                    href="{{ route('facility.buildings.show', $building) }}"
                                    class="inline-flex items-center gap-1 rounded-md border border-border bg-background px-2.5 py-1 text-xs font-medium text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                                    title="Lihat Detail"
                                >
                                    <x-lucide-eye class="h-3.5 w-3.5" />
                                    <span>Detail</span>
                                </a>

                                <a
                                    href="{{ route('facility.buildings.edit', $building) }}"
                                    class="inline-flex items-center gap-1 rounded-md border border-border bg-background px-2.5 py-1 text-xs font-medium text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                                    title="Ubah Gedung"
                                >
                                    <x-lucide-pencil class="h-3.5 w-3.5" />
                                    <span>Ubah</span>
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
                                        class="inline-flex items-center gap-1 rounded-md border border-red-500/20 bg-red-500/10 px-2.5 py-1 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-500/20 transition-colors cursor-pointer"
                                        title="Hapus / Nonaktifkan"
                                    >
                                        <x-lucide-trash-2 class="h-3.5 w-3.5" />
                                        <span>Hapus</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <div class="rounded-full bg-secondary p-3 text-muted-foreground">
                                    <x-lucide-building-2 class="h-6 w-6" />
                                </div>
                                <p class="text-xs font-medium text-foreground">Belum ada data gedung</p>
                                <p class="text-[11px] text-muted-foreground">Tambahkan data gedung baru untuk memulai inventaris fasilitas.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($buildings->hasPages())
        <div class="border-t border-border px-4 py-3">
            {{ $buildings->links() }}
        </div>
    @endif
</div>
