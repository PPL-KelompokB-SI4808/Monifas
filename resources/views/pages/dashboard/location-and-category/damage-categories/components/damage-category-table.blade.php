@props(['categories'])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-border bg-card shadow-2xs overflow-hidden']) }}>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="border-b border-border bg-muted/40 text-muted-foreground font-medium">
                <tr>
                    <th scope="col" class="py-3 px-4">Kode</th>
                    <th scope="col" class="py-3 px-4">Nama Kategori Kerusakan</th>
                    <th scope="col" class="py-3 px-4">Tingkat Urgensi (Severity)</th>
                    <th scope="col" class="py-3 px-4">Deskripsi</th>
                    <th scope="col" class="py-3 px-4 text-center">Status</th>
                    <th scope="col" class="py-3 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border text-foreground">
                @forelse($categories as $category)
                    <tr class="hover:bg-muted/30 transition-colors">
                        <td class="py-3.5 px-4 font-mono font-semibold text-primary">
                            {{ $category->code }}
                        </td>
                        <td class="py-3.5 px-4">
                            <a
                                href="{{ route('location-and-category.damage-categories.show', $category) }}"
                                class="font-medium hover:underline text-foreground"
                            >
                                {{ $category->name }}
                            </a>
                        </td>
                        <td class="py-3.5 px-4">
                            @if($category->severity_hint)
                                @php
                                    $isUrgent = str_contains(strtolower($category->severity_hint), 'segera') || str_contains(strtolower($category->severity_hint), 'darurat');
                                @endphp
                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-medium {{ $isUrgent ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' : 'bg-muted text-muted-foreground border border-border' }}">
                                    <x-lucide-alert-triangle class="h-3 w-3 shrink-0" />
                                    <span>{{ $category->severity_hint }}</span>
                                </span>
                            @else
                                <span class="text-muted-foreground">-</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-muted-foreground max-w-xs truncate" title="{{ $category->description }}">
                            {{ $category->description ?: '-' }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @if($category->is_active)
                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-medium bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Aktif</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-medium bg-muted text-muted-foreground border border-border">
                                    <span class="h-1.5 w-1.5 rounded-full bg-muted-foreground"></span>
                                    <span>Nonaktif</span>
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <div class="inline-flex items-center justify-end gap-1">
                                <!-- Toggle Status Form -->
                                <form
                                    action="{{ route('location-and-category.damage-categories.toggle-status', $category) }}"
                                    method="POST"
                                    class="inline"
                                >
                                    @csrf
                                    @method('PATCH')
                                    <button
                                        type="submit"
                                        title="{{ $category->is_active ? 'Nonaktifkan Kategori' : 'Aktifkan Kategori' }}"
                                        class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-border text-muted-foreground hover:bg-accent hover:text-foreground transition-colors"
                                    >
                                        @if($category->is_active)
                                            <x-lucide-toggle-right class="h-4 w-4 text-emerald-500" />
                                        @else
                                            <x-lucide-toggle-left class="h-4 w-4 text-muted-foreground" />
                                        @endif
                                    </button>
                                </form>

                                <!-- Detail -->
                                <a
                                    href="{{ route('location-and-category.damage-categories.show', $category) }}"
                                    title="Lihat Detail"
                                    class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-border text-muted-foreground hover:bg-accent hover:text-foreground transition-colors"
                                >
                                    <x-lucide-eye class="h-3.5 w-3.5" />
                                </a>

                                <!-- Edit -->
                                <a
                                    href="{{ route('location-and-category.damage-categories.edit', $category) }}"
                                    title="Edit Kategori"
                                    class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-border text-muted-foreground hover:bg-accent hover:text-foreground transition-colors"
                                >
                                    <x-lucide-pencil class="h-3.5 w-3.5" />
                                </a>

                                <!-- Delete (FR-LCT-06 Soft Delete) -->
                                <form
                                    action="{{ route('location-and-category.damage-categories.destroy', $category) }}"
                                    method="POST"
                                    class="inline"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori kerusakan ini? Kategori yang memiliki keterikatan laporan akan dinonaktifkan secara aman via Soft Delete.');"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        title="Hapus Kategori"
                                        class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-red-500/20 text-red-500 hover:bg-red-500/10 transition-colors"
                                    >
                                        <x-lucide-trash-2 class="h-3.5 w-3.5" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-muted-foreground">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <x-lucide-alert-triangle class="h-8 w-8 text-muted-foreground/50" />
                                <p class="text-xs font-medium">Belum ada data kategori kerusakan ditemukan.</p>
                                <a
                                    href="{{ route('location-and-category.damage-categories.create') }}"
                                    class="inline-flex items-center gap-1.5 text-xs text-primary hover:underline font-medium"
                                >
                                    <x-lucide-plus class="h-3.5 w-3.5" />
                                    <span>Tambah Kategori Baru</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categories->hasPages())
        <div class="border-t border-border p-3">
            {{ $categories->links() }}
        </div>
    @endif
</div>
