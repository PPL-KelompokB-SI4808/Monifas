@props([
    'announcements',
    'filters' => [],
])

@php
    use App\Enums\AnnouncementStatus;
@endphp

<div class="space-y-4">
    <!-- Filter Bar & Create Button -->
    <div class="rounded-xl border border-border bg-background p-4 shadow-2xs">
        <form method="GET" action="{{ route('supporting.announcements.index') }}" class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2 lg:flex lg:flex-wrap lg:items-center">
                <!-- Search Input -->
                <div class="relative min-w-[220px]">
                    <x-lucide-search class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground" />
                    <input
                        type="text"
                        name="search"
                        value="{{ $filters['search'] ?? '' }}"
                        placeholder="Cari judul atau isi pengumuman..."
                        class="w-full rounded-lg border border-border bg-background pl-8.5 pr-3 py-1.5 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                    />
                </div>

                <!-- Status Filter -->
                <div>
                    <select
                        name="status"
                        class="w-full rounded-lg border border-border bg-background px-3 py-1.5 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                    >
                        <option value="">Semua Status</option>
                        @foreach (AnnouncementStatus::cases() as $statusCase)
                            <option value="{{ $statusCase->value }}" @selected(($filters['status'] ?? '') === $statusCase->value)>
                                {{ $statusCase->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Category Filter -->
                <div>
                    <select
                        name="category"
                        class="w-full rounded-lg border border-border bg-background px-3 py-1.5 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                    >
                        <option value="">Semua Kategori</option>
                        <option value="Pemeliharaan Rutin" @selected(($filters['category'] ?? '') === 'Pemeliharaan Rutin')>Pemeliharaan Rutin</option>
                        <option value="Keadaan Darurat" @selected(($filters['category'] ?? '') === 'Keadaan Darurat')>Keadaan Darurat</option>
                        <option value="Informasi Umum" @selected(($filters['category'] ?? '') === 'Informasi Umum')>Informasi Umum</option>
                    </select>
                </div>

                <!-- Submit Filter Button -->
                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-border bg-secondary px-3 py-1.5 text-xs font-medium text-secondary-foreground hover:bg-secondary/80 transition-colors"
                >
                    <x-lucide-filter class="h-3.5 w-3.5" />
                    <span>Filter</span>
                </button>

                @if (!empty($filters['search']) || !empty($filters['status']) || !empty($filters['category']))
                    <a
                        href="{{ route('supporting.announcements.index') }}"
                        class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-border bg-background px-3 py-1.5 text-xs font-medium text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                    >
                        <x-lucide-rotate-ccw class="h-3.5 w-3.5" />
                        <span>Reset</span>
                    </a>
                @endif
            </div>

            <!-- Add Announcement Button -->
            <a
                href="{{ route('supporting.announcements.create') }}"
                class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-primary px-3.5 py-1.5 text-xs font-medium text-primary-foreground shadow-2xs hover:bg-primary/90 transition-colors"
            >
                <x-lucide-plus class="h-3.5 w-3.5" />
                <span>Buat Pengumuman</span>
            </a>
        </form>
    </div>

    <!-- Table Container -->
    <div class="rounded-xl border border-border bg-background shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-border bg-muted/30 text-muted-foreground font-semibold">
                        <th class="px-4 py-3">Pengumuman</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Target</th>
                        <th class="px-4 py-3">Periode Tayang</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($announcements as $announcement)
                        <tr class="hover:bg-muted/10 transition-colors">
                            <!-- Title & Author -->
                            <td class="px-4 py-3.5">
                                <a href="{{ route('supporting.announcements.show', $announcement) }}" class="font-medium text-foreground hover:text-primary transition-colors line-clamp-1">
                                    {{ $announcement->title }}
                                </a>
                                <div class="mt-0.5 text-[11px] text-muted-foreground flex items-center gap-1.5">
                                    <x-lucide-user class="h-3 w-3 shrink-0" />
                                    <span>{{ $announcement->author?->name ?? 'Sistem' }}</span>
                                    <span>•</span>
                                    <span>{{ $announcement->created_at->translatedFormat('d M Y') }}</span>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium border border-border bg-secondary text-secondary-foreground">
                                    {{ $announcement->category }}
                                </span>
                            </td>

                            <!-- Target -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="flex flex-col gap-0.5 text-[11px]">
                                    <span class="text-foreground font-medium">
                                        Peran: {{ $announcement->target_role ? ucwords(str_replace('_', ' ', $announcement->target_role)) : 'Semua Pengguna' }}
                                    </span>
                                    <span class="text-muted-foreground">
                                        Gedung: {{ $announcement->targetBuilding?->name ?? ($announcement->target_building_id ? 'ID #' . $announcement->target_building_id : 'Semua Gedung') }}
                                    </span>
                                </div>
                            </td>

                            <!-- Period -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="flex flex-col gap-0.5 text-[11px] text-muted-foreground">
                                    <span class="text-foreground font-medium flex items-center gap-1">
                                        <x-lucide-calendar class="h-3 w-3" />
                                        {{ $announcement->publish_at?->format('d/m/Y H:i') }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <x-lucide-clock class="h-3 w-3" />
                                        s/d {{ $announcement->expires_at?->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                            </td>

                            <!-- Status Badge -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @php
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
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-medium border {{ $statusClasses }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current opacity-70"></span>
                                    {{ $announcement->status instanceof AnnouncementStatus ? $announcement->status->label() : ucfirst($statusValue) }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-3.5 whitespace-nowrap text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- View / Detail -->
                                    <a
                                        href="{{ route('supporting.announcements.show', $announcement) }}"
                                        title="Lihat Detail"
                                        class="rounded-md p-1.5 text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                                    >
                                        <x-lucide-eye class="h-3.5 w-3.5" />
                                    </a>

                                    <!-- Edit -->
                                    <a
                                        href="{{ route('supporting.announcements.edit', $announcement) }}"
                                        title="Edit Pengumuman"
                                        class="rounded-md p-1.5 text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                                    >
                                        <x-lucide-pencil class="h-3.5 w-3.5" />
                                    </a>

                                    <!-- Archive (if not already archived/cancelled) -->
                                    @if ($statusValue !== 'archived' && $statusValue !== 'cancelled')
                                        <form method="POST" action="{{ route('supporting.announcements.archive', $announcement) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                type="submit"
                                                onclick="return confirm('Apakah Anda yakin ingin mengarsipkan pengumuman ini?')"
                                                title="Arsipkan Pengumuman"
                                                class="rounded-md p-1.5 text-muted-foreground hover:bg-amber-500/10 hover:text-amber-600 transition-colors"
                                            >
                                                <x-lucide-archive class="h-3.5 w-3.5" />
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Delete -->
                                    <form method="POST" action="{{ route('supporting.announcements.destroy', $announcement) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')"
                                            title="Hapus Pengumuman"
                                            class="rounded-md p-1.5 text-muted-foreground hover:bg-rose-500/10 hover:text-rose-600 transition-colors"
                                        >
                                            <x-lucide-trash-2 class="h-3.5 w-3.5" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-muted-foreground">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <x-lucide-megaphone-off class="h-8 w-8 stroke-[1.5] text-muted-foreground/60" />
                                    <p class="font-medium text-foreground">Belum ada pengumuman</p>
                                    <p class="text-xs">Mulai dengan membuat pengumuman baru untuk pengguna sistem.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($announcements->hasPages())
            <div class="border-t border-border p-4 bg-muted/10">
                {{ $announcements->links() }}
            </div>
        @endif
    </div>
</div>

