@props(['category' => null])

<div
    x-data="{
        severityHint: '{{ old('severity_hint', $category?->severity_hint) }}',
        setSeverity(val) {
            this.severityHint = val;
        }
    }"
    {{ $attributes->merge(['class' => 'space-y-6']) }}
>
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <!-- Kode Kategori Kerusakan -->
        <div>
            <label for="code" class="block text-xs font-medium text-foreground">
                Kode Kategori Kerusakan <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="code"
                id="code"
                required
                maxlength="30"
                value="{{ old('code', $category?->code) }}"
                placeholder="Contoh: DC-ELEC-01"
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs font-mono text-foreground uppercase placeholder:text-muted-foreground placeholder:normal-case focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            />
            <p class="mt-1 text-[11px] text-muted-foreground">Kode unik klasifikasi kerusakan (maksimal 30 karakter).</p>
            @error('code')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Nama Kategori Kerusakan -->
        <div>
            <label for="name" class="block text-xs font-medium text-foreground">
                Nama Kategori Kerusakan <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="name"
                id="name"
                required
                maxlength="150"
                value="{{ old('name', $category?->name) }}"
                placeholder="Contoh: Korsleting / Gangguan Pasokan Listrik"
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            />
            <p class="mt-1 text-[11px] text-muted-foreground">Judul/nama jenis kerusakan.</p>
            @error('name')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Tingkat Urgensi (Severity Hint) -->
    <div>
        <label for="severity_hint" class="block text-xs font-medium text-foreground">
            Indikasi Tingkat Urgensi / Severity Hint <span class="text-xs text-muted-foreground">(Opsional)</span>
        </label>
        <input
            type="text"
            name="severity_hint"
            id="severity_hint"
            maxlength="50"
            x-model="severityHint"
            placeholder="Contoh: Memerlukan Penanganan Segera"
            class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
        />
        
        <!-- Pilihan Cepat Severity -->
        <div class="mt-2 flex flex-wrap items-center gap-1.5">
            <span class="text-[11px] text-muted-foreground">Pilihan cepat:</span>
            <button
                type="button"
                @click="setSeverity('Memerlukan Penanganan Segera')"
                class="inline-flex items-center gap-1 rounded-md border border-amber-500/20 bg-amber-500/5 px-2 py-0.5 text-[11px] text-amber-600 dark:text-amber-400 hover:bg-amber-500/10 transition-colors"
            >
                <x-lucide-alert-triangle class="h-3 w-3" />
                <span>Memerlukan Penanganan Segera</span>
            </button>
            <button
                type="button"
                @click="setSeverity('Penanganan Standar')"
                class="inline-flex items-center gap-1 rounded-md border border-blue-500/20 bg-blue-500/5 px-2 py-0.5 text-[11px] text-blue-600 dark:text-blue-400 hover:bg-blue-500/10 transition-colors"
            >
                <x-lucide-clock class="h-3 w-3" />
                <span>Penanganan Standar</span>
            </button>
            <button
                type="button"
                @click="setSeverity('Dapat Ditunda')"
                class="inline-flex items-center gap-1 rounded-md border border-border bg-muted/40 px-2 py-0.5 text-[11px] text-muted-foreground hover:bg-muted transition-colors"
            >
                <x-lucide-info class="h-3 w-3" />
                <span>Dapat Ditunda</span>
            </button>
        </div>
        @error('severity_hint')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Deskripsi -->
    <div>
        <label for="description" class="block text-xs font-medium text-foreground">
            Deskripsi Kategori Kerusakan <span class="text-xs text-muted-foreground">(Opsional)</span>
        </label>
        <textarea
            name="description"
            id="description"
            rows="3"
            placeholder="Panduan gejala kerusakan, indikator penanganan, dan potensi dampak bahaya..."
            class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
        >{{ old('description', $category?->description) }}</textarea>
        @error('description')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Status Aktif -->
    <div class="flex items-center gap-3 rounded-xl border border-border bg-muted/20 p-3">
        <input
            type="checkbox"
            name="is_active"
            id="is_active"
            value="1"
            @checked(old('is_active', $category ? $category->is_active : true))
            class="h-4 w-4 rounded border-border text-primary focus:ring-ring"
        />
        <label for="is_active" class="cursor-pointer select-none">
            <span class="block text-xs font-medium text-foreground">Kategori Kerusakan Aktif</span>
            <span class="block text-[11px] text-muted-foreground">Kategori aktif dapat dipilih oleh pengguna saat mengajukan tiket pelaporan kerusakan.</span>
        </label>
    </div>
</div>
