<x-layouts.dashboard title="Manajemen Pengguna">
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground">Daftar Pengguna Sistem</h1>
                <p class="text-xs text-muted-foreground mt-0.5">Kelola akun pengguna, peran akses, dan status operasional MONIFAS.</p>
            </div>
        </div>

        <!-- Flash Status Message -->
        @if (session('status'))
            <div class="flex items-center gap-2 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-xs font-medium text-emerald-600 dark:text-emerald-400 shadow-2xs">
                <x-lucide-check-circle-2 class="h-4 w-4 shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-xs font-medium text-red-600 dark:text-red-400 shadow-2xs space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="flex items-center gap-2">
                        <x-lucide-alert-circle class="h-4 w-4 shrink-0" />
                        <span>{{ $error }}</span>
                    </p>
                @endforeach
            </div>
        @endif

        <!-- Users Table Component -->
        <x-users-and-access-users::user-table
            :users="$users"
            :roles="$roles"
            :filters="$filters"
        />
    </div>
</x-layouts.dashboard>
