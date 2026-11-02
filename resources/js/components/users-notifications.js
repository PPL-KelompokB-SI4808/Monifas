export default function usersNotifications(config = {}) {
    return {
        tab: config.initialTab || 'all',
        unreadCount: config.unreadCount || 0,
        notifications: config.notifications || [],
        isLoading: false,

        get csrfToken() {
            return (
                document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                config.csrfToken ||
                ''
            );
        },

        get filteredNotifications() {
            if (this.tab === 'unread') {
                return this.notifications.filter((item) => !item.is_read);
            }
            return this.notifications;
        },

        setTab(newTab) {
            this.tab = newTab;
        },

        getTypeLabel(type) {
            const map = {
                ReportStatusChanged: 'Status Laporan Berubah',
                report_status_changed: 'Status Laporan Berubah',
                NewAssignment: 'Penugasan Baru',
                new_assignment: 'Penugasan Baru',
                SlaWarning: 'Peringatan SLA',
                sla_warning: 'Peringatan SLA',
                SystemAlert: 'Peringatan Sistem',
                system_alert: 'Peringatan Sistem',
            };
            return map[type] || 'Notifikasi';
        },

        getTypeBadgeClasses(type) {
            const map = {
                ReportStatusChanged: 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20',
                report_status_changed: 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20',
                NewAssignment: 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20',
                new_assignment: 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20',
                SlaWarning: 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
                sla_warning: 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
                SystemAlert: 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
                system_alert: 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
            };
            return map[type] || 'bg-secondary text-foreground border-border';
        },

        formatDate(dateString) {
            if (!dateString) return '';
            try {
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                });
            } catch (e) {
                return dateString;
            }
        },

        async markAsRead(id, actionUrl = null) {
            const index = this.notifications.findIndex((n) => n.id === id);
            if (index === -1) return;

            const wasUnread = !this.notifications[index].is_read;
            if (wasUnread) {
                this.notifications[index].is_read = true;
                this.notifications[index].read_at = new Date().toISOString();
                this.unreadCount = Math.max(0, this.unreadCount - 1);
            }

            try {
                const response = await fetch(`/dashboard/users-and-access/notifications/${id}/read`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                    },
                });

                if (!response.ok) {
                    throw new Error('Gagal menandai notifikasi');
                }

                if (actionUrl) {
                    window.location.href = actionUrl;
                }
            } catch (error) {
                console.error('Gagal memperbarui status notifikasi:', error);
                if (wasUnread) {
                    this.notifications[index].is_read = false;
                    this.notifications[index].read_at = null;
                    this.unreadCount++;
                }
            }
        },

        async markAllAsRead() {
            if (this.unreadCount === 0) return;

            const prevNotifications = JSON.parse(JSON.stringify(this.notifications));
            const prevCount = this.unreadCount;

            this.notifications.forEach((item) => {
                item.is_read = true;
                item.read_at = new Date().toISOString();
            });
            this.unreadCount = 0;

            try {
                const response = await fetch('/dashboard/users-and-access/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                    },
                });

                if (!response.ok) {
                    throw new Error('Gagal menandai semua notifikasi');
                }
            } catch (error) {
                console.error('Gagal menandai semua notifikasi:', error);
                this.notifications = prevNotifications;
                this.unreadCount = prevCount;
            }
        },

        async deleteNotification(id) {
            const item = this.notifications.find((n) => n.id === id);
            if (!item) return;

            const prevNotifications = [...this.notifications];
            const wasUnread = !item.is_read;

            this.notifications = this.notifications.filter((n) => n.id !== id);
            if (wasUnread) {
                this.unreadCount = Math.max(0, this.unreadCount - 1);
            }

            try {
                const response = await fetch(`/dashboard/users-and-access/notifications/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                    },
                });

                if (!response.ok) {
                    throw new Error('Gagal menghapus notifikasi');
                }
            } catch (error) {
                console.error('Gagal menghapus notifikasi:', error);
                this.notifications = prevNotifications;
                if (wasUnread) {
                    this.unreadCount++;
                }
            }
        },

        async clearAll() {
            if (this.notifications.length === 0) return;

            const prevNotifications = [...this.notifications];
            const prevCount = this.unreadCount;

            this.notifications = [];
            this.unreadCount = 0;

            try {
                const response = await fetch('/dashboard/users-and-access/notifications/clear-all', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                    },
                });

                if (!response.ok) {
                    throw new Error('Gagal membersihkan notifikasi');
                }
            } catch (error) {
                console.error('Gagal membersihkan notifikasi:', error);
                this.notifications = prevNotifications;
                this.unreadCount = prevCount;
            }
        },
    };
}

