<x-layouts.dashboard title="Detail Prioritas: {{ $reportPriority->name }}">
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Back Navigation & Title -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a
                    href="{{ route('reporting.report-priorities.index') }}"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-border bg-background text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                    title="Kembali ke Daftar Prioritas"
                >
                    <x-lucide-arrow-left class="h-4 w-4" />
                </a>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-foreground">{{ $reportPriority->name }}</h1>
                    <p class="text-xs text-muted-foreground mt-0.5">Detail konfigurasi parameter prioritas dan Service Level Agreement.</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a
                    href="{{ route('reporting.report-priorities.edit', $reportPriority) }}"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-medium text-primary-foreground shadow-2xs hover:bg-primary/90 transition-colors"
                >
                    <x-lucide-pencil class="h-3.5 w-3.5" />
                    <span>Ubah Data</span>
                </a>
            </div>
        </div>

        @php
            $badgeStyles = match ($reportPriority->badge_variant) {
                'danger' => 'border-red-500/20 bg-red-500/10 text-red-600 dark:text-red-400',
                'warning' => 'border-amber-500/20 bg-amber-500/10 text-amber-600 dark:text-amber-400',
                'info' => 'border-blue-500/20 bg-blue-500/10 text-blue-600 dark:text-blue-400',
                'emerald', 'success' => 'border-emerald-500/20 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
                default => 'border-border bg-secondary text-muted-foreground',
            };
        @endphp

        <!-- Detail Cards -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <!-- Level Card -->
            <div class="rounded-xl border border-border bg-background p-5 shadow-2xs space-y-2">
                <span class="text-[11px] font-semibold text-muted-foreground uppercase tracking-wider block">Tingkat Level</span>
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full border {{ $badgeStyles }} px-3 py-1 text-xs font-bold uppercase tracking-wider">
                        {{ $reportPriority->level instanceof \App\Enums\PriorityLevel ? $reportPriority->level->label() : ucfirst($reportPriority->level) }}
                    </span>
                </div>
                <p class="text-[11px] text-muted-foreground font-mono">Kode: {{ $reportPriority->level instanceof \App\Enums\PriorityLevel ? $reportPriority->level->value : $reportPriority->level }}</p>
            </div>

            <!-- Sort Order Card -->
            <div class="rounded-xl border border-border bg-background p-5 shadow-2xs space-y-2">
                <span class="text-[11px] font-semibold text-muted-foreground uppercase tracking-wider block">Urutan Urgensi</span>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-secondary font-mono text-sm font-bold text-foreground">
                        #{{ $reportPriority->sort_order }}
                    </span>
                    <span class="text-xs text-muted-foreground">Urutan proses penanganan</span>
                </div>
                <p class="text-[11px] text-muted-foreground">Otomatisasi: Darurat (1) &rarr; Rendah (4)</p>
            </div>

            <!-- Status Card -->
            <div class="rounded-xl border border-border bg-background p-5 shadow-2xs space-y-2">
                <span class="text-[11px] font-semibold text-muted-foreground uppercase tracking-wider block">Status Master</span>
                <div>
                    @if ($reportPriority->is_active)
                        <span class="inline-flex items-center gap-1.5 rounded-md border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-1 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            <span>Aktif Digunakan</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-md border border-red-500/20 bg-red-500/10 px-2.5 py-1 text-xs font-medium text-red-600 dark:text-red-400">
                            <span class="h-2 w-2 rounded-full bg-red-500"></span>
                            <span>Nonaktif</span>
                        </span>
                    @endif
                </div>
                <p class="text-[11px] text-muted-foreground">Dapat dinonaktifkan tanpa menghapus relasi data.</p>
            </div>
        </div>

        <!-- SLA Matriks Card -->
        <div class="rounded-xl border border-border bg-background p-6 shadow-2xs space-y-4">
            <h2 class="text-sm font-bold text-foreground flex items-center gap-2">
                <x-lucide-gauge class="h-4 w-4 text-primary" />
                <span>Batas Waktu Service Level Agreement (SLA)</span>
            </h2>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="rounded-lg border border-border bg-secondary/30 p-4">
                    <span class="text-xs font-medium text-muted-foreground block">Target Respon Awal (Response SLA)</span>
                    <div class="mt-2 flex items-center gap-2">
                        <x-lucide-clock class="h-5 w-5 text-primary" />
                        <span class="text-2xl font-bold tracking-tight text-foreground">{{ $reportPriority->sla_response_hours }}</span>
                        <span class="text-xs font-semibold text-muted-foreground">Jam</span>
                    </div>
                    <p class="mt-2 text-[11px] text-muted-foreground">Batas waktu maksimal petugas memberikan konfirmasi penerimaan dan asesmen laporan.</p>
                </div>

                <div class="rounded-lg border border-border bg-secondary/30 p-4">
                    <span class="text-xs font-medium text-muted-foreground block">Target Jam Penyelesaian (Resolution SLA)</span>
                    <div class="mt-2 flex items-center gap-2">
                        <x-lucide-timer class="h-5 w-5 text-amber-500" />
                        <span class="text-2xl font-bold tracking-tight text-foreground">{{ $reportPriority->sla_resolution_hours }}</span>
                        <span class="text-xs font-semibold text-muted-foreground">Jam</span>
                    </div>
                    <p class="mt-2 text-[11px] text-muted-foreground">Batas waktu maksimal perbaikan fisik tuntas dan laporan diselesaikan di lapangan.</p>
                </div>
            </div>
        </div>

        <!-- Deskripsi Card -->
        <div class="rounded-xl border border-border bg-background p-6 shadow-2xs space-y-3">
            <h2 class="text-sm font-bold text-foreground flex items-center gap-2">
                <x-lucide-file-text class="h-4 w-4 text-primary" />
                <span>Deskripsi & Panduan Klasifikasi Kerusakan</span>
            </h2>
            <div class="text-xs text-muted-foreground leading-relaxed whitespace-pre-line rounded-lg bg-secondary/20 p-4 border border-border/50">
                {{ $reportPriority->description ?: 'Belum ada panduan kriteria atau deskripsi khusus yang ditambahkan.' }}
            </div>
        </div>
    </div>
</x-layouts.dashboard>

