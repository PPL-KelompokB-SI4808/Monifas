<?php

use Tests\TestCase;

uses(TestCase::class);

it('renders the sla monitoring page with real-time operational data', function (): void {
    $response = $this->withoutVite()->get('/dashboard/monitoring-and-analytic/sla-monitoring');

    $response->assertStatus(200);
    $response->assertSee('SLA Performance & Real-time Monitoring', false);
    $response->assertSee('Perhatian: 4 Tiket Kritis Mendekati Batas SLA');
    $response->assertSee('SLA Compliance Rate');
    $response->assertSee('96.8%');
    $response->assertSee('Tiket Tepat Waktu');
    $response->assertSee('412');
    $response->assertSee('Mendekati Batas (At Risk)');
    $response->assertSee('Terlambat (SLA Breached)');
    $response->assertSee('Matriks Target SLA Berdasarkan Keparahan', false);
    $response->assertSee('Critical (P1)');
    $response->assertSee('Kalkulasi Skor SLA Keseluruhan');
    $response->assertSee('Status Pelanggaran & Batas SLA Tiket Aktif', false);
    $response->assertSee('#TK-8941');
    $response->assertSee('AC Precision Server Down (Overheat)');
    $response->assertSee('Evaluasi & Leaderboard Performa Teknisi (SLA)', false);
    $response->assertSee('Budi Santoso');
    $response->assertSee('Beban Kerja Aktif Teknisi');
});
