<x-layouts.dashboard title="Edit Kampanye Edukasi">
    <div class="mx-auto max-w-3xl space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground">Edit Kampanye</h1>
                <p class="text-xs text-muted-foreground mt-0.5">Perbarui informasi, materi, atau jadwal kampanye edukasi.</p>
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
            :campaign="$campaign"
            :action="route('supporting.campaigns.update', $campaign)"
            method="PUT"
            :buildings="$buildings"
            :categories="$categories"
        />
    </div>
</x-layouts.dashboard>
