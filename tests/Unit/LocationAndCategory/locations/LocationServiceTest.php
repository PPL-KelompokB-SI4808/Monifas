<?php

use App\DTOs\LocationAndCategory\LocationData;
use App\Models\Location;
use App\Services\LocationAndCategory\Locations\LocationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->service = new LocationService;
});

test('service dapat membuat lokasi baru dari DTO', function () {
    $dto = new LocationData(
        parentId: null,
        code: 'LOC-TEST-01',
        name: 'Kampus Barat',
        region: 'Bandung',
        address: 'Jl. Radio Palasari',
        description: 'Kawasan riset dan laboratorium',
        isActive: true,
    );

    $location = $this->service->store($dto);

    expect($location)->toBeInstanceOf(Location::class);
    expect($location->code)->toBe('LOC-TEST-01');
    expect($location->name)->toBe('Kampus Barat');
    expect($location->region)->toBe('Bandung');
    expect($location->address)->toBe('Jl. Radio Palasari');
    expect($location->description)->toBe('Kawasan riset dan laboratorium');
    expect($location->is_active)->toBeTrue();
    expect($location->parent_id)->toBeNull();

    $this->assertDatabaseHas('locations', [
        'code' => 'LOC-TEST-01',
        'name' => 'Kampus Barat',
    ]);
});

test('service dapat memperbarui lokasi dari DTO', function () {
    $location = Location::factory()->create([
        'code' => 'LOC-OLD',
        'name' => 'Nama Lama',
        'region' => 'Bandung',
        'is_active' => true,
    ]);

    $dto = new LocationData(
        parentId: null,
        code: 'LOC-NEW',
        name: 'Nama Baru Diperbarui',
        region: 'Jakarta',
        address: 'Jl. Baru No. 99',
        description: 'Deskripsi baru',
        isActive: false,
    );

    $updated = $this->service->update($location, $dto);

    expect($updated->code)->toBe('LOC-NEW');
    expect($updated->name)->toBe('Nama Baru Diperbarui');
    expect($updated->region)->toBe('Jakarta');
    expect($updated->is_active)->toBeFalse();

    $this->assertDatabaseHas('locations', [
        'id' => $location->id,
        'code' => 'LOC-NEW',
        'name' => 'Nama Baru Diperbarui',
    ]);
});

test('service dapat menghapus lokasi tanpa keterkaitan secara aman (soft delete)', function () {
    $location = Location::factory()->create([
        'code' => 'LOC-DEL',
    ]);

    $result = $this->service->delete($location);

    expect($result)->toBeTrue();
    $this->assertSoftDeleted('locations', ['id' => $location->id]);
});

test('service menonaktifkan lokasi jika memiliki anak lokasi atau gedung (safe delete)', function () {
    $parent = Location::factory()->create([
        'code' => 'LOC-PARENT',
        'is_active' => true,
    ]);

    $child = Location::factory()->create([
        'parent_id' => $parent->id,
        'code' => 'LOC-CHILD',
        'is_active' => true,
    ]);

    $result = $this->service->delete($parent);

    expect($result)->toBeTrue();
    expect($parent->fresh()->is_active)->toBeFalse();
    expect($parent->fresh()->deleted_at)->toBeNull();
    $this->assertDatabaseHas('locations', [
        'id' => $parent->id,
        'is_active' => false,
        'deleted_at' => null,
    ]);
});

test('service dapat membalikkan status keaktifan lokasi', function () {
    $location = Location::factory()->create([
        'is_active' => true,
    ]);

    $this->service->toggleStatus($location);
    expect($location->fresh()->is_active)->toBeFalse();

    $this->service->toggleStatus($location);
    expect($location->fresh()->is_active)->toBeTrue();
});

test('service dapat mengambil semua lokasi aktif', function () {
    Location::factory()->create(['is_active' => true, 'name' => 'Aktif 1']);
    Location::factory()->create(['is_active' => true, 'name' => 'Aktif 2']);
    Location::factory()->create(['is_active' => false, 'name' => 'Nonaktif']);

    $activeLocations = $this->service->getActiveLocations();

    expect($activeLocations)->toHaveCount(2);
    expect($activeLocations->pluck('name'))->toContain('Aktif 1', 'Aktif 2');
    expect($activeLocations->pluck('name'))->not->toContain('Nonaktif');
});

test('service mengecualikan lokasi itu sendiri dan seluruh keturunannya saat mencari kandidat induk', function () {
    $root = Location::factory()->create(['name' => 'Root Area', 'is_active' => true]);
    $child = Location::factory()->create(['parent_id' => $root->id, 'name' => 'Child Area', 'is_active' => true]);
    $grandchild = Location::factory()->create(['parent_id' => $child->id, 'name' => 'Grandchild Area', 'is_active' => true]);
    $other = Location::factory()->create(['name' => 'Other Area', 'is_active' => true]);

    $candidates = $this->service->getParentCandidates($root);

    $candidateIds = $candidates->pluck('id')->toArray();

    expect($candidateIds)->not->toContain($root->id);
    expect($candidateIds)->not->toContain($child->id);
    expect($candidateIds)->not->toContain($grandchild->id);
    expect($candidateIds)->toContain($other->id);
});

test('service dapat mengambil pohon hierarki kawasan', function () {
    $root = Location::factory()->create(['name' => 'Root 1', 'parent_id' => null, 'is_active' => true]);
    $child1 = Location::factory()->create(['name' => 'Sub 1', 'parent_id' => $root->id, 'is_active' => true]);
    $child2 = Location::factory()->create(['name' => 'Sub 2', 'parent_id' => $root->id, 'is_active' => true]);

    $tree = $this->service->getTree();

    expect($tree)->toHaveCount(1);
    expect($tree->first()->id)->toBe($root->id);
    expect($tree->first()->children)->toHaveCount(2);
});

test('service dapat menyaring daftar terpaginasi berdasarkan kata kunci, wilayah, dan status', function () {
    Location::factory()->create([
        'code' => 'BDG-01',
        'name' => 'Kampus Bandung',
        'region' => 'Bandung',
        'is_active' => true,
    ]);

    Location::factory()->create([
        'code' => 'JKT-01',
        'name' => 'Kampus Jakarta',
        'region' => 'Jakarta',
        'is_active' => false,
    ]);

    // Filter search
    $searchResult = $this->service->getPaginatedList(['search' => 'Bandung']);
    expect($searchResult->total())->toBe(1);
    expect($searchResult->first()->code)->toBe('BDG-01');

    // Filter region
    $regionResult = $this->service->getPaginatedList(['region' => 'Jakarta']);
    expect($regionResult->total())->toBe(1);
    expect($regionResult->first()->code)->toBe('JKT-01');

    // Filter is_active
    $activeResult = $this->service->getPaginatedList(['is_active' => '1']);
    expect($activeResult->total())->toBe(1);
    expect($activeResult->first()->code)->toBe('BDG-01');
});
