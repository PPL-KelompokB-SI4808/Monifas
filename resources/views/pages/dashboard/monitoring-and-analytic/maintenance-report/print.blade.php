<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Pemeliharaan - MONIFAS</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-background text-foreground p-6 print:p-0">
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex items-start justify-between border-b border-border pb-4">
            <div>
                <h1 class="font-display text-xl font-bold text-foreground">Laporan &amp; Analisis Pemeliharaan</h1>
                <p class="text-xs text-muted-foreground">
                    Dicetak pada: {{ $generatedAt->format('d M Y H:i') }} WIB
                </p>
            </div>
            <x-ui.button type="button" size="sm" onclick="window.print()" class="print:hidden">
                Cetak / Simpan PDF
            </x-ui.button>
        </div>

        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
            <x-ui.card class="p-4">
                <p class="text-xs text-muted-foreground">Total Perbaikan</p>
                <p class="mt-1 text-lg font-bold text-foreground">{{ number_format((int) ($summary['total_repairs'] ?? 0)) }}</p>
            </x-ui.card>
            <x-ui.card class="p-4">
                <p class="text-xs text-muted-foreground">Selesai</p>
                <p class="mt-1 text-lg font-bold text-success">{{ number_format((int) ($summary['completed_repairs'] ?? 0)) }}</p>
            </x-ui.card>
            <x-ui.card class="p-4">
                <p class="text-xs text-muted-foreground">Total Biaya Material</p>
                <p class="mt-1 text-lg font-bold text-foreground">Rp {{ number_format((float) ($summary['total_cost'] ?? 0), 0, ',', '.') }}</p>
            </x-ui.card>
            <x-ui.card class="p-4">
                <p class="text-xs text-muted-foreground">Rata-rata Durasi</p>
                <p class="mt-1 text-lg font-bold text-foreground">{{ number_format((float) ($summary['avg_duration_hours'] ?? 0), 1) }} jam</p>
            </x-ui.card>
        </div>

        <x-ui.card class="p-4">
            <h2 class="mb-3 text-sm font-bold text-foreground">Riwayat Pemeliharaan &amp; Perbaikan</h2>
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-border text-muted-foreground">
                        <th class="py-2 pr-3">ID</th>
                        <th class="py-2 pr-3">Tanggal</th>
                        <th class="py-2 pr-3">Fasilitas</th>
                        <th class="py-2 pr-3">Teknisi</th>
                        <th class="py-2 pr-3">Status</th>
                        <th class="py-2 text-right">Biaya Material</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($records as $record)
                        <tr>
                            <td class="py-2 pr-3 font-mono">REP-{{ $record->id }}</td>
                            <td class="py-2 pr-3">{{ $record->started_at->format('d M Y') }}</td>
                            <td class="py-2 pr-3">{{ $record->facility?->name ?? '-' }}</td>
                            <td class="py-2 pr-3">{{ $record->officer?->user?->name ?? '-' }}</td>
                            <td class="py-2 pr-3">{{ $record->status->value }}</td>
                            <td class="py-2 text-right">Rp {{ number_format((float) $record->materials->sum('subtotal'), 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-muted-foreground">Tidak ada data pemeliharaan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-ui.card>
    </div>
</body>
</html>
