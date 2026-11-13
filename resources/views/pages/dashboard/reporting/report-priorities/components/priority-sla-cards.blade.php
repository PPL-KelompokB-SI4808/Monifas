@props(['priorities' => []])

<div {{ $attributes->merge(['class' => 'grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4']) }}>
    @forelse($priorities as $priority)
        @php
            $badgeStyles = match ($priority->badge_variant) {
                'danger' => [
                    'border' => 'border-red-500/20 dark:border-red-500/30',
                    'bg' => 'bg-red-500/5 dark:bg-red-500/10',
                    'text' => 'text-red-600 dark:text-red-400',
                    'badge' => 'border-red-500/30 bg-red-500/15 text-red-700 dark:text-red-300',
                    'icon' => 'lucide-alert-octagon',
                ],
                'warning' => [
                    'border' => 'border-amber-500/20 dark:border-amber-500/30',
                    'bg' => 'bg-amber-500/5 dark:bg-amber-500/10',
                    'text' => 'text-amber-600 dark:text-amber-400',
                    'badge' => 'border-amber-500/30 bg-amber-500/15 text-amber-700 dark:text-amber-300',
                    'icon' => 'lucide-alert-triangle',
                ],
                'info' => [
                    'border' => 'border-blue-500/20 dark:border-blue-500/30',
                    'bg' => 'bg-blue-500/5 dark:bg-blue-500/10',
                    'text' => 'text-blue-600 dark:text-blue-400',
                    'badge' => 'border-blue-500/30 bg-blue-500/15 text-blue-700 dark:text-blue-300',
                    'icon' => 'lucide-info',
                ],
                'emerald', 'success' => [
                    'border' => 'border-emerald-500/20 dark:border-emerald-500/30',
                    'bg' => 'bg-emerald-500/5 dark:bg-emerald-500/10',
                    'text' => 'text-emerald-600 dark:text-emerald-400',
                    'badge' => 'border-emerald-500/30 bg-emerald-500/15 text-emerald-700 dark:text-emerald-300',
                    'icon' => 'lucide-check-circle-2',
                ],
                default => [
                    'border' => 'border-border',
                    'bg' => 'bg-secondary/40',
                    'text' => 'text-muted-foreground',
                    'badge' => 'border-border bg-secondary text-foreground',
                    'icon' => 'lucide-circle-dot',
                ],
            };
        @endphp

        <div class="relative flex flex-col justify-between rounded-xl border {{ $badgeStyles['border'] }} {{ $badgeStyles['bg'] }} p-4 shadow-2xs transition-all hover:shadow-xs">
            <div>
                <!-- Top Row: Badge Level & Sort Order -->
                <div class="flex items-center justify-between gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full border {{ $badgeStyles['badge'] }} px-2.5 py-0.5 text-[11px] font-semibold uppercase tracking-wider">
                        <span class="h-1.5 w-1.5 rounded-full {{ $priority->is_active ? 'bg-current animate-pulse' : 'bg-muted-foreground' }}"></span>
                        {{ $priority->level instanceof \App\Enums\PriorityLevel ? $priority->level->label() : ucfirst($priority->level) }}
                    </span>

                    <span class="inline-flex items-center gap-1 rounded-md border border-border/60 bg-background/80 px-2 py-0.5 text-[11px] font-mono font-medium text-muted-foreground backdrop-blur-xs" title="Tingkat Urgensi #{{ $priority->sort_order }}">
                        <span>Urutan #{{ $priority->sort_order }}</span>
                    </span>
                </div>

                <!-- Priority Name -->
                <h3 class="mt-3 text-sm font-bold text-foreground">
                    {{ $priority->name }}
                </h3>

                <!-- Description -->
                <p class="mt-1 text-xs text-muted-foreground line-clamp-2">
                    {{ $priority->description ?: 'Tidak ada deskripsi detail untuk tingkat prioritas ini.' }}
                </p>
            </div>

            <!-- SLA Metrics -->
            <div class="mt-4 pt-3 border-t border-border/60 grid grid-cols-2 gap-2 text-xs">
                <div class="rounded-lg bg-background/80 p-2 border border-border/50">
                    <span class="text-[10px] font-medium text-muted-foreground uppercase tracking-wider block">Target Respon</span>
                    <div class="mt-0.5 flex items-center gap-1 font-semibold text-foreground">
                        <x-lucide-clock class="h-3.5 w-3.5 text-primary" />
                        <span>{{ $priority->sla_response_hours }} Jam</span>
                    </div>
                </div>

                <div class="rounded-lg bg-background/80 p-2 border border-border/50">
                    <span class="text-[10px] font-medium text-muted-foreground uppercase tracking-wider block">Target Selesai</span>
                    <div class="mt-0.5 flex items-center gap-1 font-semibold text-foreground">
                        <x-lucide-timer class="h-3.5 w-3.5 text-amber-500" />
                        <span>{{ $priority->sla_resolution_hours }} Jam</span>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full rounded-xl border border-dashed border-border bg-secondary/20 p-6 text-center">
            <x-lucide-clock-alert class="mx-auto h-8 w-8 text-muted-foreground/60" />
            <p class="mt-2 text-xs font-medium text-foreground">Belum ada data master prioritas laporan.</p>
            <p class="text-[11px] text-muted-foreground">Tambahkan data prioritas baru atau jalankan seeder untuk inisialisasi default.</p>
        </div>
    @endforelse
</div>

