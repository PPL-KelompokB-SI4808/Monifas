<section id="beranda" class="relative overflow-hidden border-b border-border bg-background">
    {{-- Dot grid decoration --}}
    <div class="pointer-events-none absolute inset-0"
         style="background-image: radial-gradient(circle, #e8e8ec 1px, transparent 1px); background-size: 28px 28px; opacity: 0.6;"></div>

    <div class="relative mx-auto max-w-6xl px-6 py-20 md:py-32">
        <span class="inline-block rounded-sm bg-secondary px-2 py-0.5 text-xs font-semibold uppercase tracking-widest text-secondary-foreground">
            Platform Pelaporan Fasilitas
        </span>

        <h1 class="mt-5 max-w-3xl text-4xl font-bold leading-tight tracking-tight text-foreground md:text-6xl"
            style="letter-spacing: -0.03em;">
            Laporkan kerusakan.<br>Pantau sampai tuntas.
        </h1>

        <p class="mt-5 max-w-lg text-base text-muted-foreground">
            MONIFAS meneruskan setiap laporan ke petugas yang tepat dan menampilkan status perbaikannya secara real-time.
        </p>

        <div class="mt-8 flex flex-wrap gap-3">
            <x-ui.button href="#laporan" class="h-10 px-5 text-sm">
                Buat Laporan
            </x-ui.button>
            <x-ui.button href="#alur" variant="outline" class="h-10 px-5 text-sm">
                Cara kerja
            </x-ui.button>
        </div>

        <dl class="mt-14 grid w-fit gap-x-12 gap-y-6 sm:grid-cols-3">
            @foreach ([
                ['24 Jam', 'Respons pertama'],
                ['500+', 'Laporan selesai'],
                ['12 Wilayah', 'Cakupan area'],
            ] as [$stat, $label])
                <div>
                    <dt class="text-2xl font-bold text-foreground" style="letter-spacing: -0.02em;">{{ $stat }}</dt>
                    <dd class="mt-0.5 text-sm text-muted-foreground">{{ $label }}</dd>
                </div>
            @endforeach
        </dl>
    </div>
</section>
