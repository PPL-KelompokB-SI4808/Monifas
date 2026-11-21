@php
    use App\Enums\CampaignStatus;

    $statusEnum = $campaign->status instanceof CampaignStatus
        ? $campaign->status
        : CampaignStatus::tryFrom((string) $campaign->status);

    $statusValue = $statusEnum?->value ?? (string) $campaign->status;

    $statusClasses = match ($statusEnum) {
        CampaignStatus::Active => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
        CampaignStatus::Scheduled => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
        CampaignStatus::Ended => 'bg-muted text-muted-foreground border-border',
        CampaignStatus::Cancelled => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
        default => 'bg-secondary text-secondary-foreground border-border',
    };
@endphp

<x-layouts.dashboard title="Detail Kampanye Edukasi">
    <div class="mx-auto max-w-4xl space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <a
                    href="{{ route('supporting.campaigns.index') }}"
                    class="rounded-lg border border-border bg-background p-2 text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                    title="Kembali ke Daftar Kampanye"
                >
                    <x-lucide-arrow-left class="h-4 w-4" />
                </a>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-foreground">Detail Kampanye Edukasi</h1>
                    <p class="text-xs text-muted-foreground mt-0.5">Informasi lengkap materi dan jadwal penayangan kampanye.</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
                <a
                    href="{{ route('supporting.campaigns.edit', $campaign) }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-background px-3 py-1.5 text-xs font-medium text-foreground hover:bg-secondary transition-colors"
                >
                    <x-lucide-pencil class="h-3.5 w-3.5" />
                    <span>Edit</span>
                </a>

                @if ($statusValue !== 'cancelled' && $statusValue !== 'ended')
                    <form method="POST" action="{{ route('supporting.campaigns.cancel', $campaign) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <button
                            type="submit"
                            onclick="return confirm('Apakah Anda yakin ingin membatalkan kampanye ini?')"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-background px-3 py-1.5 text-xs font-medium text-amber-600 dark:text-amber-400 hover:bg-amber-500/10 transition-colors"
                        >
                            <x-lucide-ban class="h-3.5 w-3.5" />
                            <span>Batalkan</span>
                        </button>
                    </form>
                @endif

                <form method="POST" action="{{ route('supporting.campaigns.destroy', $campaign) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        onclick="return confirm('Apakah Anda yakin ingin menghapus kampanye ini?')"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-rose-500/20 bg-rose-500/10 px-3 py-1.5 text-xs font-medium text-rose-600 dark:text-rose-400 hover:bg-rose-500/20 transition-colors"
                    >
                        <x-lucide-trash-2 class="h-3.5 w-3.5" />
                        <span>Hapus</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Card -->
        <div class="rounded-xl border border-border bg-background shadow-2xs overflow-hidden">
            {{-- Banner (if exists) --}}
            @if ($campaign->banner_path)
                <div class="relative h-64 w-full overflow-hidden bg-muted">
                    <img
                        src="{{ asset('storage/' . $campaign->banner_path) }}"
                        alt="{{ $campaign->title }}"
                        class="h-full w-full object-cover"
                    />
                </div>
            @endif

            <div class="p-6 space-y-6">
                <!-- Badges & Meta -->
                <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-border">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $statusClasses }}">
                            <span class="h-1.5 w-1.5 rounded-full bg-current opacity-70"></span>
                            {{ $statusEnum?->label() ?? ucfirst($statusValue) }}
                        </span>

                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold border border-border bg-secondary text-secondary-foreground">
                            <x-lucide-building-2 class="h-3 w-3" />
                            {{ $campaign->building?->name ?? 'Gedung' }}
                        </span>

                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold border border-border bg-secondary text-secondary-foreground">
                            <x-lucide-layers class="h-3 w-3" />
                            {{ $campaign->facilityCategory?->name ?? 'Kategori' }}
                        </span>
                    </div>

                    <div class="text-xs text-muted-foreground flex items-center gap-1.5">
                        <x-lucide-user class="h-3.5 w-3.5" />
                        <span>Dibuat oleh <strong>{{ $campaign->creator?->name ?? 'Admin Sistem' }}</strong></span>
                    </div>
                </div>

                <!-- Title -->
                <div>
                    <h2 class="text-xl font-bold text-foreground tracking-tight">
                        {{ $campaign->title }}
                    </h2>
                </div>

                <!-- Objective -->
                <div class="rounded-lg border border-primary/20 bg-primary/5 p-4 text-xs space-y-1.5">
                    <h4 class="font-semibold text-primary flex items-center gap-1.5">
                        <x-lucide-target class="h-3.5 w-3.5" />
                        <span>Tujuan Kampanye</span>
                    </h4>
                    <p class="text-foreground leading-relaxed">{{ $campaign->objective }}</p>
                </div>

                <!-- Content -->
                <div class="space-y-2">
                    <h4 class="text-xs font-semibold text-foreground flex items-center gap-1.5">
                        <x-lucide-book-open class="h-3.5 w-3.5 text-primary" />
                        <span>Materi Edukasi & Konten</span>
                    </h4>
                    <div class="rounded-lg border border-border/60 bg-muted/20 p-4 text-xs text-foreground/90 leading-relaxed whitespace-pre-line">
                        {{ $campaign->content }}
                    </div>
                </div>

                <!-- Meta Grid (Target & Timing) -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 pt-4 border-t border-border text-xs">
                    <!-- Target Information -->
                    <div class="space-y-2">
                        <h4 class="font-semibold text-foreground flex items-center gap-1.5">
                            <x-lucide-map-pin class="h-3.5 w-3.5 text-primary" />
                            <span>Target Edukasi</span>
                        </h4>
                        <div class="space-y-1 text-muted-foreground">
                            <p>
                                <span class="font-medium text-foreground">Gedung:</span>
                                {{ $campaign->building?->name ?? '-' }}
                            </p>
                            <p>
                                <span class="font-medium text-foreground">Kategori Fasilitas:</span>
                                {{ $campaign->facilityCategory?->name ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <!-- Schedule & Timing -->
                    <div class="space-y-2">
                        <h4 class="font-semibold text-foreground flex items-center gap-1.5">
                            <x-lucide-calendar class="h-3.5 w-3.5 text-primary" />
                            <span>Jadwal Penayangan</span>
                        </h4>
                        <div class="space-y-1 text-muted-foreground">
                            <p>
                                <span class="font-medium text-foreground">Tanggal Mulai:</span>
                                {{ $campaign->start_date?->translatedFormat('d F Y') }}
                            </p>
                            <p>
                                <span class="font-medium text-foreground">Tanggal Berakhir:</span>
                                {{ $campaign->end_date?->translatedFormat('d F Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.dashboard>
