@php
    $user = auth()->user();
    $unreadCount = $user ? $user->notifications()->where('is_read', false)->count() : 0;
    $recentNotifications = $user ? $user->notifications()->latest('id')->take(5)->get() : collect();
@endphp

<div class="relative" x-data="{ notifOpen: false }">
    <!-- Bell Button -->
    <button
        type="button"
        @click="notifOpen = !notifOpen"
        class="relative rounded-lg p-2 text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
        aria-label="Pusat Notifikasi"
        :aria-expanded="notifOpen"
    >
        <x-lucide-bell class="h-4 w-4" />
        @if ($unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-primary px-1 text-[10px] font-bold text-primary-foreground shadow-2xs">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown Panel -->
    <div
        x-show="notifOpen"
        x-cloak
        @click.away="notifOpen = false"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 top-11 z-50 w-80 sm:w-96 rounded-xl border border-border bg-background shadow-xl overflow-hidden"
    >
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-border px-4 py-3 bg-muted/30">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-foreground">Notifikasi Sistem</span>
                @if ($unreadCount > 0)
                    <span class="rounded-full bg-primary/10 px-2 py-0.5 text-[10px] font-semibold text-primary">
                        {{ $unreadCount }} Baru
                    </span>
                @endif
            </div>

            @if ($unreadCount > 0)
                <form method="POST" action="{{ route('users-and-access.notifications.mark-all-read') }}">
                    @csrf
                    <button
                        type="submit"
                        class="text-[11px] font-medium text-primary hover:underline cursor-pointer"
                    >
                        Tandai dibaca
                    </button>
                </form>
            @endif
        </div>

        <!-- Notification List -->
        <div class="max-h-80 overflow-y-auto divide-y divide-border">
            @forelse ($recentNotifications as $notification)
                <div class="p-3 transition-colors hover:bg-muted/40 {{ ! $notification->is_read ? 'bg-primary/5' : '' }}">
                    <div class="flex items-start gap-3">
                        <!-- Icon -->
                        <div class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg border {{ $notification->type->badgeClasses() }}">
                            @if ($notification->type->value === 'report_status_changed')
                                <x-lucide-file-text class="h-3.5 w-3.5" />
                            @elseif ($notification->type->value === 'new_assignment')
                                <x-lucide-user-check class="h-3.5 w-3.5" />
                            @elseif ($notification->type->value === 'sla_warning')
                                <x-lucide-clock-alert class="h-3.5 w-3.5" />
                            @else
                                <x-lucide-alert-triangle class="h-3.5 w-3.5" />
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-1">
                                <span class="text-xs font-semibold text-foreground truncate">
                                    {{ $notification->title }}
                                </span>
                                @if (! $notification->is_read)
                                    <span class="h-2 w-2 shrink-0 rounded-full bg-primary"></span>
                                @endif
                            </div>
                            <p class="mt-0.5 text-[11px] text-muted-foreground line-clamp-2">
                                {{ $notification->message }}
                            </p>
                            <div class="mt-1.5 flex items-center justify-between text-[10px] text-muted-foreground">
                                <span>{{ $notification->created_at->diffForHumans() }}</span>
                                <div class="flex items-center gap-2">
                                    @if ($notification->action_url)
                                        <a
                                            href="{{ $notification->action_url }}"
                                            class="font-medium text-primary hover:underline"
                                        >
                                            Rincian
                                        </a>
                                    @endif
                                    @if (! $notification->is_read)
                                        <form method="POST" action="{{ route('users-and-access.notifications.mark-as-read', $notification) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                type="submit"
                                                class="font-medium text-muted-foreground hover:text-foreground cursor-pointer"
                                            >
                                                Tandai
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center px-4">
                    <x-lucide-bell-off class="mx-auto h-8 w-8 text-muted-foreground/50" />
                    <p class="mt-2 text-xs font-medium text-foreground">Tidak Ada Notifikasi</p>
                    <p class="text-[11px] text-muted-foreground mt-0.5">Semua peringatan dan tugas telah diperbarui.</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        <div class="border-t border-border p-2 bg-muted/20 text-center">
            <a
                href="{{ route('users-and-access.notifications.index') }}"
                class="inline-flex items-center justify-center gap-1.5 text-xs font-medium text-primary hover:underline py-1 w-full"
            >
                <span>Lihat Semua Notifikasi</span>
                <x-lucide-arrow-right class="h-3.5 w-3.5" />
            </a>
        </div>
    </div>
</div>

