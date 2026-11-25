<?php

use App\Enums\UserRole;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('renders the maintenance report page with analytics components', function (): void {
    $role = Role::factory()->create(['slug' => UserRole::Admin->value, 'name' => UserRole::Admin->label()]);
    $user = User::factory()->create(['role_id' => $role->id]);

    $response = $this->withoutVite()
        ->actingAs($user)
        ->get(route('monitoring-and-analytic.maintenance-report.index'));

    $response->assertStatus(200)
        ->assertSee('Laporan &amp; Analisis Pemeliharaan', false)
        ->assertSee('Ekspor / Cetak Laporan')
        ->assertSee('Total Perbaikan')
        ->assertSee('Perbaikan Selesai')
        ->assertSee('Total Biaya Material')
        ->assertSee('Preventive vs Reaktif / Darurat')
        ->assertSee('Frekuensi Kerusakan Fasilitas')
        ->assertSee('Analisis Biaya Perbaikan &amp; Konsumsi Material', false)
        ->assertSee('Riwayat Pemeliharaan &amp; Perbaikan', false);
});
