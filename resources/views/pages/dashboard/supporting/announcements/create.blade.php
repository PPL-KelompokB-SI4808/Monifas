<x-layouts.dashboard title="Buat Pengumuman Baru">
    <div class="mx-auto max-w-3xl space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground">Buat Pengumuman Baru</h1>
                <p class="text-xs text-muted-foreground mt-0.5">Siarkan informasi atau jadwal pemeliharaan fasilitas kepada pengguna.</p>
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
            :action="route('supporting.announcements.store')"
            method="POST"
        />
    </div>
</x-layouts.dashboard>

