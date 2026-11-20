@props([
    'campaign',
])

@php
    use App\Enums\CampaignStatus;

    $statusEnum = $campaign->status instanceof CampaignStatus
        ? $campaign->status
        : CampaignStatus::tryFrom((string) $campaign->status);

    $statusClasses = match ($statusEnum) {
        CampaignStatus::Active => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
        CampaignStatus::Scheduled => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
        CampaignStatus::Ended => 'bg-muted text-muted-foreground border-border',
        CampaignStatus::Cancelled => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
        default => 'bg-secondary text-secondary-foreground border-border',
    };
@endphp

<div class="group relative flex flex-col overflow-hidden rounded-xl border border-border bg-background shadow-2xs transition-all hover:shadow-md hover:border-primary/30">
    {{-- Banner --}}
    <div class="relative h-40 w-full overflow-hidden bg-muted">
        @if ($campaign->banner_path)
            <img
                src="{{ asset('storage/' . $campaign->banner_path) }}"
                alt="{{ $campaign->title }}"
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
            />
        @else
            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-primary/10 via-primary/5 to-transparent">
                <x-lucide-megaphone class="h-10 w-10 text-primary/30" />
            </div>
        @endif

        {{-- Status Badge Overlay --}}
        <span class="absolute top-2.5 right-2.5 inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-[10px] font-semibold backdrop-blur-sm {{ $statusClasses }}">
            <span class="h-1.5 w-1.5 rounded-full bg-current opacity-70"></span>
            {{ $statusEnum?->label() ?? ucfirst((string) $campaign->status) }}
        </span>
    </div>

    {{-- Content --}}
    <div class="flex flex-1 flex-col p-4 space-y-3">
        {{-- Title --}}
        <a href="{{ route('supporting.campaigns.show', $campaign) }}" class="block">
            <h3 class="text-sm font-bold leading-snug text-foreground line-clamp-2 group-hover:text-primary transition-colors">
                {{ $campaign->title }}
            </h3>
        </a>

        {{-- Objective --}}
        <p class="text-xs text-muted-foreground leading-relaxed line-clamp-2">
            {{ $campaign->objective }}
        </p>

        {{-- Meta --}}
        <div class="mt-auto space-y-2 pt-3 border-t border-border/60">
            <div class="flex items-center gap-1.5 text-[11px] text-muted-foreground">
                <x-lucide-building-2 class="h-3 w-3 shrink-0" />
                <span class="truncate">{{ $campaign->building?->name ?? '-' }}</span>
            </div>
            <div class="flex items-center gap-1.5 text-[11px] text-muted-foreground">
                <x-lucide-layers class="h-3 w-3 shrink-0" />
                <span class="truncate">{{ $campaign->facilityCategory?->name ?? '-' }}</span>
            </div>
            <div class="flex items-center gap-1.5 text-[11px] text-muted-foreground">
                <x-lucide-calendar-range class="h-3 w-3 shrink-0" />
                <span>{{ $campaign->start_date->format('d M Y') }} – {{ $campaign->end_date->format('d M Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Action Footer --}}
    <div class="flex items-center justify-end gap-1.5 border-t border-border/60 px-4 py-2.5 bg-muted/20">
        <a
            href="{{ route('supporting.campaigns.show', $campaign) }}"
            class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-[11px] font-medium text-foreground hover:bg-secondary transition-colors"
            title="Detail"
        >
            <x-lucide-eye class="h-3 w-3" />
            <span>Detail</span>
        </a>
        <a
            href="{{ route('supporting.campaigns.edit', $campaign) }}"
            class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-[11px] font-medium text-foreground hover:bg-secondary transition-colors"
            title="Edit"
        >
            <x-lucide-pencil class="h-3 w-3" />
            <span>Edit</span>
        </a>
        <form method="POST" action="{{ route('supporting.campaigns.destroy', $campaign) }}" class="inline">
            @csrf
            @method('DELETE')
            <button
                type="submit"
                onclick="return confirm('Apakah Anda yakin ingin menghapus kampanye ini?')"
                class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-[11px] font-medium text-rose-600 dark:text-rose-400 hover:bg-rose-500/10 transition-colors"
                title="Hapus"
            >
                <x-lucide-trash-2 class="h-3 w-3" />
                <span>Hapus</span>
            </button>
        </form>
    </div>
</div>
