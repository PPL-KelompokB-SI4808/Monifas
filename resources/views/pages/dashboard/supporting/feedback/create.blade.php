<x-layouts.dashboard title="Beri Ulasan Fasilitas">
    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Breadcrumb / Back button -->
        <div class="flex items-center gap-2">
            <a
                href="{{ route('supporting.feedback.index') }}"
                class="inline-flex items-center gap-1 text-xs font-medium text-muted-foreground hover:text-foreground"
            >
                <x-lucide-arrow-left class="h-3.5 w-3.5" />
                Kembali ke Daftar Umpan Balik
            </a>
        </div>

        <!-- Form Card -->
        <x-ui.card class="p-6">
            <div class="border-b border-border pb-4 mb-5">
                <h1 class="text-lg font-bold tracking-tight text-foreground">Form Penilaian & Ulasan Pelapor</h1>
                <p class="text-xs text-muted-foreground mt-0.5">Beri rating bintang dan ulasan objektif mengenai hasil perbaikan sarana fasilitas.</p>
            </div>

            @if ($errors->any())
                <div class="mb-5 rounded-lg border border-red-500/20 bg-red-500/10 p-3 text-xs text-red-600 dark:text-red-400 space-y-1">
                    @foreach ($errors->all() as $error)
                        <p class="flex items-center gap-1.5">
                            <x-lucide-alert-circle class="h-3.5 w-3.5 shrink-0" />
                            <span>{{ $error }}</span>
                        </p>
                    @endforeach
                </div>
            @endif

            <!-- Report Reference Information -->
            @if($report)
                <div class="mb-5 rounded-lg bg-muted/40 p-4 border border-border text-xs space-y-1.5">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-foreground">Tiket #{{ $report->ticket_number }}</span>
                        <span class="px-2 py-0.5 rounded-full text-2xs font-medium bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">Selesai</span>
                    </div>
                    <div class="text-foreground font-medium">{{ $report->title }}</div>
                    <div class="text-muted-foreground text-2xs">Fasilitas: {{ $report->facility?->name ?? '-' }} | Kategori: {{ $report->damageCategory?->name ?? '-' }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('supporting.feedback.store') }}" class="space-y-5">
                @csrf

                @if($report)
                    <input type="hidden" name="report_id" value="{{ $report->id }}">
                @else
                    <div>
                        <label for="report_id" class="block text-xs font-medium text-foreground mb-1">
                            ID / Tiket Laporan Terkait <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            name="report_id"
                            id="report_id"
                            value="{{ old('report_id') }}"
                            required
                            placeholder="Masukkan ID laporan berstatus Completed..."
                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-xs text-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                        >
                    </div>
                @endif

                <!-- Rating Interactive Alpine Component -->
                <div>
                    <label class="block text-xs font-medium text-foreground mb-1.5">
                        Tingkat Kepuasan (Rating) <span class="text-red-500">*</span>
                    </label>
                    <x-supporting-feedback::star-rating-input
                        name="rating"
                        :value="old('rating', 5)"
                    />
                </div>

                <!-- Comment Input -->
                <div>
                    <label for="comment" class="block text-xs font-medium text-foreground mb-1">
                        Ulasan / Masukan Pelapor <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        name="comment"
                        id="comment"
                        rows="4"
                        required
                        placeholder="Berikan pendapat Anda tentang kecepatan, kerapian hasil kerja, atau keramahan petugas..."
                        class="w-full rounded-md border border-input bg-background p-3 text-xs text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                    >{{ old('comment') }}</textarea>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-border">
                    <a
                        href="{{ route('supporting.feedback.index') }}"
                        class="rounded-md px-4 py-2 text-xs font-medium border border-border text-foreground hover:bg-secondary transition-colors"
                    >
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="inline-flex items-center gap-1.5 rounded-md px-5 py-2 text-xs font-medium bg-primary text-primary-foreground hover:bg-primary/90 transition-colors"
                    >
                        <x-lucide-send class="h-3.5 w-3.5" />
                        Kirim Ulasan
                    </button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-layouts.dashboard>
