@props(['buildings'])

<div class="overflow-hidden rounded-xl border border-border bg-background">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-border text-left text-xs">
            <thead>
                <tr class="bg-secondary/40">
                    <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Kode</th>
                    <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Nama Gedung</th>
                    <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Lokasi</th>
                    <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Zona</th>
                    <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Lantai</th>
                    <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Status</th>
                    <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border bg-background">
                @forelse($buildings as $building)
                    <tr class="transition-colors hover:bg-secondary/30">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-primary">
                            {{ $building->code }}
                        </td>
                        <td class="px-4 py-3 font-medium text-foreground">
                            {{ $building->name }}
                        </td>
                        <td class="px-4 py-3 text-muted-foreground">
                            {{ $building->location->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-muted-foreground">
                            {{ $building->areaZone->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-foreground">
                            {{ $building->total_floors }}
                        </td>
                        <td class="px-4 py-3">
                            @if ($building->is_active)
                                <span class="inline-flex items-center gap-1 rounded-full border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-0.5 text-[11px] font-medium text-emerald-600">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full border border-border bg-secondary px-2.5 py-0.5 text-[11px] font-medium text-muted-foreground">
                                    <span class="h-1.5 w-1.5 rounded-full bg-muted-foreground/50"></span>
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex items-center gap-1">
                                <a href="{{ route('facility.buildings.show', $building) }}"
                                   title="Lihat detail"
                                   class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-border bg-background text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground">
                                    <x-lucide-eye class="h-3.5 w-3.5" />
                                </a>
                                <a href="{{ route('facility.buildings.edit', $building) }}"
                                   title="Ubah"
                                   class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-border bg-background text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground">
                                    <x-lucide-pencil class="h-3.5 w-3.5" />
                                </a>
                                <form action="{{ route('facility.buildings.destroy', $building) }}"
                                      method="POST"
                                      class="inline-block"
                                      onsubmit="return confirm('Yakin ingin menghapus gedung ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            title="Hapus"
                                            class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-red-500/20 bg-red-500/10 text-red-600 transition-colors hover:bg-red-500/20 cursor-pointer">
                                        <x-lucide-trash-2 class="h-3.5 w-3.5" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-secondary text-muted-foreground">
                                    <x-lucide-building-2 class="h-5 w-5" />
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-foreground">Belum ada data gedung</p>
                                    <p class="mt-0.5 text-xs text-muted-foreground">Tambahkan gedung baru untuk memulai inventaris fasilitas.</p>
                                </div>
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
