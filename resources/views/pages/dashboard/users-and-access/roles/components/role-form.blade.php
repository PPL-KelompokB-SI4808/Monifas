@props([
    'role' => null,
    'action',
    'method' => 'POST',
])

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if (in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE']))
        @method($method)
    @endif

    <div class="rounded-xl border border-border bg-background p-6 shadow-2xs space-y-5">
        <h3 class="text-sm font-semibold text-foreground pb-3 border-b border-border">
            {{ $role ? 'Perbarui Konfigurasi Peran' : 'Definisi Peran Baru' }}
        </h3>

        <!-- Role Name -->
        <div>
            <label for="name" class="block text-xs font-medium text-foreground">
                Nama Peran <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="name"
                id="name"
                value="{{ old('name', $role?->name) }}"
                required
                placeholder="Contoh: Teknisi Elektrikal"
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            />
            @error('name')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Role Slug -->
        <div>
            <label for="slug" class="block text-xs font-medium text-foreground">
                Identifier / Slug <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="slug"
                id="slug"
                value="{{ old('slug', $role?->slug) }}"
                required
                placeholder="contoh: teknisi_elektrikal"
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs font-mono text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            />
            <p class="mt-1 text-[11px] text-muted-foreground">Hanya huruf, angka, tanda hubung (-), dan garis bawah (_).</p>
            @error('slug')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Role Description -->
        <div>
            <label for="description" class="block text-xs font-medium text-foreground">
                Deskripsi Peran & Tanggung Jawab
            </label>
            <textarea
                name="description"
                id="description"
                rows="3"
                placeholder="Jelaskan cakupan wewenang atau tanggung jawab peran ini..."
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            >{{ old('description', $role?->description) }}</textarea>
            @error('description')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Actions -->
    <div class="flex items-center justify-end gap-3">
        <a
            href="{{ route('users-and-access.roles.index') }}"
            class="inline-flex items-center justify-center rounded-lg border border-border bg-background px-4 py-2 text-xs font-medium text-foreground hover:bg-secondary transition-colors"
        >
            Batal
        </a>
        <button
            type="submit"
            class="inline-flex items-center justify-center rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground shadow-2xs hover:bg-primary/90 transition-colors cursor-pointer"
        >
            {{ $role ? 'Perbarui Peran' : 'Simpan Peran Baru' }}
        </button>
    </div>
</form>
