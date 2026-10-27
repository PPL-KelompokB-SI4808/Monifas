<x-layouts.dashboard title="Edit Peran">
    <div class="mx-auto max-w-3xl space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground">Edit Peran: {{ $role->name }}</h1>
                <p class="text-xs text-muted-foreground mt-0.5">Perbarui konfigurasi peran dan hak akses sistem.</p>
            </div>
            <a
                href="{{ route('users-and-access.roles.index') }}"
                class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-background px-3 py-1.5 text-xs font-medium text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
            >
                <x-lucide-arrow-left class="h-3.5 w-3.5" />
                <span>Kembali</span>
            </a>
        </div>

        <!-- Role Form Component -->
        <x-users-and-access-roles::role-form
            :role="$role"
            :action="route('users-and-access.roles.update', $role)"
            method="PUT"
        />
    </div>
</x-layouts.dashboard>

