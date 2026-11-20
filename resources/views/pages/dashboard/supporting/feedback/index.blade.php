<x-layouts.dashboard title="Ulasan & Penilaian Fasilitas">
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground">Umpan Balik & Kepuasan Pelapor</h1>
                <p class="text-xs text-muted-foreground mt-0.5">Monitoring rating, kritik, saran, serta tanggapan resmi dari pihak pengelola fasilitas.</p>
            </div>
        </div>

        <!-- Flash Notifications -->
        @if (session('success'))
            <div class="flex items-center gap-2 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                <x-lucide-check-circle-2 class="h-4 w-4 shrink-0" />
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-center gap-2 rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-xs font-medium text-red-600 dark:text-red-400">
                <x-lucide-alert-circle class="h-4 w-4 shrink-0" />
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Filter Bar Component -->
        <x-supporting-feedback::feedback-filter-bar
            :facilityCategories="$facilityCategories"
            :technicians="$technicians"
            :filters="$filters"
        />

        <!-- Feedbacks List / Grid -->
        @if($feedbacks->isEmpty())
            <x-ui.card class="p-12 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-muted/60 text-muted-foreground">
                    <x-lucide-message-square-off class="h-6 w-6" />
                </div>
                <h3 class="mt-3 text-sm font-semibold text-foreground">Belum ada umpan balik</h3>
                <p class="mt-1 text-xs text-muted-foreground">Tidak ditemukan catatan rating atau umpan balik yang sesuai dengan kriteria filter.</p>
            </x-ui.card>
        @else
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach($feedbacks as $feedback)
                    <x-supporting-feedback::feedback-card :feedback="$feedback" />
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $feedbacks->links() }}
            </div>
        @endif

        <!-- Response Modal Component -->
        <x-supporting-feedback::feedback-response-modal />
    </div>
</x-layouts.dashboard>
