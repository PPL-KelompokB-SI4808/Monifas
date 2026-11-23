<section>
    <div class="flex items-center gap-2 border-b border-red-500/20 pb-4">
        <x-lucide-triangle-alert class="h-4 w-4 text-red-600" />
        <h2 class="text-sm font-semibold text-red-700">Hapus Akun</h2>
    </div>

    <p class="mt-4 text-xs leading-relaxed text-muted-foreground">
        Setelah akun dihapus, seluruh data terkait akan dihapus secara permanen. Pastikan Anda sudah mengunduh data penting sebelum melanjutkan.
    </p>

    <div class="mt-4">
        <button
            type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="inline-flex h-9 items-center gap-1.5 rounded-lg border border-red-500/30 bg-red-500/10 px-4 text-xs font-medium text-red-600 transition-colors hover:bg-red-500/20 cursor-pointer"
        >
            <x-lucide-trash-2 class="h-3.5 w-3.5" />
            Hapus Akun Saya
        </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="POST" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <div class="flex items-start gap-3">
                <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-500/10 text-red-600">
                    <x-lucide-triangle-alert class="h-4 w-4" />
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-foreground">Hapus akun secara permanen?</h3>
                    <p class="mt-1 text-xs text-muted-foreground leading-relaxed">
                        Tindakan ini tidak dapat dibatalkan. Semua data Anda akan dihapus selamanya.
                    </p>
                </div>
            </div>

            <div class="mt-5">
                <x-input-label for="confirm_delete_password" value="Konfirmasi dengan password Anda" />
                <x-text-input
                    id="confirm_delete_password"
                    name="password"
                    type="password"
                    class="mt-1.5 w-full"
                    placeholder="••••••••" />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1.5" />
            </div>

            <div class="mt-5 flex items-center justify-end gap-2 border-t border-border pt-4">
                <button
                    type="button"
                    x-on:click="$dispatch('close')"
                    class="inline-flex h-9 items-center rounded-lg border border-border bg-background px-4 text-xs font-medium text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground cursor-pointer"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-red-600 px-4 text-xs font-medium text-white transition-colors hover:bg-red-700 cursor-pointer"
                >
                    <x-lucide-trash-2 class="h-3.5 w-3.5" />
                    Ya, Hapus Akun
                </button>
            </div>
        </form>
    </x-modal>
</section>
