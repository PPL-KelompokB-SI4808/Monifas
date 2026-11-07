<?php

use App\DTOs\LocationAndCategory\FacilityCategoryData;
use App\Models\FacilityCategory;
use App\Services\LocationAndCategory\FacilityCategories\FacilityCategoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->service = new FacilityCategoryService;
});

test('service dapat membuat kategori fasilitas baru dari DTO', function () {
    $dto = new FacilityCategoryData(
        code: 'FC-TEST-01',
        name: 'Sistem Kelistrikan',
        description: 'Mencakup panel daya, kabel, dan pencahayaan.',
        isActive: true,
    );

    $category = $this->service->store($dto);

    expect($category)->toBeInstanceOf(FacilityCategory::class);
    expect($category->code)->toBe('FC-TEST-01');
    expect($category->name)->toBe('Sistem Kelistrikan');
    expect($category->description)->toBe('Mencakup panel daya, kabel, dan pencahayaan.');
    expect($category->is_active)->toBeTrue();

    $this->assertDatabaseHas('facility_categories', [
        'code' => 'FC-TEST-01',
        'name' => 'Sistem Kelistrikan',
    ]);
});

test('service dapat memperbarui kategori fasilitas dari DTO', function () {
    $category = FacilityCategory::factory()->create([
        'code' => 'FC-OLD',
        'name' => 'Nama Lama',
        'is_active' => true,
    ]);

    $dto = new FacilityCategoryData(
        code: 'FC-NEW',
        name: 'Nama Baru Diperbarui',
        description: 'Deskripsi diperbaharui.',
        isActive: false,
    );

    $updated = $this->service->update($category, $dto);

    expect($updated->code)->toBe('FC-NEW');
    expect($updated->name)->toBe('Nama Baru Diperbarui');
    expect($updated->is_active)->toBeFalse();

    $this->assertDatabaseHas('facility_categories', [
        'id' => $category->id,
        'code' => 'FC-NEW',
        'name' => 'Nama Baru Diperbarui',
    ]);
});

test('service dapat menghapus kategori fasilitas tanpa keterkaitan via soft delete', function () {
    $category = FacilityCategory::factory()->create([
        'code' => 'FC-DEL',
    ]);

    $result = $this->service->delete($category);

    expect($result)->toBeTrue();
    $this->assertSoftDeleted('facility_categories', ['id' => $category->id]);
});

test('service mendeteksi tidak ada dependensi pada kategori baru tanpa fasilitas', function () {
    $category = FacilityCategory::factory()->create();

    expect($this->service->hasDependencies($category))->toBeFalse();
});

test('service menonaktifkan sekaligus soft delete kategori fasilitas yang memiliki dependensi (FR-LCT-06)', function () {
    $category = FacilityCategory::factory()->create([
        'code' => 'FC-LINKED',
        'is_active' => true,
    ]);

    // Simulasikan hasDependencies = true dengan mengganti partial mock
    $serviceMock = Mockery::mock(FacilityCategoryService::class)->makePartial();
    $serviceMock->shouldReceive('hasDependencies')->andReturn(true);

    $serviceMock->delete($category);

    // Kategori harus dinonaktifkan dahulu
    expect($category->fresh()->is_active)->toBeFalse();

    // Dan kemudian soft deleted
    $this->assertSoftDeleted('facility_categories', ['id' => $category->id]);
});

test('service memblokir force delete kategori yang memiliki dependensi (FR-LCT-06)', function () {
    $category = FacilityCategory::factory()->create([
        'code' => 'FC-FORCE-PROTECT',
        'is_active' => true,
    ]);

    $serviceMock = Mockery::mock(FacilityCategoryService::class)->makePartial();
    $serviceMock->shouldReceive('hasDependencies')->andReturn(true);

    expect(fn () => $serviceMock->forceDelete($category))
        ->toThrow(RuntimeException::class, 'Kategori fasilitas yang telah terhubung dengan data fasilitas dilarang dihapus permanen.');
});

test('service dapat membalikkan status keaktifan kategori fasilitas', function () {
    $category = FacilityCategory::factory()->create([
        'is_active' => true,
    ]);

    $this->service->toggleStatus($category);
    expect($category->fresh()->is_active)->toBeFalse();

    $this->service->toggleStatus($category);
    expect($category->fresh()->is_active)->toBeTrue();
});

test('service dapat mengambil semua kategori fasilitas aktif', function () {
    FacilityCategory::factory()->create(['is_active' => true, 'name' => 'Aktif 1']);
    FacilityCategory::factory()->create(['is_active' => true, 'name' => 'Aktif 2']);
    FacilityCategory::factory()->create(['is_active' => false, 'name' => 'Nonaktif']);

    $active = $this->service->getActiveCategories();

    expect($active)->toHaveCount(2);
    expect($active->pluck('name'))->toContain('Aktif 1', 'Aktif 2');
    expect($active->pluck('name'))->not->toContain('Nonaktif');
});

test('service dapat menyaring daftar terpaginasi berdasarkan kata kunci dan status', function () {
    FacilityCategory::factory()->create([
        'code' => 'FC-SRCH-A',
        'name' => 'Listrik dan Panel',
        'is_active' => true,
    ]);

    FacilityCategory::factory()->create([
        'code' => 'FC-SRCH-B',
        'name' => 'HVAC Pendingin Ruangan',
        'is_active' => false,
    ]);

    // Filter search
    $searchResult = $this->service->getPaginatedList(['search' => 'Listrik']);
    expect($searchResult->total())->toBe(1);
    expect($searchResult->first()->code)->toBe('FC-SRCH-A');

    // Filter is_active
    $activeResult = $this->service->getPaginatedList(['is_active' => '0']);
    expect($activeResult->total())->toBe(1);
    expect($activeResult->first()->code)->toBe('FC-SRCH-B');
});

test('service dapat mencari kategori fasilitas berdasarkan kode', function () {
    FacilityCategory::factory()->create(['code' => 'FC-FIND-ME']);

    $found = $this->service->findByCode('FC-FIND-ME');

    expect($found)->not->toBeNull();
    expect($found->code)->toBe('FC-FIND-ME');

    $notFound = $this->service->findByCode('FC-NOT-EXISTS');
    expect($notFound)->toBeNull();
});
