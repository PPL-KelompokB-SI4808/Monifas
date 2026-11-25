<?php

use App\Enums\UserRole;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('renders the monitoring dashboard page with analytics components', function (): void {
    $role = Role::factory()->create(['slug' => UserRole::Admin->value, 'name' => UserRole::Admin->label()]);
    $user = User::factory()->create(['role_id' => $role->id]);

    $response = $this->withoutVite()
        ->actingAs($user)
        ->get(route('monitoring-and-analytic.dashboard.index'));

    $response->assertStatus(200)
        ->assertSee('Ringkasan Kondisi Operasional')
        ->assertSee('Filter Parameter Operasional')
        ->assertSee('Total Laporan Aktif')
        ->assertSee('Laporan Selesai')
        ->assertSee('Aset Bermasalah')
        ->assertSee('Tugas Pemeliharaan')
        ->assertSee('Tren Volume Laporan Periodik')
        ->assertSee('Performa Penyelesaian Tugas Tim Teknisi');
});
