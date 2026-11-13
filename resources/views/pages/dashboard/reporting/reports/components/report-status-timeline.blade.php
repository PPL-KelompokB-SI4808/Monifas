@props([
    'report',
])

@php
    $status = $report->status instanceof \App\Enums\ReportStatus ? $report->status : \App\Enums\ReportStatus::tryFrom((string) $report->status);
    $isPending = $status === \App\Enums\ReportStatus::Pending;
    $isVerified = in_array($status, [\App\Enums\ReportStatus::Verified, \App\Enums\ReportStatus::Assigned, \App\Enums\ReportStatus::InProgress, \App\Enums\ReportStatus::Completed], true);
    $isRejected = $status === \App\Enums\ReportStatus::Rejected;
    $isAssigned = in_array($status, [\App\Enums\ReportStatus::Assigned, \App\Enums\ReportStatus::InProgress, \App\Enums\ReportStatus::Completed], true);
    $isInProgress = in_array($status, [\App\Enums\ReportStatus::InProgress, \App\Enums\ReportStatus::Completed], true);
    $isCompleted = $status === \App\Enums\ReportStatus::Completed;
@endphp

<div class="rounded-xl border border-border bg-background p-5 shadow-2xs space-y-4">
    <div class="flex items-center justify-between border-b border-border pb-3">
        <h3 class="text-xs font-bold text-foreground uppercase tracking-wider flex items-center gap-2">
            <x-lucide-git-commit class="h-4 w-4 text-primary" />
            <span>Riwayat & Siklus Status Laporan</span>
        </h3>
        <span class="text-[11px] font-mono text-muted-foreground">{{ $report->ticket_number }}</span>
    </div>

    <div class="relative pl-6 space-y-6 before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-border">
        <!-- Tahap 1: Laporan Dibuat -->
        <div class="relative">
            <div class="absolute -left-6 top-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500 text-white shadow-2xs">
                <x-lucide-check class="h-3 w-3" />
            </div>
            <div class="space-y-0.5">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-foreground">Laporan Diajukan</span>
                    <span class="text-[10px] text-muted-foreground">{{ $report->created_at?->translatedFormat('d M Y, H:i') }}</span>
                </div>
                <p class="text-[11px] text-muted-foreground">
                    Dilaporkan oleh <strong class="text-foreground font-medium">{{ $report->reporter?->name ?? 'Anonim' }}</strong> melalui portal pelaporan fasilitas.
                </p>
            </div>
        </div>

        <!-- Tahap 2: Verifikasi Supervisor -->
        <div class="relative">
            @if ($isRejected)
                <div class="absolute -left-6 top-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-white shadow-2xs">
                    <x-lucide-x class="h-3 w-3" />
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-red-600 dark:text-red-400">Laporan Ditolak</span>
                        <span class="text-[10px] text-muted-foreground">{{ $report->verified_at?->translatedFormat('d M Y, H:i') }}</span>
                    </div>
                    <p class="text-[11px] text-muted-foreground">
                        Diverifikasi dan ditolak oleh <strong class="text-foreground font-medium">{{ $report->verifiedBy?->name ?? 'Supervisor' }}</strong>.
                    </p>
                    @if ($report->rejection_reason)
                        <div class="rounded-lg border border-red-500/20 bg-red-500/10 p-2.5 text-[11px] text-red-700 dark:text-red-300">
                            <strong>Alasan Penolakan:</strong> {{ $report->rejection_reason }}
                        </div>
                    @endif
                </div>
            @elseif ($isVerified)
                <div class="absolute -left-6 top-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-blue-500 text-white shadow-2xs">
                    <x-lucide-check class="h-3 w-3" />
                </div>
                <div class="space-y-0.5">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-blue-600 dark:text-blue-400">Laporan Diverifikasi</span>
                        <span class="text-[10px] text-muted-foreground">{{ $report->verified_at?->translatedFormat('d M Y, H:i') }}</span>
                    </div>
                    <p class="text-[11px] text-muted-foreground">
                        Diverifikasi dan disetujui oleh <strong class="text-foreground font-medium">{{ $report->verifiedBy?->name ?? 'Supervisor' }}</strong>.
                    </p>
                </div>
            @else
                <div class="absolute -left-6 top-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-amber-500 text-white shadow-2xs animate-pulse">
                    <x-lucide-clock class="h-3 w-3" />
                </div>
                <div class="space-y-0.5">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-amber-600 dark:text-amber-400">Menunggu Verifikasi</span>
                        <span class="inline-flex items-center rounded-full bg-amber-500/10 px-1.5 py-0.5 text-[10px] font-semibold text-amber-600 dark:text-amber-400">Saat Ini</span>
                    </div>
                    <p class="text-[11px] text-muted-foreground">
                        Sedang dalam antrean peninjauan oleh Supervisor Fasilitas.
                    </p>
                </div>
            @endif
        </div>

        @if (! $isRejected)
            <!-- Tahap 3: Penugasan Teknisi -->
            <div class="relative">
                <div class="absolute -left-6 top-0.5 flex h-5 w-5 items-center justify-center rounded-full {{ $isAssigned ? 'bg-purple-500 text-white' : 'bg-secondary text-muted-foreground border border-border' }}">
                    <x-lucide-user-check class="h-3 w-3" />
                </div>
                <div class="space-y-0.5">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold {{ $isAssigned ? 'text-foreground' : 'text-muted-foreground' }}">Penugasan Teknisi</span>
                        @if ($status === \App\Enums\ReportStatus::Assigned)
                            <span class="inline-flex items-center rounded-full bg-purple-500/10 px-1.5 py-0.5 text-[10px] font-semibold text-purple-600 dark:text-purple-400">Saat Ini</span>
                        @endif
                    </div>
                    <p class="text-[11px] text-muted-foreground">
                        {{ $isAssigned ? 'Laporan telah dialokasikan ke petugas teknisi untuk jadwal pengerjaan.' : 'Menunggu verifikasi selesai untuk dapat ditugaskan.' }}
                    </p>
                </div>
            </div>

            <!-- Tahap 4: Pelaksanaan Perbaikan -->
            <div class="relative">
                <div class="absolute -left-6 top-0.5 flex h-5 w-5 items-center justify-center rounded-full {{ $isInProgress ? 'bg-sky-500 text-white' : 'bg-secondary text-muted-foreground border border-border' }}">
                    <x-lucide-wrench class="h-3 w-3" />
                </div>
                <div class="space-y-0.5">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold {{ $isInProgress ? 'text-foreground' : 'text-muted-foreground' }}">Proses Pengerjaan Perbaikan</span>
                        @if ($status === \App\Enums\ReportStatus::InProgress)
                            <span class="inline-flex items-center rounded-full bg-sky-500/10 px-1.5 py-0.5 text-[10px] font-semibold text-sky-600 dark:text-sky-400">Sedang Dikerjakan</span>
                        @endif
                    </div>
                    <p class="text-[11px] text-muted-foreground">
                        {{ $isInProgress ? 'Petugas teknisi sedang melakukan tindakan perbaikan di lokasi.' : 'Menunggu penugasan teknisi.' }}
                    </p>
                </div>
            </div>

            <!-- Tahap 5: Selesai -->
            <div class="relative">
                <div class="absolute -left-6 top-0.5 flex h-5 w-5 items-center justify-center rounded-full {{ $isCompleted ? 'bg-emerald-600 text-white' : 'bg-secondary text-muted-foreground border border-border' }}">
                    <x-lucide-check-check class="h-3 w-3" />
                </div>
                <div class="space-y-0.5">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold {{ $isCompleted ? 'text-emerald-600 dark:text-emerald-400' : 'text-muted-foreground' }}">Perbaikan Selesai</span>
                        @if ($report->completed_at)
                            <span class="text-[10px] text-muted-foreground">{{ $report->completed_at->translatedFormat('d M Y, H:i') }}</span>
                        @endif
                    </div>
                    <p class="text-[11px] text-muted-foreground">
                        {{ $isCompleted ? 'Fasilitas telah diperbaiki dan diverifikasi berfungsi kembali normal.' : 'Pekerjaan perbaikan belum rampung.' }}
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>

