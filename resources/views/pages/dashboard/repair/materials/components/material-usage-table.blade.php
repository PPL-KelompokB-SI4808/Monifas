
@props([
    'materials',
    'totalMaterial',
    'repair',
])

<div class="space-y-4">
    <div class="grid gap-4 sm:grid-cols-2">
        <x-ui.card class="p-4">
            <p class="text-xs font-medium text-muted-foreground">
                Total Material Digunakan
            </p>
            <p class="mt-1 text-lg font-bold text-foreground">
                {{ $materials->count() }} item
            </p>
        </x-ui.card>

        <x-ui.card class="p-4">
            <p class="text-xs font-medium text-muted-foreground">
                Total Biaya Material
            </p>
            <p class="mt-1 text-lg font-bold text-foreground">
                Rp {{ number_format($totalMaterial, 0, ',', '.') }}
            </p>
        </x-ui.card>
    </div>

    <div class="overflow-hidden rounded-xl border border-border bg-background shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="border-b border-border bg-secondary/40 font-medium text-muted-foreground">
                    <tr>
                        <th class="py-3 pl-4 pr-3 sm:pl-6">Material</th>
                        <th class="px-3 py-3">Jenis</th>
                        <th class="px-3 py-3">Jumlah</th>
                        <th class="px-3 py-3">Harga Satuan</th>
                        <th class="px-3 py-3">Subtotal</th>
                        <th class="py-3 pl-3 pr-4 text-right sm:pr-6">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-border">
                    @forelse ($materials as $material)
                        <tr class="hover:bg-muted/30">
                            <td class="py-3.5 pl-4 pr-3 sm:pl-6">
                                <div class="flex flex-col gap-0.5">
                                    <span class="font-semibold text-foreground">
                                        {{ $material->material_name }}
                                    </span>
                                    <span class="text-[11px] text-muted-foreground">
                                        {{ $material->notes ?: 'Tidak ada catatan' }}
                                    </span>
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-3 py-3.5 text-muted-foreground">
                                {{ $material->material_type }}
                            </td>

                            <td class="whitespace-nowrap px-3 py-3.5 text-foreground">
                                {{ number_format((float) $material->quantity, 2, ',', '.') }}
                                {{ $material->unit }}
                            </td>

                            <td class="whitespace-nowrap px-3 py-3.5 text-foreground">
                                Rp {{ number_format((float) $material->unit_price, 0, ',', '.') }}
                            </td>

                            <td class="whitespace-nowrap px-3 py-3.5 font-semibold text-foreground">
                                Rp {{ number_format((float) $material->subtotal, 0, ',', '.') }}
                            </td>

                            <td class="whitespace-nowrap py-3.5 pl-3 pr-4 text-right sm:pr-6">
                                <div class="flex items-center justify-end gap-1">
                                    <a
                                        href="{{ route('repair.materials.edit', $material) }}"
                                        class="rounded-md p-1.5 text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground"
                                        title="Edit material"
                                    >
                                        <x-lucide-pencil class="h-4 w-4" />
                                        <span class="sr-only">Edit material</span>
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('repair.materials.destroy', $material) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus material ini?');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="rounded-md p-1.5 text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground"
                                            title="Hapus material"
                                        >
                                            <x-lucide-trash-2 class="h-4 w-4" />
                                            <span class="sr-only">Hapus material</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="mx-auto flex max-w-xs flex-col items-center">
                                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-muted/60 text-muted-foreground">
                                        <x-lucide-package class="h-6 w-6" />
                                    </span>

                                    <h2 class="mt-3 text-sm font-semibold text-foreground">
                                        Belum ada material
                                    </h2>

                                    <p class="mt-1 text-xs text-muted-foreground">
                                        Belum ada material yang dicatat untuk perbaikan ini.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-border bg-secondary/30 px-4 py-4 sm:px-6">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-foreground">
                    Total Biaya Material
                </span>
                <span class="text-sm font-bold text-foreground">
                    Rp {{ number_format($totalMaterial, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>
</div>
