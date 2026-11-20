<x-layouts.dashboard title="Edit Ulasan Fasilitas">
    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Breadcrumb / Back button -->
        <div class="flex items-center gap-2">
            <a
                href="{{ route('supporting.feedback.show', $feedback) }}"
                class="inline-flex items-center gap-1 text-xs font-medium text-muted-foreground hover:text-foreground"
            >
                <x-lucide-arrow-left class="h-3.5 w-3.5" />
                Kembali ke Detail Ulasan
            </a>
        </div>

        <!-- Form Card -->
        <x-ui.card class="p-6">
            <div class="border-b border-border pb-4 mb-5">
                <h1 class="text-lg font-bold tracking-tight text-foreground">Perbarui Ulasan & Penilaian</h1>
                <p class="text-xs text-muted-foreground mt-0.5">Edit ulasan sebelum adanya tanggapan resmi dari pihak pengelola.</p>
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

            <form method="POST" action="{{ route('supporting.feedback.update', $feedback) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <!-- Rating Interactive Alpine Component -->
                <div>
                    <label class="block text-xs font-medium text-foreground mb-1.5">
                        Tingkat Kepuasan (Rating) <span class="text-red-500">*</span>
                    </label>
                    <x-supporting-feedback::star-rating-input
                        name="rating"
                        :value="old('rating', $feedback->rating)"
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
                        placeholder="Berikan pendapat objektif..."
                        class="w-full rounded-md border border-input bg-background p-3 text-xs text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                    >{{ old('comment', $feedback->comment) }}</textarea>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-border">
                    <a
                        href="{{ route('supporting.feedback.show', $feedback) }}"
                        class="rounded-md px-4 py-2 text-xs font-medium border border-border text-foreground hover:bg-secondary transition-colors"
                    >
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="inline-flex items-center gap-1.5 rounded-md px-5 py-2 text-xs font-medium bg-primary text-primary-foreground hover:bg-primary/90 transition-colors"
                    >
                        <x-lucide-save class="h-3.5 w-3.5" />
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-layouts.dashboard>
