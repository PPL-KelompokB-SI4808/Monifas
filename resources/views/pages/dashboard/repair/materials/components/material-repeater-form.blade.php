
@props([
    'repair',
    'action',
])

<form
    method="POST"
    action="{{ $action }}"
    class="space-y-6"
    x-data="materialRepeater()"
>
    @csrf

    <input type="hidden" name="repair_id" value="{{ $repair->id }}">

    <x-ui.card class="space-y-5 p-5 sm:p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-border pb-3">
            <div>
                <h2 class="text-sm font-semibold text-foreground">Daftar Material</h2>
                <p class="mt-1 text-xs text-muted-foreground">
                    Masukkan material atau suku cadang yang digunakan dalam perbaikan.
                </p>
            </div>

            <x-ui.button type="button" variant="outline" @click="addMaterial()">
                <x-lucide-plus class="h-4 w-4" />
                Tambah Material
            </x-ui.button>
        </div>

        <template x-for="(material, index) in materials" :key="material.id">
            <div class="space-y-4 rounded-xl border border-border p-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-semibold text-foreground">
                        Material <span x-text="index + 1"></span>
                    </h3>

                    <button
                        type="button"
                        @click="removeMaterial(index)"
                        x-show="materials.length > 1"
                        class="text-xs font-medium text-primary hover:underline"
                    >
                        Hapus
                    </button>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-medium text-foreground">
                            Nama Material *
                        </label>
                        <input
                            type="text"
                            :name="`materials[${index}][material_name]`"
                            x-model="material.material_name"
                            maxlength="150"
                            required
                            placeholder="Contoh: Oli Mesin"
                            class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground"
                        >
                        @error('materials.*.material_name')
                            <p class="mt-1 text-xs text-primary">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-foreground">
                            Jenis Material *
                        </label>
                        <input
                            type="text"
                            :name="`materials[${index}][material_type]`"
                            x-model="material.material_type"
                            maxlength="100"
                            required
                            placeholder="Contoh: Suku Cadang"
                            class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-foreground">
                            Satuan *
                        </label>
                        <input
                            type="text"
                            :name="`materials[${index}][unit]`"
                            x-model="material.unit"
                            maxlength="30"
                            required
                            placeholder="Contoh: Liter, Pcs, Unit"
                            class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-foreground">
                            Jumlah *
                        </label>
                        <input
                            type="number"
                            :name="`materials[${index}][quantity]`"
                            x-model.number="material.quantity"
                            min="0.01"
                            step="0.01"
                            required
                            placeholder="0"
                            class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-foreground">
                            Harga Satuan
                        </label>
                        <input
                            type="number"
                            :name="`materials[${index}][unit_price]`"
                            x-model.number="material.unit_price"
                            min="0"
                            step="0.01"
                            placeholder="0"
                            class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-foreground">
                            Subtotal
                        </label>
                        <div class="mt-1.5 rounded-lg border border-border bg-secondary px-3 py-2 text-xs font-semibold text-foreground">
                            Rp <span x-text="formatCurrency(material.quantity * material.unit_price)"></span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-foreground">
                        Catatan
                    </label>
                    <textarea
                        :name="`materials[${index}][notes]`"
                        x-model="material.notes"
                        rows="2"
                        placeholder="Catatan tambahan (opsional)"
                        class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground"
                    ></textarea>
                </div>
            </div>
        </template>

        <div class="border-t border-border pt-4">
            <div class="flex items-center justify-between text-sm">
                <span class="font-semibold text-foreground">Total Biaya Material</span>
                <span class="font-bold text-foreground">
                    Rp <span x-text="formatCurrency(totalMaterial)"></span>
                </span>
            </div>
        </div>
    </x-ui.card>

    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
        <x-ui.button
            href="{{ route('repair.materials.index', $repair->id) }}"
            variant="outline"
            class="w-full sm:w-auto"
        >
            Batal
        </x-ui.button>

        <x-ui.button type="submit" class="w-full sm:w-auto">
            Simpan Material
        </x-ui.button>
    </div>
</form>
