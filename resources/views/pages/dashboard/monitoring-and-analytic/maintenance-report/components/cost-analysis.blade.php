<div class="flex flex-col justify-between rounded-xl border border-border bg-card p-5 shadow-xs">
    <div>
        <!-- Header -->
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-border/60 pb-3">
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-foreground sm:text-sm">
                    Analisis Pengeluaran Biaya & Suku Cadang Teratas
                </h3>
                <p class="mt-0.5 text-xs text-muted-foreground">
                    Ringkasan pengeluaran sparepart & jasa pihak ketiga
                </p>
            </div>
            <span class="inline-flex items-center rounded-md border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 self-start sm:self-auto">
                Sisa Anggaran: Rp 31.500.000
            </span>
        </div>

        <!-- 3 Highlight Mini Cards -->
        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
            <!-- Mini Card 1 -->
            <div class="rounded-xl border border-border/80 bg-muted/20 p-3.5 space-y-1">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-muted-foreground">
                    Suku Cadang Terbanyak
                </span>
                <div class="font-bold text-xs text-foreground leading-tight">
                    Refrigerant R410A & Filter AC
                </div>
                <div class="text-sm font-extrabold text-primary pt-0.5">
                    Rp 42.800.000
                </div>
                <div class="text-[10px] text-muted-foreground">
                    32 Unit diganti / 18 Tabung
                </div>
            </div>

            <!-- Mini Card 2 -->
            <div class="rounded-xl border border-border/80 bg-muted/20 p-3.5 space-y-1">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-muted-foreground">
                    Service Kontrak Lift (Vendor)
                </span>
                <div class="font-bold text-xs text-foreground leading-tight">
                    Maintenance Schindler 4 Unit
                </div>
                <div class="text-sm font-extrabold text-primary pt-0.5">
                    Rp 38.500.000
                </div>
                <div class="text-[10px] text-muted-foreground">
                    Jasa berkala + ganti brake pad
                </div>
            </div>

            <!-- Mini Card 3 -->
            <div class="rounded-xl border border-border/80 bg-muted/20 p-3.5 space-y-1">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-muted-foreground">
                    Komponen Kelistrikan
                </span>
                <div class="font-bold text-xs text-foreground leading-tight">
                    MCB Schneider 3-Phase & Lampu LED
                </div>
                <div class="text-sm font-extrabold text-primary pt-0.5">
                    Rp 26.200.000
                </div>
                <div class="text-[10px] text-muted-foreground">
                    Gedung Utama Lt. 1-6
                </div>
            </div>
        </div>

        <!-- Cost Allocation Table -->
        <div class="mt-5 overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-border/60 text-[10px] font-bold uppercase tracking-wider text-muted-foreground">
                        <th scope="col" class="pb-2.5 pr-4 font-semibold">Kategori Pekerjaan</th>
                        <th scope="col" class="pb-2.5 px-4 font-semibold">Alokasi Biaya</th>
                        <th scope="col" class="pb-2.5 px-4 font-semibold">Proporsi</th>
                        <th scope="col" class="pb-2.5 pl-4 text-right font-semibold">Deviasi thd Budget</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/40">
                    <tr class="transition hover:bg-muted/20">
                        <td class="py-3 pr-4 font-semibold text-foreground">
                            HVAC Chiller & Ducting
                        </td>
                        <td class="py-3 px-4 text-muted-foreground">
                            Rp 56.400.000
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2">
                                <span class="w-8 text-[11px] text-muted-foreground font-medium">38%</span>
                                <div class="h-1.5 w-24 overflow-hidden rounded-full bg-muted">
                                    <div class="h-full bg-blue-600 rounded-full" style="width: 38%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 pl-4 text-right font-semibold text-emerald-600">
                            -2.1%
                        </td>
                    </tr>

                    <tr class="transition hover:bg-muted/20">
                        <td class="py-3 pr-4 font-semibold text-foreground">
                            Vertical Transport (Lift/Escalator)
                        </td>
                        <td class="py-3 px-4 text-muted-foreground">
                            Rp 44.100.000
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2">
                                <span class="w-8 text-[11px] text-muted-foreground font-medium">30%</span>
                                <div class="h-1.5 w-24 overflow-hidden rounded-full bg-muted">
                                    <div class="h-full bg-blue-600 rounded-full" style="width: 30%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 pl-4 text-right font-semibold text-slate-500">
                            0.0%
                        </td>
                    </tr>

                    <tr class="transition hover:bg-muted/20">
                        <td class="py-3 pr-4 font-semibold text-foreground">
                            MEP (Mechanical, Electrical, Plumbing)
                        </td>
                        <td class="py-3 px-4 text-muted-foreground">
                            Rp 48.000.000
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2">
                                <span class="w-8 text-[11px] text-muted-foreground font-medium">32%</span>
                                <div class="h-1.5 w-24 overflow-hidden rounded-full bg-muted">
                                    <div class="h-full bg-blue-600 rounded-full" style="width: 32%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 pl-4 text-right font-semibold text-rose-500">
                            +1.4%
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
