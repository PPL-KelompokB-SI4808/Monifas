
@props([
    'material',
    'action',
])

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @method('PUT')

    <x-ui.card class="space-y-5 p-5 sm:p-6">
        <div class="border-b border-border pb-3">
            <h2 class="text-sm font-semibold text-foreground">
                Informasi Material
            </h2>
            <p class="mt-1 text-xs text-muted-foreground">
                Perbarui data penggunaan material perbaikan.
            </p>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="material_name" class="block text-xs font-medium text-foreground">
                    Nama Material *
                </label>
                <input
                    type="text"
                    name="material_name"
                    id="material_name"
                    value="{{ old('material_name', $material->material_name) }}"
                    maxlength="150"
                    required
                    class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground"
                />
                @error('material_name')
                    <p class="mt-1.5 text-xs text-primary">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="material_type" class="block text-xs font-medium text-foreground">
                    Jenis Material *
                </label>
                <input
                    type="text"
                    name="material_type"
                    id="material_type"
                    value="{{ old('material_type', $material->material_type) }}"
                    maxlength="100"
                    required
                    class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground"
                />
                @error('material_type')
                    <p class="mt-1.5 text-xs text-primary">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="unit" class="block text-xs font-medium text-foreground">
                    Satuan *
                </label>
                <input
                    type="text"
                    name="unit"
                    id="unit"
                    value="{{ old('unit', $material->unit) }}"
                    maxlength="30"
                    required
                    class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground"
                />
                @error('unit')
                    <p class="mt-1.5 text-xs text-primary">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="quantity" class="block text-xs font-medium text-foreground">
                    Jumlah *
                </label>
                <input
                    type="number"
                    name="quantity"
                    id="quantity"
                    value="{{ old('quantity', $material->quantity) }}"
                    min="0.01"
                    step="0.01"
                    required
                    class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground"
                />
                @error('quantity')
                    <p class="mt-1.5 text-xs text-primary">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="unit_price" class="block text-xs font-medium text-foreground">
                    Harga Satuan
                </label>
                <input
                    type="number"
                    name="unit_price"
                    id="unit_price"
                    value="{{ old('unit_price', $material->unit_price) }}"
                    min="0"
                    step="0.01"
                    class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground"
                />
                @error('unit_price')
                    <p class="mt-1.5 text-xs text-primary">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="notes" class="block text-xs font-medium text-foreground">
                Catatan
            </label>
            <textarea
                name="notes"
                id="notes"
                rows="3"
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground"
            >{{ old('notes', $material->notes) }}</textarea>
            @error('notes')
                <p class="mt-1.5 text-xs text-primary">{{ $message }}</p>
            @enderror
        </div>
    </x-ui.card>

    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
        <x-ui.button
            href="{{ route('repair.materials.index', $material->repair_id) }}"
            variant="outline"
            class="w-full sm:w-auto"
        >
            Batal
        </x-ui.button>

        <x-ui.button type="submit" class="w-full sm:w-auto">
            Simpan Perubahan
        </x-ui.button>
    </div>
</form>
