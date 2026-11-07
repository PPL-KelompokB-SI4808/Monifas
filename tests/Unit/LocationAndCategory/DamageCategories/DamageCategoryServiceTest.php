<?php

use App\DTOs\LocationAndCategory\DamageCategoryData;
use App\Models\DamageCategory;
use App\Services\LocationAndCategory\DamageCategories\DamageCategoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->service = new DamageCategoryService;
});

test('service dapat membuat kategori kerusakan baru dari DTO', function () {
    $dto = new DamageCategoryData(
        code: 'DC-TEST-01',
        name: 'Korsleting Listrik',
        severityHint: 'Memerlukan Penanganan Segera',
        description: 'Arus pendek pada instalasi kelistrikan gedung.',
        isActive: true,
    );

    $category = $this->service->store($dto);

    expect($category)->toBeInstanceOf(DamageCategory::class);
    expect($category->code)->toBe('DC-TEST-01');
    expect($category->name)->toBe('Korsleting Listrik');
    expect($category->severity_hint)->toBe('Memerlukan Penanganan Segera');
    expect($category->description)->toBe('Arus pendek pada instalasi kelistrikan gedung.');
    expect($category->is_active)->toBeTrue();

    $this->assertDatabaseHas('damage_categories', [
        'code' => 'DC-TEST-01',
        'name' => 'Korsleting Listrik',
        'severity_hint' => 'Memerlukan Penanganan Segera',
    ]);
});

test('service dapat memperbarui kategori kerusakan dari DTO', function () {
    $category = DamageCategory::factory()->create([
        'code' => 'DC-OLD',
        'name' => 'Nama Lama',
        'severity_hint' => 'Dapat Ditunda',
        'is_active' => true,
    ]);

    $dto = new DamageCategoryData(
        code: 'DC-NEW',
        name: 'Nama Baru Diperbarui',
        severityHint: 'Memerlukan Penanganan Segera',
        description: 'Deskripsi diperbaharui.',
        isActive: false,
    );

    $updated = $this->service->update($category, $dto);

    expect($updated->code)->toBe('DC-NEW');
    expect($updated->name)->toBe('Nama Baru Diperbarui');
    expect($updated->severity_hint)->toBe('Memerlukan Penanganan Segera');
    expect($updated->is_active)->toBeFalse();

    $this->assertDatabaseHas('damage_categories', [
        'id' => $category->id,
        'code' => 'DC-NEW',
        'name' => 'Nama Baru Diperbarui',
    ]);
});

test('service dapat menghapus kategori kerusakan tanpa keterkaitan via soft delete', function () {
    $category = DamageCategory::factory()->create([
        'code' => 'DC-DEL',
    ]);

    $result = $this->service->delete($category);

    expect($result)->toBeTrue();
    $this->assertSoftDeleted('damage_categories', ['id' => $category->id]);
});

test('service mendeteksi tidak ada dependensi pada kategori baru tanpa laporan (FR-LCT-06)', function () {
    $category = DamageCategory::factory()->create();

    // Tabel reports belum ada / tidak ada laporan terkait
    expect($this->service->hasDependencies($category))->toBeFalse();
});

test('service menonaktifkan sekaligus soft delete kategori kerusakan yang memiliki dependensi (FR-LCT-06)', function () {
    $category = DamageCategory::factory()->create([
        'code' => 'DC-LINKED',
        'is_active' => true,
    ]);

    $serviceMock = Mockery::mock(DamageCategoryService::class)->makePartial();
    $serviceMock->shouldReceive('hasDependencies')->andReturn(true);

    $serviceMock->delete($category);

    expect($category->fresh()->is_active)->toBeFalse();
    $this->assertSoftDeleted('damage_categories', ['id' => $category->id]);
});

