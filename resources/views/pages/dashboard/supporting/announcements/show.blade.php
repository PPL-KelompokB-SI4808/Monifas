@php
    use App\Enums\AnnouncementStatus;

    $statusValue = $announcement->status instanceof AnnouncementStatus
        ? $announcement->status->value
        : (string) $announcement->status;

    $statusClasses = match ($statusValue) {
        'published' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
        'scheduled' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
        'archived' => 'bg-muted text-muted-foreground border-border',
        'cancelled' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
        default => 'bg-secondary text-secondary-foreground border-border',
    };
@endphp

<x-layouts.dashboard title="Detail Pengumuman">
    <div class="mx-auto max-w-4xl space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <a
                    href="{{ route('supporting.announcements.index') }}"
                    class="rounded-lg border border-border bg-background p-2 text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                    title="Kembali ke Daftar Pengumuman"
                >
                    <x-lucide-arrow-left class="h-4 w-4" />
                </a>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-foreground">Detail Pengumuman</h1>
                    <p class="text-xs text-muted-foreground mt-0.5">Informasi lengkap penayangan pengumuman fasilitas.</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
                <a
                    href="{{ route('supporting.announcements.edit', $announcement) }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-background px-3 py-1.5 text-xs font-medium text-foreground hover:bg-secondary transition-colors"
                >
                    <x-lucide-pencil class="h-3.5 w-3.5" />
                    <span>Edit</span>
                </a>

                @if ($statusValue !== 'archived' && $statusValue !== 'cancelled')
                    <form method="POST" action="{{ route('supporting.announcements.archive', $announcement) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <button
                            type="submit"
                            onclick="return confirm('Apakah Anda yakin ingin mengarsipkan pengumuman ini?')"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-background px-3 py-1.5 text-xs font-medium text-amber-600 dark:text-amber-400 hover:bg-amber-500/10 transition-colors"
                        >
                            <x-lucide-archive class="h-3.5 w-3.5" />
                            <span>Arsipkan</span>
                        </button>
                    </form>
                @endif

                <form method="POST" action="{{ route('supporting.announcements.destroy', $announcement) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        onclick="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-rose-500/20 bg-rose-500/10 px-3 py-1.5 text-xs font-medium text-rose-600 dark:text-rose-400 hover:bg-rose-500/20 transition-colors"
                    >
                        <x-lucide-trash-2 class="h-3.5 w-3.5" />
                        <span>Hapus</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Card -->
        <div class="rounded-xl border border-border bg-background p-6 shadow-2xs space-y-6">
            <!-- Header Badges & Meta -->
            <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-border">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold border border-border bg-secondary text-secondary-foreground">
                        {{ $announcement->category }}
                    </span>

                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $statusClasses }}">
                        <span class="h-1.5 w-1.5 rounded-full bg-current opacity-70"></span>
                        {{ $announcement->status instanceof AnnouncementStatus ? $announcement->status->label() : ucfirst($statusValue) }}
                    </span>
                </div>

                <div class="text-xs text-muted-foreground flex items-center gap-2">
                    <x-lucide-user class="h-3.5 w-3.5" />
                    <span>Diterbitkan oleh <strong>{{ $announcement->author?->name ?? 'Admin Sistem' }}</strong></span>
                </div>
            </div>

            <!-- Title -->
            <div>
                <h2 class="text-lg font-bold text-foreground tracking-tight">
                    {{ $announcement->title }}
                </h2>
            </div>

            <!-- Content -->
            <div class="rounded-lg border border-border/60 bg-muted/20 p-4 text-xs text-foreground/90 leading-relaxed whitespace-pre-line">
                {{ $announcement->content }}
            </div>

            <!-- Meta Grid (Target & Timing) -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 pt-2 border-t border-border text-xs">
                <!-- Target Audience -->
                <div class="space-y-2">
                    <h4 class="font-semibold text-foreground flex items-center gap-1.5">
                        <x-lucide-users class="h-3.5 w-3.5 text-primary" />
                        <span>Target Audiens</span>
                    </h4>
                    <div class="space-y-1 text-muted-foreground">
                        <p>
                            <span class="font-medium text-foreground">Target Peran:</span>
                            {{ $announcement->target_role ? ucwords(str_replace('_', ' ', $announcement->target_role)) : 'Semua Pengguna (Global)' }}
                        </p>
                        <p>
                            <span class="font-medium text-foreground">Target Gedung:</span>
                            {{ $announcement->targetBuilding?->name ?? ($announcement->target_building_id ? 'ID #' . $announcement->target_building_id : 'Semua Gedung (Global)') }}
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
                            <span class="font-medium text-foreground">Mulai Tayang:</span>
                            {{ $announcement->publish_at?->translatedFormat('d F Y, H:i') }}
                        </p>
                        <p>
                            <span class="font-medium text-foreground">Berakhir:</span>
                            {{ $announcement->expires_at?->translatedFormat('d F Y, H:i') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.dashboard>

