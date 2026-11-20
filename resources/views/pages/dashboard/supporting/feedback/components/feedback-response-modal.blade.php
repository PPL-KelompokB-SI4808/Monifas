<div
    x-data="{
        open: false,
        feedbackId: null,
        ticketNumber: '',
        adminResponse: '',
        actionUrl: '',

        init() {
            window.addEventListener('open-feedback-response-modal', (event) => {
                this.feedbackId = event.detail.id;
                this.ticketNumber = event.detail.ticket || '';
                this.actionUrl = '{{ url('dashboard/supporting/feedback') }}/' + this.feedbackId + '/respond';
                this.adminResponse = '';
                this.open = true;
            });
        },

        close() {
            this.open = false;
        }
    }"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-background/80 backdrop-blur-xs"
>
    <!-- Modal Dialog -->
    <div
        @click.away="close()"
        class="w-full max-w-lg rounded-xl border border-border bg-card p-6 shadow-xl transition-all"
    >
        <div class="flex items-center justify-between border-b border-border pb-3">
            <div>
                <h3 class="text-sm font-semibold text-foreground">Beri Tanggapan Resmi Pengelola</h3>
                <p class="text-xs text-muted-foreground mt-0.5">Tiket Laporan: <span class="font-medium text-foreground" x-text="'#' + ticketNumber"></span></p>
            </div>
            <button
                type="button"
                @click="close()"
                class="rounded-md p-1 text-muted-foreground hover:bg-secondary hover:text-foreground"
            >
                <x-lucide-x class="h-4 w-4" />
            </button>
        </div>

        <form :action="actionUrl" method="POST" class="mt-4 space-y-4">
            @csrf

            <div>
                <label for="admin_response" class="block text-xs font-medium text-foreground mb-1">
                    Tanggapan Pengelola / Tindak Lanjut <span class="text-red-500">*</span>
                </label>
                <textarea
                    name="admin_response"
                    id="admin_response"
                    rows="4"
                    x-model="adminResponse"
                    required
                    placeholder="Tuliskan ucapan terima kasih atau klarifikasi atas penanganan fasilitas terkait..."
                    class="w-full rounded-md border border-input bg-background p-3 text-xs text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                ></textarea>
                <p class="mt-1 text-2xs text-muted-foreground">
                    Catatan: Setelah tanggapan diberikan, ulasan ini akan terkunci (locked) dan pelapor tidak dapat lagi mengedit/menghapusnya.
                </p>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button
                    type="button"
                    @click="close()"
                    class="rounded-md px-3 py-1.5 text-xs font-medium border border-border text-foreground hover:bg-secondary transition-colors"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="inline-flex items-center gap-1.5 rounded-md px-4 py-1.5 text-xs font-medium bg-primary text-primary-foreground hover:bg-primary/90 transition-colors"
                >
                    <x-lucide-send class="h-3.5 w-3.5" />
                    Kirim Tanggapan
                </button>
            </div>
        </form>
    </div>
</div>
