<x-layouts.dashboard title="Ubah Laporan #{{ $report->ticket_number }}">
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Back Navigation & Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a
                    href="{{ route('reporting.reports.show', $report) }}"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-border bg-background text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                    title="Kembali ke Detail Laporan"
                >
                    <x-lucide-arrow-left class="h-4 w-4" />
                </a>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-foreground">Ubah Laporan #{{ $report->ticket_number }}</h1>
                    <p class="text-xs text-muted-foreground mt-0.5">
                        Perbarui rincian laporan kerusakan sebelum diproses atau diverifikasi oleh supervisor.
                    </p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="rounded-xl border border-border bg-background p-6 shadow-2xs">
            <form action="{{ route('reporting.reports.update', $report) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Report Form Component -->
                <x-reporting-reports::report-form
                    :locations="$locations"
                    :buildings="$buildings"
                    :rooms="$rooms"
                    :facilities="$facilities"
                    :damageCategories="$damageCategories"
                    :priorities="$priorities"
                    :report="$report"
                />

                <!-- Action Buttons -->
                <div class="mt-6 pt-5 border-t border-border flex items-center justify-end gap-3">
                    <a
                        href="{{ route('reporting.reports.show', $report) }}"
                        class="rounded-lg border border-border bg-background px-4 py-2 text-xs font-medium text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                    >
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground shadow-2xs hover:bg-primary/90 transition-colors cursor-pointer"
                    >
                        <x-lucide-check class="h-3.5 w-3.5" />
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.dashboard>

