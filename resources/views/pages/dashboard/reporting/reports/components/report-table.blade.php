@props([
    'reports',
])

<div class="overflow-hidden rounded-xl border border-border bg-background shadow-2xs">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="border-b border-border bg-secondary/50 text-[11px] font-semibold text-muted-foreground uppercase tracking-wider">
                <tr>
                    <th scope="col" class="px-4 py-3">Tiket & Judul</th>
                    <th scope="col" class="px-4 py-3">Lokasi / Fasilitas</th>
                    <th scope="col" class="px-4 py-3">Kategori</th>
                    <th scope="col" class="px-4 py-3">Prioritas SLA</th>
                    <th scope="col" class="px-4 py-3">Pelapor</th>
                    <th scope="col" class="px-4 py-3">Status</th>
                    <th scope="col" class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($reports as $report)
                    @php
                        $statusStyles = $report->status instanceof \App\Enums\ReportStatus ? $report->status->styles() : [
                            'badge' => 'border-border bg-secondary text-muted-foreground',
                            'dot' => 'bg-muted-foreground',
                        ];
                        $statusIcon = $report->status instanceof \App\Enums\ReportStatus ? $report->status->icon() : 'lucide-clock';
                        $priorityBadgeStyles = match ($report->priority?->badge_variant) {
                            'danger' => 'border-red-500/20 bg-red-500/10 text-red-600 dark:text-red-400',
                            'warning' => 'border-amber-500/20 bg-amber-500/10 text-amber-600 dark:text-amber-400',
                            'info' => 'border-blue-500/20 bg-blue-500/10 text-blue-600 dark:text-blue-400',
                            'emerald', 'success' => 'border-emerald-500/20 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
                            default => 'border-border bg-secondary text-muted-foreground',
                        };
                    @endphp
                    <tr class="hover:bg-secondary/40 transition-colors">
                        <!-- Tiket & Judul -->
                        <td class="px-4 py-3.5">
                            <div class="space-y-0.5">
                                <a
                                    href="{{ route('reporting.reports.show', $report) }}"
                                    class="font-mono text-xs font-bold text-primary hover:underline block"
                                >
                                    {{ $report->ticket_number }}
                                </a>
                                <div class="font-medium text-foreground line-clamp-1 max-w-xs" title="{{ $report->title }}">
                                    {{ $report->title }}
                                </div>
                                <div class="text-[11px] text-muted-foreground">
                                    {{ $report->created_at?->translatedFormat('d M Y, H:i') }}
                                </div>
                            </div>
                        </td>

                        <!-- Lokasi / Fasilitas -->
                        <td class="px-4 py-3.5">
                            <div class="space-y-0.5 max-w-[200px]">
                                <span class="font-semibold text-foreground block truncate" title="{{ $report->facility?->name }}">
                                    {{ $report->facility?->name ?? 'Fasilitas Tidak Diketahui' }}
                                </span>
                                <div class="text-[11px] text-muted-foreground flex items-center gap-1 truncate">
                                    <x-lucide-building-2 class="h-3 w-3 shrink-0" />
                                    <span>{{ $report->building?->name }}</span>
                                    @if ($report->room)
                                        <span>&bull; {{ $report->room->name }}</span>
                                    @endif
                                </div>
                                <div class="text-[10px] text-muted-foreground truncate">
                                    {{ $report->location?->name }}
                                </div>
                            </div>
                        </td>

                        <!-- Kategori Kerusakan -->
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center gap-1 rounded-md border border-border bg-secondary/60 px-2 py-0.5 text-[11px] font-medium text-foreground">
                                <x-lucide-tag class="h-3 w-3 text-muted-foreground shrink-0" />
                                <span>{{ $report->damageCategory?->name ?? '-' }}</span>
                            </span>
                        </td>

                        <!-- Prioritas SLA -->
                        <td class="px-4 py-3.5">
                            <div class="space-y-1">
                                <span class="inline-flex items-center gap-1 rounded-full border {{ $priorityBadgeStyles }} px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider">
                                    <span>{{ $report->priority?->name ?? '-' }}</span>
                                </span>
                                @if ($report->priority)
                                    <div class="text-[10px] text-muted-foreground flex items-center gap-1">
                                        <x-lucide-timer class="h-3 w-3" />
                                        <span>Maks. {{ $report->priority->sla_resolution_hours }} jam</span>
                                    </div>
                                @endif
                            </div>
                        </td>

                        <!-- Pelapor -->
                        <td class="px-4 py-3.5">
                            <div class="space-y-0.5">
                                <span class="font-medium text-foreground block">{{ $report->reporter?->name ?? 'Anonim' }}</span>
                                <span class="text-[11px] text-muted-foreground block truncate max-w-[150px]">{{ $report->reporter?->email }}</span>
                            </div>
                        </td>

                        <!-- Status -->
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center gap-1.5 rounded-full border {{ $statusStyles['badge'] }} px-2.5 py-0.5 text-[11px] font-semibold">
                                <span class="h-1.5 w-1.5 rounded-full {{ $statusStyles['dot'] }}"></span>
                                <span>{{ $report->status instanceof \App\Enums\ReportStatus ? $report->status->label() : ucfirst($report->status) }}</span>
                            </span>
                        </td>

                        <!-- Aksi -->
                        <td class="px-4 py-3.5 text-right">
                            <div class="inline-flex items-center justify-end gap-1.5">
                                <a
                                    href="{{ route('reporting.reports.show', $report) }}"
                                    class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-border bg-background text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                                    title="Lihat Detail Laporan"
                                >
                                    <x-lucide-eye class="h-3.5 w-3.5" />
                                </a>

                                @if ($report->status === \App\Enums\ReportStatus::Pending && (auth()->id() === $report->reporter_id || auth()->user()?->hasRole(\App\Enums\UserRole::Admin)))
                                    <a
                                        href="{{ route('reporting.reports.edit', $report) }}"
                                        class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-border bg-background text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                                        title="Ubah Laporan"
                                    >
                                        <x-lucide-pencil class="h-3.5 w-3.5" />
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center justify-center space-y-2">
                                <div class="h-10 w-10 rounded-full bg-secondary flex items-center justify-center text-muted-foreground">
                                    <x-lucide-inbox class="h-5 w-5" />
                                </div>
                                <h3 class="text-xs font-semibold text-foreground">Tidak Ada Laporan Kerusakan</h3>
                                <p class="text-[11px] text-muted-foreground max-w-sm">
                                    Belum ada data laporan yang sesuai dengan kriteria filter saat ini.
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($reports->hasPages())
        <div class="border-t border-border px-4 py-3 bg-secondary/20">
            {{ $reports->links() }}
        </div>
    @endif
</div>

