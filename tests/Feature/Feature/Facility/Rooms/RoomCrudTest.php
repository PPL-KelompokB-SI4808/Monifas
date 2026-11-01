<?php

namespace Tests\Feature\Facility\Rooms;

use App\Models\Building;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_rooms_index()
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
             ->get(route('facility.rooms.index'))
             ->assertStatus(200);
    }

    public function test_admin_can_create_room()
    {
        $admin = User::factory()->create();
        
        // Buat data gedung terlebih dahulu untuk relasi foreign key
        $building = Building::create([
            'code' => 'BLD-01',
            'name' => 'Gedung Utama',
            'total_floors' => 5,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)
             ->post(route('facility.rooms.store'), [
                 'building_id' => $building->id,
                 'code' => 'ROOM-01',
                 'name' => 'Ruang Kelas 101',
                 'floor' => 1,
                 'capacity' => 30,
                 'description' => 'Ruangan perkuliahan lantai 1',
                 'is_active' => true,
             ]);

        // Memastikan redirect kembali ke halaman index rooms setelah sukses
        $response->assertRedirect(route('facility.rooms.index'));
        
        // Memastikan data tersimpan di database
        $this->assertDatabaseHas('rooms', ['code' => 'ROOM-01']);
    }

    public function test_admin_can_update_room()
    {
        $admin = User::factory()->create();

        $building = Building::create([
            'code' => 'BLD-01',
            'name' => 'Gedung Utama',
            'total_floors' => 5,
            'is_active' => true,
        ]);

        $room = Room::create([
            'building_id' => $building->id,
            'code' => 'ROOM-01',
            'name' => 'Ruang Lama',
            'floor' => 1,
            'capacity' => 20,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)
             ->put(route('facility.rooms.update', $room->id), [
                 'building_id' => $building->id,
                 'code' => 'ROOM-01-UPDATED',
                 'name' => 'Ruang Baru Terupdate',
                 'floor' => 2,
                 'capacity' => 40,
                 'is_active' => true,
             ]);

        $response->assertRedirect(route('facility.rooms.index'));
        $this->assertDatabaseHas('rooms', ['name' => 'Ruang Baru Terupdate']);
    }

    public function test_admin_can_delete_room()
    {
        $admin = User::factory()->create();

        $building = Building::create([
            'code' => 'BLD-01',
            'name' => 'Gedung Utama',
            'total_floors' => 5,
            'is_active' => true,
        ]);

        $room = Room::create([
            'building_id' => $building->id,
            'code' => 'ROOM-DEL',
            'name' => 'Ruang Hapus',
            'floor' => 1,
            'capacity' => 15,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)
             ->delete(route('facility.rooms.destroy', $room->id));

        // Memastikan redirect dan data terhapus dari database (soft delete atau database missing)
        $response->assertRedirect(route('facility.rooms.index'));
        $this->assertDatabaseMissing('rooms', ['code' => 'ROOM-DEL']);
    }
}