<x-layouts.dashboard title="Pusat Notifikasi">
    <div
        class="space-y-6"
        x-data="usersNotifications({
            initialTab: '{{ $currentFilter }}',
            unreadCount: {{ $unreadCount }},
            notifications: {{ \Illuminate\Support\Js::from($notifications->items()) }}
        })"
    >
        <!-- Page Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground flex items-center gap-2">
                    <x-lucide-bell class="h-5 w-5 text-primary" />
                    <span>Pusat Notifikasi Sistem</span>
                </h1>
                <p class="text-xs text-muted-foreground mt-0.5">
                    Pantau peringatan sistem, pembaruan status laporan, dan penugasan operasional MONIFAS.
                </p>
            </div>

            <!-- Global Actions -->
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    @click="markAllAsRead()"
                    :disabled="unreadCount === 0"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-background px-3 py-1.5 text-xs font-medium text-foreground hover:bg-secondary transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed shadow-2xs"
                >
                    <x-lucide-check-check class="h-3.5 w-3.5 text-primary" />
                    <span>Tandai Semua Dibaca</span>
                </button>

                <button
                    type="button"
                    @click="clearAll()"
                    :disabled="notifications.length === 0"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-red-500/20 bg-red-500/10 px-3 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-500/20 transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed shadow-2xs"
                >
                    <x-lucide-trash-2 class="h-3.5 w-3.5" />
                    <span>Bersihkan Semua</span>
                </button>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session('status'))
            <div class="flex items-center gap-2 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-xs font-medium text-emerald-600 dark:text-emerald-400 shadow-2xs">
                <x-lucide-check-circle-2 class="h-4 w-4 shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- Filter Tabs -->
        <div class="border-b border-border">
            <nav class="-mb-px flex gap-6" aria-label="Tabs">
                <button
                    type="button"
                    @click="setTab('all')"
                    class="flex items-center gap-2 border-b-2 py-3 text-xs font-medium transition-colors cursor-pointer"
                    :class="tab === 'all' ? 'border-primary text-primary font-semibold' : 'border-transparent text-muted-foreground hover:border-border hover:text-foreground'"
                >
                    <span>Semua Notifikasi</span>
                    <span
                        class="rounded-full px-2 py-0.5 text-[10px] font-semibold"
                        :class="tab === 'all' ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground'"
                        x-text="notifications.length"
                    ></span>
                </button>

                <button
                    type="button"
                    @click="setTab('unread')"
                    class="flex items-center gap-2 border-b-2 py-3 text-xs font-medium transition-colors cursor-pointer"
                    :class="tab === 'unread' ? 'border-primary text-primary font-semibold' : 'border-transparent text-muted-foreground hover:border-border hover:text-foreground'"
                >
                    <span>Belum Dibaca</span>
                    <span
                        class="rounded-full px-2 py-0.5 text-[10px] font-semibold"
                        :class="unreadCount > 0 ? 'bg-primary text-primary-foreground' : 'bg-muted text-muted-foreground'"
                        x-text="unreadCount"
                    ></span>
                </button>
            </nav>
        </div>

        <!-- Notification List Area -->
        <div class="space-y-3">
            <template x-for="item in filteredNotifications" :key="item.id">
                <div
                    class="relative flex flex-col sm:flex-row sm:items-start justify-between gap-4 rounded-xl border border-border bg-card p-4 transition-all hover:shadow-sm"
                    :class="!item.is_read ? 'border-l-4 border-l-primary bg-primary/5' : ''"
                >
                    <div class="flex items-start gap-3.5 min-w-0 flex-1">
                        <!-- Type Icon -->
                        <div
                            class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border shadow-2xs"
                            :class="getTypeBadgeClasses(item.type)"
                        >
                            <template x-if="item.type === 'report_status_changed' || item.type === 'ReportStatusChanged'">
                                <x-lucide-file-text class="h-4 w-4" />
                            </template>
                            <template x-if="item.type === 'new_assignment' || item.type === 'NewAssignment'">
                                <x-lucide-user-check class="h-4 w-4" />
                            </template>
                            <template x-if="item.type === 'sla_warning' || item.type === 'SlaWarning'">
                                <x-lucide-clock-alert class="h-4 w-4" />
                            </template>
                            <template x-if="item.type === 'system_alert' || item.type === 'SystemAlert'">
                                <x-lucide-alert-triangle class="h-4 w-4" />
                            </template>
                        </div>

                        <!-- Content Details -->
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span
                                    class="rounded-md px-2 py-0.5 text-[10px] font-semibold border"
                                    :class="getTypeBadgeClasses(item.type)"
                                    x-text="getTypeLabel(item.type)"
                                ></span>

                                <template x-if="!item.is_read">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-primary/10 px-2 py-0.5 text-[10px] font-semibold text-primary">
                                        <span class="h-1.5 w-1.5 rounded-full bg-primary"></span>
                                        <span>Baru</span>
                                    </span>
                                </template>

                                <span class="text-[11px] text-muted-foreground" x-text="formatDate(item.created_at)"></span>
                            </div>

                            <h3 class="text-sm font-semibold text-foreground leading-snug" x-text="item.title"></h3>
                            <p class="mt-1 text-xs text-muted-foreground whitespace-pre-line" x-text="item.message"></p>

                            <!-- Optional Action URL -->
                            <template x-if="item.action_url">
                                <div class="mt-2.5">
                                    <button
                                        type="button"
                                        @click="markAsRead(item.id, item.action_url)"
                                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary hover:underline cursor-pointer"
                                    >
                                        <span>Buka Tautan Rincian</span>
                                        <x-lucide-arrow-right class="h-3 w-3" />
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Individual Item Actions -->
                    <div class="flex items-center gap-2 self-end sm:self-start shrink-0 pt-2 sm:pt-0">
                        <template x-if="!item.is_read">
                            <button
                                type="button"
                                @click="markAsRead(item.id)"
                                title="Tandai sebagai sudah dibaca"
                                class="inline-flex items-center gap-1 rounded-lg border border-border bg-background px-2.5 py-1 text-xs font-medium text-foreground hover:bg-secondary transition-colors cursor-pointer shadow-2xs"
                            >
                                <x-lucide-check class="h-3.5 w-3.5 text-primary" />
                                <span>Tandai Dibaca</span>
                            </button>
                        </template>

                        <button
                            type="button"
                            @click="deleteNotification(item.id)"
                            title="Hapus notifikasi"
                            class="rounded-lg p-1.5 text-muted-foreground hover:bg-red-500/10 hover:text-red-500 transition-colors cursor-pointer"
                        >
                            <x-lucide-trash-2 class="h-4 w-4" />
                        </button>
                    </div>
                </div>
            </template>

            <!-- Empty State -->
            <div
                x-show="filteredNotifications.length === 0"
                class="rounded-xl border border-dashed border-border bg-card/50 py-12 px-4 text-center"
            >
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                    <x-lucide-bell-off class="h-6 w-6 text-muted-foreground" />
                </div>
                <h3 class="mt-3 text-sm font-semibold text-foreground">Tidak Ada Notifikasi</h3>
                <p class="mt-1 text-xs text-muted-foreground max-w-sm mx-auto">
                    <span x-show="tab === 'unread'">Tidak ada notifikasi yang belum dibaca. Semua tugas operasional Anda telah diperbarui.</span>
                    <span x-show="tab === 'all'">Belum ada notifikasi atau peringatan operasional yang tercatat untuk akun Anda.</span>
                </p>
            </div>
        </div>

        <!-- Pagination -->
        @if ($notifications->hasPages())
            <div class="pt-4 border-t border-border">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</x-layouts.dashboard>

