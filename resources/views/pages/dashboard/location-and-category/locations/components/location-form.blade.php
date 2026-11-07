@props(['location' => null, 'parents' => [], 'regions' => []])

<div {{ $attributes->merge(['class' => 'space-y-5']) }}>
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <!-- Kode Lokasi -->
        <div>
            <label for="code" class="block text-xs font-medium text-foreground">
                Kode Lokasi <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="code"
                id="code"
                required
                maxlength="30"
                value="{{ old('code', $location?->code) }}"
                placeholder="Contoh: LOC-BDG-01"
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs font-mono text-foreground uppercase placeholder:text-muted-foreground placeholder:normal-case focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            />
            <p class="mt-1 text-[11px] text-muted-foreground">Kode unik penanda lokasi/kawasan (maksimal 30 karakter).</p>
            @error('code')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Nama Lokasi -->
        <div>
            <label for="name" class="block text-xs font-medium text-foreground">
                Nama Lokasi <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="name"
                id="name"
                required
                maxlength="150"
                value="{{ old('name', $location?->name) }}"
                placeholder="Contoh: Kampus Utama Telkom University"
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            />
            @error('name')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <!-- Induk Kawasan (Parent) -->
        <div>
            <label for="parent_id" class="block text-xs font-medium text-foreground">
                Induk Kawasan <span class="text-xs text-muted-foreground">(Opsional)</span>
            </label>
            <select
                name="parent_id"
                id="parent_id"
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            >
                <option value="">Tidak Ada (Jadikan Lokasi Induk Utama / Root)</option>
                @foreach($parents as $parent)
                    <option value="{{ $parent->id }}" @selected(old('parent_id', $location?->parent_id) == $parent->id)>
                        {{ $parent->name }} ({{ $parent->code }})
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-[11px] text-muted-foreground">Pilih lokasi induk jika kawasan ini merupakan sub-kawasan.</p>
            @error('parent_id')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Wilayah / Region -->
        <div>
            <label for="region" class="block text-xs font-medium text-foreground">
                Wilayah / Region <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="region"
                id="region"
                required
                maxlength="100"
                list="region-list"
                value="{{ old('region', $location?->region) }}"
                placeholder="Contoh: Bandung, Jawa Barat"
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            />
            <datalist id="region-list">
                @foreach($regions as $reg)
                    <option value="{{ $reg }}"></option>
                @endforeach
            </datalist>
            @error('region')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Alamat Lengkap -->
    <div>
        <label for="address" class="block text-xs font-medium text-foreground">
            Alamat Lengkap <span class="text-red-500">*</span>
        </label>
        <textarea
            name="address"
            id="address"
            rows="3"
            required
            placeholder="Masukkan rincian alamat fisik jalan, kelurahan, kecamatan, dan kode pos..."
            class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
        >{{ old('address', $location?->address) }}</textarea>
        @error('address')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Deskripsi Lokasi -->
    <div>
        <label for="description" class="block text-xs font-medium text-foreground">
            Deskripsi / Catatan Tambahan <span class="text-xs text-muted-foreground">(Opsional)</span>
        </label>
        <textarea
            name="description"
            id="description"
            rows="3"
            placeholder="Catatan tambahan mengenai luas wilayah, batas area, atau fungsi kawasan..."
            class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
        >{{ old('description', $location?->description) }}</textarea>
        @error('description')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Status Keaktifan -->
    <div class="rounded-lg border border-border bg-secondary/30 p-3.5">
        <label class="flex items-center gap-2.5 cursor-pointer">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                @checked(old('is_active', $location ? $location->is_active : true))
                class="h-4 w-4 rounded border-border text-primary focus:ring-ring"
            />
            <div>
                <span class="text-xs font-medium text-foreground">Kawasan Aktif</span>
                <p class="text-[11px] text-muted-foreground">Lokasi yang aktif dapat dipilih dalam pendaftaran gedung dan penempatan fasilitas baru.</p>
            </div>
        </label>
        @error('is_active')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>
</div>
