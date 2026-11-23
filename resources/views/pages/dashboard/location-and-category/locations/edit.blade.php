<x-layouts.dashboard title="Ubah Lokasi">
    <div class="max-w-3xl mx-auto space-y-6">

        {{-- Page Header --}}
        <div class="flex items-center gap-3">
            <a
                href="{{ route('location-and-category.locations.index') }}"
                class="inline-flex items-center gap-1.5 rounded-[6px] border border-border bg-background px-3 py-1.5 text-xs font-medium text-muted-foreground transition-all hover:bg-secondary hover:text-foreground hover:-translate-y-px"
            >
                <x-lucide-arrow-left class="h-3.5 w-3.5" />
                <span>Kembali</span>
            </a>
            <div>
                <h1 class="text-xl font-bold text-foreground" style="letter-spacing: -0.03em;">Ubah Lokasi: {{ $location->name }}</h1>
                <p class="mt-0.5 text-xs text-muted-foreground">Perbarui data wilayah, hierarki kawasan, atau status keaktifan.</p>
            </div>
        </div>

        {{-- Form Card --}}
        <div class="rounded-xl border border-border bg-background p-6">
            <form action="{{ route('location-and-category.locations.update', $location) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="rounded-[8px] border border-error/20 bg-error/10 px-4 py-3 text-sm font-medium text-error space-y-1">
                        @foreach ($errors->all() as $error)
                            <p class="flex items-center gap-2">
                                <x-lucide-alert-circle class="h-4 w-4 shrink-0" />
                                <span>{{ $error }}</span>
                            </p>
                        @endforeach
                    </div>
                @endif

                <div class="border-b border-border pb-3">
                    <h3 class="text-sm font-semibold text-foreground flex items-center gap-2">
                        <x-lucide-pencil class="h-4 w-4 text-primary" />
                        <span>Informasi Kawasan</span>
                    </h3>
                </div>

                <x-location-and-category-locations::location-form
                    :location="$location"
                    :parents="$parents"
                    :regions="$regions"
                />

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-border">
                    <a
                        href="{{ route('location-and-category.locations.index') }}"
                        class="inline-flex items-center gap-1.5 rounded-[6px] border border-border bg-background h-[38px] px-4 text-sm font-medium text-muted-foreground transition-all hover:bg-secondary hover:text-foreground"
                    >
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="inline-flex items-center gap-1.5 rounded-[6px] h-[38px] px-4 text-sm font-medium bg-primary text-primary-foreground transition-all hover:bg-primary-hover hover:shadow-[0_4px_12px_rgba(99,102,241,0.35)] hover:-translate-y-px active:translate-y-0 cursor-pointer"
                    >
                        <x-lucide-save class="h-4 w-4" />
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-layouts.dashboard>
