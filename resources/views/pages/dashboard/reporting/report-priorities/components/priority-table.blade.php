@props(['priorities'])

<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-xl border border-border bg-background shadow-2xs']) }}>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-border text-left text-xs">
            <thead class="bg-secondary/50 text-[11px] font-semibold text-muted-foreground uppercase tracking-wider">
                <tr>
                    <th scope="col" class="px-4 py-3">Urgensi</th>
                    <th scope="col" class="px-4 py-3">Tingkat & Nama Prioritas</th>
                    <th scope="col" class="px-4 py-3">Target SLA Respon</th>
                    <th scope="col" class="px-4 py-3">Target SLA Penyelesaian</th>
                    <th scope="col" class="px-4 py-3">Badge & Deskripsi</th>
                    <th scope="col" class="px-4 py-3">Status</th>
                    <th scope="col" class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border bg-background">
                @forelse($priorities as $priority)
                    @php
                        $badgeStyles = match ($priority->badge_variant) {
                            'danger' => 'border-red-500/20 bg-red-500/10 text-red-600 dark:text-red-400',
                            'warning' => 'border-amber-500/20 bg-amber-500/10 text-amber-600 dark:text-amber-400',
                            'info' => 'border-blue-500/20 bg-blue-500/10 text-blue-600 dark:text-blue-400',
                            'emerald', 'success' => 'border-emerald-500/20 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
                            default => 'border-border bg-secondary text-muted-foreground',
                        };
                    @endphp
                    <tr class="hover:bg-secondary/30 transition-colors">
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-secondary font-mono text-xs font-bold text-foreground">
                                {{ $priority->sort_order }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-foreground">{{ $priority->name }}</div>
                            <div class="mt-0.5">
                                <span class="inline-flex items-center gap-1 rounded-md border {{ $badgeStyles }} px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider">
                                    {{ $priority->level instanceof \App\Enums\PriorityLevel ? $priority->level->label() : ucfirst($priority->level) }}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-3 font-medium text-foreground">
                            <div class="inline-flex items-center gap-1.5 rounded-lg bg-secondary/60 px-2.5 py-1 text-xs">
                                <x-lucide-clock class="h-3.5 w-3.5 text-primary" />
                                <span>{{ $priority->sla_response_hours }} Jam</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 font-medium text-foreground">
                            <div class="inline-flex items-center gap-1.5 rounded-lg bg-secondary/60 px-2.5 py-1 text-xs">
                                <x-lucide-timer class="h-3.5 w-3.5 text-amber-500" />
                                <span>{{ $priority->sla_resolution_hours }} Jam</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 max-w-xs">
                            <p class="text-xs text-muted-foreground line-clamp-2">
                                {{ $priority->description ?: '-' }}
                            </p>
                        </td>
                        <td class="px-4 py-3">
                            <form action="{{ route('reporting.report-priorities.toggle-status', $priority) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PATCH')
                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-1.5 rounded-md border px-2 py-0.5 text-[11px] font-medium transition-colors cursor-pointer {{ $priority->is_active ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/20' : 'border-red-500/20 bg-red-500/10 text-red-600 dark:text-red-400 hover:bg-red-500/20' }}"
                                    title="Klik untuk mengubah status aktif"
                                >
                                    <span class="h-1.5 w-1.5 rounded-full {{ $priority->is_active ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                    <span>{{ $priority->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a
                                    href="{{ route('reporting.report-priorities.show', $priority) }}"
                                    class="inline-flex items-center gap-1 rounded-md border border-border bg-background px-2 py-1 text-xs font-medium text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                                    title="Lihat Detail Prioritas"
                                >
                                    <x-lucide-eye class="h-3.5 w-3.5" />
                                    <span class="sr-only sm:not-sr-only">Detail</span>
                                </a>

                                <a
                                    href="{{ route('reporting.report-priorities.edit', $priority) }}"
                                    class="inline-flex items-center gap-1 rounded-md border border-border bg-background px-2 py-1 text-xs font-medium text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                                    title="Ubah Data Prioritas"
                                >
                                    <x-lucide-pencil class="h-3.5 w-3.5" />
                                    <span class="sr-only sm:not-sr-only">Ubah</span>
                                </a>

                                <form
                                    action="{{ route('reporting.report-priorities.destroy', $priority) }}"
                                    method="POST"
                                    class="inline-block"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus data master prioritas ini?')"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="inline-flex items-center gap-1 rounded-md border border-red-500/20 bg-red-500/5 px-2 py-1 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-500/15 transition-colors cursor-pointer"
                                        title="Hapus Data"
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
                        <td colspan="7" class="px-4 py-8 text-center">
                            <x-lucide-alert-circle class="mx-auto h-8 w-8 text-muted-foreground/60" />
                            <p class="mt-2 text-xs font-medium text-foreground">Tidak ada data prioritas yang sesuai kriteria pencarian.</p>
                            <p class="text-[11px] text-muted-foreground">Silakan atur ulang filter pencarian Anda.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($priorities->hasPages())
        <div class="border-t border-border px-4 py-3">
            {{ $priorities->links() }}
        </div>
    @endif
</div>

