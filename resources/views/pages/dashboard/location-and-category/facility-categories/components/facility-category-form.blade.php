@props(['category' => null, 'defaultCategories' => []])

<div
    x-data="{
        code: '{{ old('code', $category?->code) }}',
        name: '{{ old('name', $category?->name) }}',
        description: `{{ old('description', $category?->description) }}`,
        applyPreset(presetName, presetDesc, presetCode) {
            this.name = presetName;
            this.description = presetDesc;
            if (!this.code) {
                this.code = presetCode;
            }
        }
    }"
    {{ $attributes->merge(['class' => 'space-y-6']) }}
>
    @if(!$category && !empty($defaultCategories))
        <!-- Template Cepat Kategori Fasilitas (Listrik, HVAC, Plumbing, Sipil) -->
        <div class="rounded-xl border border-primary/20 bg-primary/5 p-4">
            <div class="flex items-center gap-2 mb-2">
                <x-lucide-sparkles class="h-4 w-4 text-primary" />
                <span class="text-xs font-semibold text-foreground">Template Kategori Standar (Klik untuk Isi Otomatis)</span>
            </div>
            <div class="flex flex-wrap gap-2">
                @php
                    $codePrefixes = [
                        'Listrik' => 'FC-ELEC-01',
                        'HVAC' => 'FC-HVAC-01',
                        'Plumbing' => 'FC-PLMB-01',
                        'Sipil' => 'FC-CIVL-01',
                    ];
                @endphp
                @foreach($defaultCategories as $presetName => $presetDesc)
                    <button
                        type="button"
                        @click="applyPreset('{{ $presetName }}', '{{ $presetDesc }}', '{{ $codePrefixes[$presetName] ?? 'FC-'.strtoupper($presetName).'-01' }}')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-border bg-background hover:border-primary/50 text-xs font-medium text-foreground hover:bg-accent transition-colors"
                    >
                        <x-lucide-plus class="h-3 w-3 text-primary" />
                        <span>{{ $presetName }}</span>
                    </button>
                @endforeach
            </div>
            <p class="text-[11px] text-muted-foreground mt-2">Mendukung kategori umum seperti Listrik, HVAC, Plumbing, dan Struktur Bangunan Sipil.</p>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <!-- Kode Kategori -->
        <div>
            <label for="code" class="block text-xs font-medium text-foreground">
                Kode Kategori <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="code"
                id="code"
                x-model="code"
                required
                maxlength="30"
                placeholder="Contoh: FC-ELEC-01"
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs font-mono text-foreground uppercase placeholder:text-muted-foreground placeholder:normal-case focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            />
            <p class="mt-1 text-[11px] text-muted-foreground">Kode unik penanda kategori fasilitas (maksimal 30 karakter).</p>
            @error('code')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Nama Kategori -->
        <div>
            <label for="name" class="block text-xs font-medium text-foreground">
                Nama Kategori <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="name"
                id="name"
                x-model="name"
                required
                maxlength="150"
                placeholder="Contoh: Sistem Kelistrikan & Pencahayaan"
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            />
            <p class="mt-1 text-[11px] text-muted-foreground">Nama deskriptif jenis fasilitas.</p>
            @error('name')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Deskripsi -->
    <div>
        <label for="description" class="block text-xs font-medium text-foreground">
            Deskripsi Kategori <span class="text-xs text-muted-foreground">(Opsional)</span>
        </label>
        <textarea
            name="description"
            id="description"
            rows="3"
            x-model="description"
            placeholder="Keterangan cakupan aset, jenis alat, atau komponen yang termasuk dalam kategori ini..."
            class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
        ></textarea>
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
            <span class="block text-xs font-medium text-foreground">Kategori Aktif</span>
            <span class="block text-[11px] text-muted-foreground">Kategori yang aktif dapat dipilih saat inventarisasi fasilitas baru.</span>
        </label>
    </div>
</div>
