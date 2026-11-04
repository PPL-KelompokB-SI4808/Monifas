<x-layouts.dashboard title="Daftar Alamat Pengguna">
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground">Daftar Alamat Pengguna</h1>
                <p class="text-xs text-muted-foreground mt-0.5">Kelola data alamat fisik, domisili, dan lokasi pengiriman fasilitas pengguna.</p>
            </div>
            <a
                href="{{ route('users-and-access.user-addresses.create') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-primary px-3.5 py-2 text-xs font-medium text-primary-foreground hover:bg-primary/90 transition-colors shadow-2xs self-start sm:self-auto"
            >
                <x-lucide-plus class="h-4 w-4" />
                <span>Tambah Alamat</span>
            </a>
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

        <!-- Filter & Search Bar -->
        <div class="rounded-xl border border-border bg-card p-4 shadow-2xs">
            <form method="GET" action="{{ route('users-and-access.user-addresses.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Search Input -->
                <div class="relative">
                    <x-lucide-search class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground" />
                    <input
                        type="text"
                        name="search"
                        value="{{ $filters['search'] ?? '' }}"
                        placeholder="Cari label, gedung, alamat..."
                        class="w-full rounded-lg border border-border bg-background pl-9 pr-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                    />
                </div>

                <!-- User Filter -->
                <div>
                    <select
                        name="user_id"
                        class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                    >
                        <option value="">Semua Pengguna</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected(($filters['user_id'] ?? null) == $user->id)>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Primary Filter -->
                <div>
                    <select
                        name="is_primary"
                        class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                    >
                        <option value="">Semua Status Utama</option>
                        <option value="1" @selected(($filters['is_primary'] ?? null) === true)>Hanya Alamat Utama</option>
                        <option value="0" @selected(($filters['is_primary'] ?? null) === false)>Bukan Alamat Utama</option>
                    </select>
                </div>

                <!-- Filter Actions -->
                <div class="flex items-center gap-2">
                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-secondary px-3 py-2 text-xs font-medium text-foreground hover:bg-secondary/80 transition-colors"
                    >
                        <x-lucide-filter class="h-3.5 w-3.5" />
                        <span>Filter</span>
                    </button>
                    @if (!empty($filters['search']) || isset($filters['user_id']) || isset($filters['is_primary']))
                        <a
                            href="{{ route('users-and-access.user-addresses.index') }}"
                            class="inline-flex items-center justify-center rounded-lg border border-border px-3 py-2 text-xs font-medium text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                            title="Reset Filter"
                        >
                            <x-lucide-x class="h-3.5 w-3.5" />
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Address Cards Grid -->
        @if ($addresses->isNotEmpty())
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($addresses as $address)
                    <x-users-and-access-user-addresses::address-card :address="$address" />
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="pt-4">
                {{ $addresses->withQueryString()->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-border bg-card p-12 text-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-secondary text-muted-foreground mb-3">
                    <x-lucide-map-pin class="h-6 w-6" />
                </div>
                <h3 class="text-sm font-semibold text-foreground">Tidak Ada Alamat</h3>
                <p class="text-xs text-muted-foreground mt-1 max-w-sm">
                    Belum ada data alamat pengguna yang sesuai dengan kriteria pencarian.
                </p>
                <div class="mt-4">
                    <a
                        href="{{ route('users-and-access.user-addresses.create') }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-primary px-3 py-1.5 text-xs font-medium text-primary-foreground hover:bg-primary/90 transition-colors"
                    >
                        <x-lucide-plus class="h-3.5 w-3.5" />
                        <span>Tambah Alamat Baru</span>
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-layouts.dashboard>
