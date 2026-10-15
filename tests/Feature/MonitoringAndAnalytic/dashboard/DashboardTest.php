<?php

use Tests\TestCase;

uses(TestCase::class);

it('renders the monitoring dashboard page with dummy operational data', function (): void {
    $response = $this->withoutVite()->get('/dashboard/monitoring-and-analytic/dashboard');

    $response->assertStatus(200);
    $response->assertSee('Ringkasan Kondisi Operasional');
    $response->assertSee('Filter Parameter Operasional');
    $response->assertSee('Total Laporan Aktif');
    $response->assertSee('128');
    $response->assertSee('Laporan Selesai');
    $response->assertSee('1,042');
    $response->assertSee('Fasilitas Bermasalah Kritis');
    $response->assertSee('9 Unit');
    $response->assertSee('Efektivitas Performa SLA');
    $response->assertSee('94.2%');
    $response->assertSee('Distribusi Status Laporan');
    $response->assertSee('Fasilitas Paling Sering Mengalami Kendala');
    $response->assertSee('Chiller Central Unit #02');
    $response->assertSee('Aktivitas & Laporan Penanganan Terkini', false);
    $response->assertSee('TK-2023-1092');
});
