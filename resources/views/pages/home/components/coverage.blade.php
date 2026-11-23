<section id="cakupan" class="border-b border-border bg-background">
    <div class="mx-auto max-w-6xl px-6 py-16 md:py-20">
        <div class="mb-10">
            <span class="text-xs font-semibold uppercase tracking-widest text-muted-foreground">Cakupan</span>
            <h2 class="mt-2 text-2xl font-bold tracking-tight text-foreground" style="letter-spacing: -0.02em;">
                Fasilitas yang bisa dilaporkan
            </h2>
        </div>

        <dl class="grid gap-px border border-border sm:grid-cols-2 lg:grid-cols-3" style="border-radius: 12px; overflow: hidden;">
            @foreach ([
                ['Jalan & Trotoar',          'Lubang, retakan, permukaan rusak.'],
                ['Penerangan Jalan',         'Lampu padam atau cahaya redup.'],
                ['Drainase',                 'Saluran tersumbat dan genangan air.'],
                ['Taman & Area Publik',      'Bangku, taman, dan fasilitas rekreasi.'],
                ['Jembatan Penyeberangan',   'Kerusakan struktur dan pagar.'],
                ['Rambu & Lampu Lalu Lintas','Rambu hilang atau lampu lalu lintas mati.'],
            ] as [$title, $desc])
                <div class="bg-background p-6 transition-colors duration-150 hover:bg-secondary">
                    <dt class="text-sm font-semibold text-foreground">{{ $title }}</dt>
                    <dd class="mt-1 text-sm text-muted-foreground">{{ $desc }}</dd>
                </div>
            @endforeach
        </dl>
    </div>
</section>