test('service memblokir force delete kategori kerusakan yang memiliki dependensi (FR-LCT-06)', function () {
    $category = DamageCategory::factory()->create([
        'code' => 'DC-FORCE-PROTECT',
        'is_active' => true,
    ]);

    $serviceMock = Mockery::mock(DamageCategoryService::class)->makePartial();
    $serviceMock->shouldReceive('hasDependencies')->andReturn(true);

    expect(fn () => $serviceMock->forceDelete($category))
        ->toThrow(RuntimeException::class, 'Kategori kerusakan yang telah terhubung dengan data laporan dilarang dihapus permanen.');
});

test('service dapat membalikkan status keaktifan kategori kerusakan', function () {
    $category = DamageCategory::factory()->create([
        'is_active' => true,
    ]);

    $this->service->toggleStatus($category);
    expect($category->fresh()->is_active)->toBeFalse();

    $this->service->toggleStatus($category);
    expect($category->fresh()->is_active)->toBeTrue();
});

test('service dapat mengambil semua kategori kerusakan aktif', function () {
    DamageCategory::factory()->create(['is_active' => true, 'name' => 'Rusak Aktif 1']);
    DamageCategory::factory()->create(['is_active' => true, 'name' => 'Rusak Aktif 2']);
    DamageCategory::factory()->create(['is_active' => false, 'name' => 'Rusak Nonaktif']);

    $active = $this->service->getActiveCategories();

    expect($active)->toHaveCount(2);
    expect($active->pluck('name'))->toContain('Rusak Aktif 1', 'Rusak Aktif 2');
    expect($active->pluck('name'))->not->toContain('Rusak Nonaktif');
});

test('service mengembalikan daftar severity hint unik yang ada di basis data', function () {
    DamageCategory::factory()->create(['severity_hint' => 'Memerlukan Penanganan Segera']);
    DamageCategory::factory()->create(['severity_hint' => 'Memerlukan Penanganan Segera']);
    DamageCategory::factory()->create(['severity_hint' => 'Penanganan Standar']);
    DamageCategory::factory()->create(['severity_hint' => null]);

    $hints = $this->service->getUniqueSeverityHints();

    expect($hints)->toHaveCount(2);
    expect($hints)->toContain('Memerlukan Penanganan Segera', 'Penanganan Standar');
});

test('service dapat menyaring daftar terpaginasi berdasarkan kata kunci, severity, dan status', function () {
    DamageCategory::factory()->create([
        'code' => 'DC-SRCH-A',
        'name' => 'Korsleting Panel Listrik',
        'severity_hint' => 'Memerlukan Penanganan Segera',
        'is_active' => true,
    ]);

    DamageCategory::factory()->create([
        'code' => 'DC-SRCH-B',
        'name' => 'Kebocoran Pipa Minor',
        'severity_hint' => 'Dapat Ditunda',
        'is_active' => false,
    ]);

    // Filter search
    $searchResult = $this->service->getPaginatedList(['search' => 'Korsleting']);
    expect($searchResult->total())->toBe(1);
    expect($searchResult->first()->code)->toBe('DC-SRCH-A');

    // Filter severity_hint
    $severityResult = $this->service->getPaginatedList(['severity_hint' => 'Dapat Ditunda']);
    expect($severityResult->total())->toBe(1);
    expect($severityResult->first()->code)->toBe('DC-SRCH-B');

    // Filter is_active
    $activeResult = $this->service->getPaginatedList(['is_active' => '1']);
    expect($activeResult->total())->toBe(1);
    expect($activeResult->first()->code)->toBe('DC-SRCH-A');
});

test('service dapat mencari kategori kerusakan berdasarkan kode', function () {
    DamageCategory::factory()->create(['code' => 'DC-FIND-ME']);

    $found = $this->service->findByCode('DC-FIND-ME');

    expect($found)->not->toBeNull();
    expect($found->code)->toBe('DC-FIND-ME');

    $notFound = $this->service->findByCode('DC-NOT-EXISTS');
    expect($notFound)->toBeNull();
});
