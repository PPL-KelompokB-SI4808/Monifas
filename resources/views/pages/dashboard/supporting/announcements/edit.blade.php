<x-layouts.dashboard title="Edit Pengumuman">
    <div class="mx-auto max-w-3xl space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground">Edit Pengumuman</h1>
                <p class="text-xs text-muted-foreground mt-0.5">Perbarui isi konten, sasaran pengguna, atau jadwal penayangan.</p>
            </div>
            <a
                href="{{ route('supporting.announcements.index') }}"
                class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-background px-3 py-1.5 text-xs font-medium text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
            >
                <x-lucide-arrow-left class="h-3.5 w-3.5" />
                <span>Kembali</span>
            </a>
        </div>

        <!-- Announcement Form Component -->
        <x-supporting-announcements::announcement-form
            :announcement="$announcement"
            :action="route('supporting.announcements.update', $announcement)"
            method="PUT"
        />
    </div>
</x-layouts.dashboard>

