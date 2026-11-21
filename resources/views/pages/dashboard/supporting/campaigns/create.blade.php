<x-layouts.dashboard title="Buat Kampanye Edukasi Baru">
    <div class="mx-auto max-w-3xl space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground">Buat Kampanye Baru</h1>
                <p class="text-xs text-muted-foreground mt-0.5">Jadwalkan dan luncurkan materi kampanye edukasi pemeliharaan fasilitas.</p>
            </div>
            <a
                href="{{ route('supporting.campaigns.index') }}"
                class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-background px-3 py-1.5 text-xs font-medium text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
            >
                <x-lucide-arrow-left class="h-3.5 w-3.5" />
                <span>Kembali</span>
            </a>
        </div>

        <!-- Campaign Form Component -->
        <x-supporting-campaigns::campaign-form
            :action="route('supporting.campaigns.store')"
            method="POST"
            :buildings="$buildings"
            :categories="$categories"
        />
    </div>
</x-layouts.dashboard>
