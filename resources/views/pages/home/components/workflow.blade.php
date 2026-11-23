<section id="alur" class="border-b border-border bg-secondary">
    <div class="mx-auto max-w-6xl px-6 py-16 md:py-20">
        <div class="mb-10">
            <span class="text-xs font-semibold uppercase tracking-widest text-muted-foreground">Cara kerja</span>
            <h2 class="mt-2 text-2xl font-bold tracking-tight text-foreground" style="letter-spacing: -0.02em;">
                Tiga langkah, dari laporan ke selesai
            </h2>
        </div>

        <ol class="grid gap-6 md:grid-cols-3">
            @foreach ([
                ['01', 'Kirim laporan',          'Isi lokasi dan deskripsi kerusakan, lalu lampirkan foto sebagai bukti.'],
                ['02', 'Ditugaskan ke petugas',  'Laporan ditinjau, diberi prioritas, dan diteruskan ke petugas terkait.'],
                ['03', 'Pantau perbaikan',        'Status diperbarui secara real-time hingga perbaikan dinyatakan selesai.'],
            ] as [$num, $title, $desc])
                <li class="rounded-xl border border-border bg-background p-6"
                    style="transition: box-shadow 200ms, transform 200ms;"
                    onmouseover="this.style.boxShadow='0 8px 30px rgba(0,0,0,0.08)';this.style.transform='translateY(-2px)'"
                    onmouseout="this.style.boxShadow='none';this.style.transform='translateY(0)'">
                    <span class="text-xs font-bold uppercase tracking-widest text-primary">{{ $num }}</span>
                    <h3 class="mt-3 text-base font-semibold text-foreground">{{ $title }}</h3>
                    <p class="mt-1.5 text-sm text-muted-foreground leading-relaxed">{{ $desc }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>
