<?php

use App\Enums\UserRole;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('renders the maintenance report page with dummy analytics data', function (): void {
    $role = Role::factory()->create(['slug' => UserRole::Admin->value, 'name' => UserRole::Admin->label()]);
    $user = User::factory()->create(['role_id' => $role->id]);

    $response = $this->withoutVite()
        ->actingAs($user)
        ->get(route('monitoring-and-analytic.maintenance-report.index'));

    $response->assertStatus(200);
    $response->assertSee('Laporan & Analisis Pemeliharaan', false);
    $response->assertSee('Total Biaya Bulan Ini');
    $response->assertSee('Rp 148.500.000');
    $response->assertSee('Rata-rata MTTR (Penyelesaian)');
    $response->assertSee('2 Jam 45 Menit');
    $response->assertSee('Rasio Preventif vs Korektif');
    $response->assertSee('68%');
    $response->assertSee('32%');
    $response->assertSee('Total Tiket Bulan Ini');
    $response->assertSee('142');
    $response->assertSee('Frekuensi Kerusakan Fasilitas');
    $response->assertSee('HVAC / Tata Udara (AC Central)', false);
    $response->assertSee('Analisis Pengeluaran Biaya & Suku Cadang Teratas', false);
    $response->assertSee('Refrigerant R410A & Filter AC', false);
    $response->assertSee('Riwayat Aktivitas Pemeliharaan & Perbaikan', false);
    $response->assertSee('WO-2024-1089');
    $response->assertSee('AC Central Chiller #02');
});
