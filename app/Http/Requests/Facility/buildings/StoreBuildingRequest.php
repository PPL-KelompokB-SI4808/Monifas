<?php

namespace Tests\Feature\Facility\Buildings;

use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuildingCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_buildings_index()
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
             ->get(route('facility.buildings.index'))
             ->assertStatus(200);
    }

    public function test_admin_can_create_building()
    {
        $admin = User::factory()->create();
        
        $location = Location::create([
            'code' => 'LOC-01',
            'name' => 'Lokasi Utama',
        ]);

        $response = $this->actingAs($admin)
             ->post(route('facility.buildings.store'), [
                 'location_id' => $location->id,
                 'code' => 'BLD-01',
                 'name' => 'Gedung Utama',
                 'total_floors' => 5,
                 'is_active' => true,
             ]);

        // Mencetak error sesi/validasi ke terminal jika pengujian gagal
        $response->dumpSession();

        $response->assertRedirect(route('facility.buildings.index'));
        $this->assertDatabaseHas('buildings', ['code' => 'BLD-01']);
    }
}