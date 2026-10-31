@props([
    'announcements' => [],
    'showHeader' => true,
])

<div {{ $attributes->merge(['class' => 'space-y-3']) }}>
    @if ($showHeader && count($announcements) > 0)
        <div class="flex items-center justify-between pb-1">
            <div class="flex items-center gap-2">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                </span>
                <h3 class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Pengumuman & Pemberitahuan Aktif</h3>
            </div>
            <span class="text-[11px] font-medium text-muted-foreground">{{ count($announcements) }} Tersedia</span>
        </div>
    @endif

    @forelse ($announcements as $announcement)
        @php
            $isEmergency = $announcement->category === 'Keadaan Darurat';
            $isMaintenance = $announcement->category === 'Pemeliharaan Rutin';

            $bannerStyles = match (true) {
                $isEmergency => 'border-rose-500/30 bg-rose-500/5 text-rose-700 dark:text-rose-300',
                $isMaintenance => 'border-amber-500/30 bg-amber-500/5 text-amber-700 dark:text-amber-300',
                default => 'border-primary/20 bg-primary/5 text-foreground',
            };

            $badgeStyles = match (true) {
                $isEmergency => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
                $isMaintenance => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
                default => 'bg-primary/10 text-primary border-primary/20',
            };
        @endphp

        <div class="rounded-xl border p-4 shadow-2xs transition-all hover:shadow-xs {{ $bannerStyles }}">
            <div class="flex items-start gap-3">
                <div class="mt-0.5 rounded-lg p-2 bg-background/80 shadow-2xs shrink-0">
                    @if ($isEmergency)
                        <x-lucide-alert-triangle class="h-4 w-4 text-rose-500" />
                    @elseif ($isMaintenance)
                        <x-lucide-wrench class="h-4 w-4 text-amber-500" />
                    @else
                        <x-lucide-info class="h-4 w-4 text-primary" />
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $badgeStyles }}">
                            {{ $announcement->category }}
                        </span>

                        @if ($announcement->target_role)
                            <span class="text-[10px] text-muted-foreground">
                                Peran: {{ ucwords(str_replace('_', ' ', $announcement->target_role)) }}
                            </span>
                        @endif

                        @if ($announcement->target_building_id)
                            <span class="text-[10px] text-muted-foreground">
                                Gedung #{{ $announcement->target_building_id }}
                            </span>
                        @endif
                    </div>

                    <h4 class="text-sm font-semibold text-foreground tracking-tight line-clamp-1">
                        {{ $announcement->title }}
                    </h4>

                    <p class="mt-1 text-xs text-foreground/80 leading-relaxed whitespace-pre-line">
                        {{ $announcement->content }}
                    </p>

                    <div class="mt-3 pt-2.5 border-t border-border/50 flex flex-wrap items-center justify-between gap-2 text-[11px] text-muted-foreground">
                        <div class="flex items-center gap-1.5">
                            <x-lucide-clock class="h-3.5 w-3.5 shrink-0 opacity-70" />
                            <span>Berlaku s/d {{ $announcement->expires_at?->translatedFormat('d M Y, H:i') }}</span>
                        </div>

                        <span class="text-[10px] opacity-80">
                            Diterbitkan oleh {{ $announcement->author?->name ?? 'Admin Fasilitas' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="rounded-xl border border-dashed border-border bg-background/50 p-6 text-center text-muted-foreground">
            <x-lucide-bell-off class="mx-auto h-6 w-6 stroke-[1.5] text-muted-foreground/50 mb-2" />
            <p class="text-xs font-medium text-foreground">Tidak ada pengumuman baru saat ini</p>
            <p class="text-[11px]">Seluruh informasi operasional fasilitas akan ditampilkan di sini.</p>
        </div>
    @endforelse
</div>

