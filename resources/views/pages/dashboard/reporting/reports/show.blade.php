<x-layouts.dashboard title="Detail Laporan #{{ $report->ticket_number }}">
    <div class="space-y-6">
        <!-- Back Navigation & Title -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <a
                    href="{{ route('reporting.reports.index') }}"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-border bg-background text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                    title="Kembali ke Daftar Laporan"
                >
                    <x-lucide-arrow-left class="h-4 w-4" />
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-sm font-bold text-primary">{{ $report->ticket_number }}</span>
                        @php
                            $statusStyles = $report->status instanceof \App\Enums\ReportStatus ? $report->status->styles() : [
                                'badge' => 'border-border bg-secondary text-muted-foreground',
                                'dot' => 'bg-muted-foreground',
                            ];
                        @endphp
                        <span class="inline-flex items-center gap-1.5 rounded-full border {{ $statusStyles['badge'] }} px-2.5 py-0.5 text-[11px] font-semibold">
                            <span class="h-1.5 w-1.5 rounded-full {{ $statusStyles['dot'] }}"></span>
                            <span>{{ $report->status instanceof \App\Enums\ReportStatus ? $report->status->label() : ucfirst($report->status) }}</span>
                        </span>
                    </div>
                    <h1 class="text-lg font-bold tracking-tight text-foreground mt-0.5">{{ $report->title }}</h1>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- Verifikasi Modal Trigger (Supervisor & Admin saat status Pending) -->
                @if ($report->status === \App\Enums\ReportStatus::Pending && (auth()->user()?->hasRole(\App\Enums\UserRole::Supervisor) || auth()->user()?->hasRole(\App\Enums\UserRole::Admin)))
                    <x-reporting-reports::report-verification-modal
                        :report="$report"
                        :priorities="$priorities"
                    />
                @endif

                <!-- Edit Button (Reporter or Admin saat status Pending) -->
                @if ($report->status === \App\Enums\ReportStatus::Pending && (auth()->id() === $report->reporter_id || auth()->user()?->hasRole(\App\Enums\UserRole::Admin)))
                    <a
                        href="{{ route('reporting.reports.edit', $report) }}"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-background px-3 py-2 text-xs font-semibold text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                    >
                        <x-lucide-pencil class="h-3.5 w-3.5" />
                        <span>Ubah</span>
                    </a>

                    <form
                        action="{{ route('reporting.reports.destroy', $report) }}"
                        method="POST"
                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini?');"
                        class="inline-block"
                    >
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-red-500/20 bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-red-500/20 transition-colors cursor-pointer"
                        >
                            <x-lucide-trash-2 class="h-3.5 w-3.5" />
                            <span>Hapus</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session('success') || session('status'))
            <div class="flex items-center gap-2 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-xs font-medium text-emerald-600 dark:text-emerald-400 shadow-2xs">
                <x-lucide-check-circle-2 class="h-4 w-4 shrink-0" />
                <span>{{ session('success') ?? session('status') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-center gap-2 rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-xs font-medium text-red-600 dark:text-red-400 shadow-2xs">
                <x-lucide-alert-circle class="h-4 w-4 shrink-0" />
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Left 2 Cols: Details -->
            <div class="space-y-6 lg:col-span-2">
                <!-- Deskripsi Kerusakan Card -->
                <div class="rounded-xl border border-border bg-background p-6 shadow-2xs space-y-4">
                    <h2 class="text-xs font-bold text-muted-foreground uppercase tracking-wider">
                        Rincian Masalah & Deskripsi Kerusakan
                    </h2>
                    <div class="rounded-lg bg-secondary/30 p-4 border border-border/50 text-xs text-foreground leading-relaxed whitespace-pre-line">
                        {{ $report->description }}
                    </div>

                    @if ($report->status === \App\Enums\ReportStatus::Rejected && $report->rejection_reason)
                        <div class="rounded-xl border border-red-500/30 bg-red-500/10 p-4 text-xs text-red-700 dark:text-red-300 space-y-1">
                            <span class="font-bold flex items-center gap-1.5">
                                <x-lucide-alert-triangle class="h-4 w-4" />
                                <span>Alasan Penolakan oleh Supervisor:</span>
                            </span>
                            <p class="leading-relaxed">{{ $report->rejection_reason }}</p>
                        </div>
                    @endif
                </div>

                <!-- Informasi Aset & Fasilitas Card -->
                <div class="rounded-xl border border-border bg-background p-6 shadow-2xs space-y-4">
                    <h2 class="text-xs font-bold text-muted-foreground uppercase tracking-wider">
                        Aset Fasilitas & Lokasi Terkait
                    </h2>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 text-xs">
                        <!-- Fasilitas -->
                        <div class="rounded-lg border border-border p-3.5 space-y-1">
                            <span class="text-[11px] text-muted-foreground block">Fasilitas / Aset</span>
                            <span class="font-bold text-foreground block">{{ $report->facility?->name ?? '-' }}</span>
                            <span class="font-mono text-[10px] text-muted-foreground block">Kode: {{ $report->facility?->code ?? '-' }}</span>
                        </div>

                        <!-- Kategori Kerusakan -->
                        <div class="rounded-lg border border-border p-3.5 space-y-1">
                            <span class="text-[11px] text-muted-foreground block">Kategori Kerusakan</span>
                            <span class="font-bold text-foreground block">{{ $report->damageCategory?->name ?? '-' }}</span>
                            <span class="text-[10px] text-muted-foreground block">Klasifikasi pelaporan</span>
                        </div>

                        <!-- Gedung & Ruangan -->
                        <div class="rounded-lg border border-border p-3.5 space-y-1">
                            <span class="text-[11px] text-muted-foreground block">Gedung & Ruangan</span>
                            <span class="font-bold text-foreground block">{{ $report->building?->name ?? '-' }}</span>
                            <span class="text-[10px] text-muted-foreground block">
                                {{ $report->room ? 'Ruang: ' . $report->room->name : 'Area Umum / Tanpa Ruangan' }}
                            </span>
                        </div>

                        <!-- Wilayah / Area -->
                        <div class="rounded-lg border border-border p-3.5 space-y-1">
                            <span class="text-[11px] text-muted-foreground block">Lokasi / Kampus</span>
                            <span class="font-bold text-foreground block">{{ $report->location?->name ?? '-' }}</span>
                            <span class="text-[10px] text-muted-foreground block">{{ $report->location?->code ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Parameter SLA Card -->
                <div class="rounded-xl border border-border bg-background p-6 shadow-2xs space-y-4">
                    <h2 class="text-xs font-bold text-muted-foreground uppercase tracking-wider">
                        Target Service Level Agreement (SLA)
                    </h2>

                    @php
                        $priorityBadgeStyles = match ($report->priority?->badge_variant) {
                            'danger' => 'border-red-500/20 bg-red-500/10 text-red-600 dark:text-red-400',
                            'warning' => 'border-amber-500/20 bg-amber-500/10 text-amber-600 dark:text-amber-400',
                            'info' => 'border-blue-500/20 bg-blue-500/10 text-blue-600 dark:text-blue-400',
                            'emerald', 'success' => 'border-emerald-500/20 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
                            default => 'border-border bg-secondary text-muted-foreground',
                        };
                    @endphp

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 text-xs">
                        <div class="rounded-lg border border-border p-3.5 space-y-1">
                            <span class="text-[11px] text-muted-foreground block">Prioritas Laporan</span>
                            <span class="inline-flex items-center gap-1 rounded-full border {{ $priorityBadgeStyles }} px-2.5 py-0.5 text-xs font-bold uppercase tracking-wider">
                                {{ $report->priority?->name ?? '-' }}
                            </span>
                        </div>

                        <div class="rounded-lg border border-border p-3.5 space-y-1">
                            <span class="text-[11px] text-muted-foreground block">Target Respon Awal</span>
                            <div class="flex items-center gap-1.5 font-bold text-foreground">
                                <x-lucide-clock class="h-4 w-4 text-primary" />
                                <span>{{ $report->priority?->sla_response_hours ?? 0 }} Jam</span>
                            </div>
                        </div>

                        <div class="rounded-lg border border-border p-3.5 space-y-1">
                            <span class="text-[11px] text-muted-foreground block">Target Penyelesaian</span>
                            <div class="flex items-center gap-1.5 font-bold text-foreground">
                                <x-lucide-timer class="h-4 w-4 text-emerald-600 dark:text-emerald-400" />
                                <span>{{ $report->priority?->sla_resolution_hours ?? 0 }} Jam</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Timeline & User Details -->
            <div class="space-y-6">
                <!-- Status Timeline Component -->
                <x-reporting-reports::report-status-timeline :report="$report" />

                <!-- Card Pihak Terkait -->
                <div class="rounded-xl border border-border bg-background p-5 shadow-2xs space-y-4">
                    <h3 class="text-xs font-bold text-foreground uppercase tracking-wider">
                        Pihak Terkait
                    </h3>

                    <div class="space-y-3 text-xs">
                        <!-- Pelapor -->
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-secondary flex items-center justify-center text-muted-foreground font-bold">
                                {{ substr($report->reporter?->name ?? 'A', 0, 1) }}
                            </div>
                            <div class="space-y-0.5">
                                <span class="font-semibold text-foreground block">{{ $report->reporter?->name ?? 'Anonim' }}</span>
                                <span class="text-[11px] text-muted-foreground block">Pelapor &bull; {{ $report->reporter?->email }}</span>
                            </div>
                        </div>

                        <!-- Verifikator jika ada -->
                        @if ($report->verifiedBy)
                            <div class="pt-3 border-t border-border flex items-center gap-3">
                                <div class="h-8 w-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                                    {{ substr($report->verifiedBy->name, 0, 1) }}
                                </div>
                                <div class="space-y-0.5">
                                    <span class="font-semibold text-foreground block">{{ $report->verifiedBy->name }}</span>
                                    <span class="text-[11px] text-muted-foreground block">
                                        {{ $report->status === \App\Enums\ReportStatus::Rejected ? 'Menolak Verifikasi' : 'Supervisor Verifikator' }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.dashboard>

