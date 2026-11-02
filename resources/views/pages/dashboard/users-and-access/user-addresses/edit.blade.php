<x-layouts.dashboard title="Edit Alamat Pengguna">
    <div class="mx-auto max-w-3xl space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground">Edit Alamat Pengguna</h1>
                <p class="text-xs text-muted-foreground mt-0.5">Perbarui detail dan pengaturan alamat {{ $userAddress->label }}.</p>
            </div>
            <a
                href="{{ route('users-and-access.user-addresses.index') }}"
                class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-background px-3 py-1.5 text-xs font-medium text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
            >
                <x-lucide-arrow-left class="h-3.5 w-3.5" />
                <span>Kembali</span>
            </a>
        </div>

        <!-- Address Form Component -->
        <x-users-and-access-user-addresses::address-form
            :address="$userAddress"
            :users="$users"
            :action="route('users-and-access.user-addresses.update', $userAddress)"
            method="PUT"
        />
    </div>
</x-layouts.dashboard>
