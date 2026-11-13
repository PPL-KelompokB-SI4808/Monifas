<x-layouts.dashboard title="Ubah Prioritas Laporan">
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
                    <h1 class="text-xl font-bold tracking-tight text-foreground">Ubah Data Prioritas</h1>
                    <p class="text-xs text-muted-foreground mt-0.5">Perbarui konfigurasi level, urutan urgensi, atau target jam SLA: {{ $reportPriority->name }}</p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="rounded-xl border border-border bg-background p-6 shadow-2xs">
            <form action="{{ route('reporting.report-priorities.update', $reportPriority) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Priority Form Component -->
                <x-reporting-report-priorities::priority-form
                    :priority="$reportPriority"
                    :levels="$levels"
                    :badgeVariants="$badgeVariants"
                />

                <!-- Action Buttons -->
                <div class="mt-6 pt-5 border-t border-border flex items-center justify-end gap-3">
                    <a
                        href="{{ route('reporting.report-priorities.index') }}"
                        class="rounded-lg border border-border bg-background px-4 py-2 text-xs font-medium text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                    >
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-xs font-medium text-primary-foreground shadow-2xs hover:bg-primary/90 transition-colors cursor-pointer"
                    >
                        <x-lucide-save class="h-3.5 w-3.5" />
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.dashboard>

