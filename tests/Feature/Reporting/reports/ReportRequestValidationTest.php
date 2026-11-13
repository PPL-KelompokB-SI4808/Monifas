<?php

use App\Enums\ReportStatus;
use App\Enums\UserRole;
use App\Models\Building;
use App\Models\DamageCategory;
use App\Models\Facility;
use App\Models\Location;
use App\Models\Report;
use App\Models\ReportPriority;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

// =========================================================
// StoreReportRequest — validasi
// =========================================================

describe('StoreReportRequest validasi', function () {
    test('validasi gagal jika field wajib tidak diisi', function () {
        $validator = Validator::make([], [
            'title' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'location_id' => ['required', 'integer'],
            'building_id' => ['required', 'integer'],
            'room_id' => ['nullable', 'integer'],
            'facility_id' => ['required', 'integer'],
            'damage_category_id' => ['required', 'integer'],
            'priority_id' => ['required', 'integer'],
        ]);

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->keys())->toContain(
                'title', 'description', 'location_id', 'building_id',
                'facility_id', 'damage_category_id', 'priority_id'
            );
    });

    test('validasi gagal jika title melebihi 200 karakter', function () {
        $validator = Validator::make(
            ['title' => str_repeat('A', 201), 'description' => 'Valid desc', 'location_id' => 1, 'building_id' => 1, 'facility_id' => 1, 'damage_category_id' => 1, 'priority_id' => 1],
            [
                'title' => ['required', 'string', 'max:200'],
                'description' => ['required', 'string'],
                'location_id' => ['required', 'integer'],
                'building_id' => ['required', 'integer'],
                'room_id' => ['nullable', 'integer'],
                'facility_id' => ['required', 'integer'],
                'damage_category_id' => ['required', 'integer'],
                'priority_id' => ['required', 'integer'],
            ]
        );

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('title'))->toBeTrue();
    });

    test('validasi berhasil jika room_id null (fasilitas tanpa ruangan spesifik)', function () {
        $location = Location::factory()->create();
        $building = Building::factory()->create(['location_id' => $location->id]);
        $priority = ReportPriority::factory()->emergency()->create();
        $damageCategory = DamageCategory::factory()->create();
        $facility = Facility::factory()->create([
            'building_id' => $building->id,
            'location_id' => $location->id,
            'room_id' => null,
        ]);

        $validator = Validator::make([
            'title' => 'Atap Bocor',
            'description' => 'Atap bocor saat hujan lebat.',
            'location_id' => $location->id,
            'building_id' => $building->id,
            'room_id' => null,
            'facility_id' => $facility->id,
            'damage_category_id' => $damageCategory->id,
            'priority_id' => $priority->id,
        ], [
            'title' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'location_id' => ['required', 'integer', 'exists:locations,id'],
            'building_id' => ['required', 'integer', 'exists:buildings,id'],
            'room_id' => ['nullable', 'integer', 'exists:rooms,id'],
            'facility_id' => ['required', 'integer', 'exists:facilities,id'],
            'damage_category_id' => ['required', 'integer', 'exists:damage_categories,id'],
            'priority_id' => ['required', 'integer', 'exists:report_priorities,id'],
        ]);

        expect($validator->fails())->toBeFalse();
    });

    test('validasi gagal jika foreign key tidak ada di database', function () {
        $validator = Validator::make([
            'title' => 'Judul Valid',
            'description' => 'Deskripsi valid.',
            'location_id' => 99999,
            'building_id' => 99999,
            'room_id' => null,
            'facility_id' => 99999,
            'damage_category_id' => 99999,
            'priority_id' => 99999,
        ], [
            'title' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'location_id' => ['required', 'integer', 'exists:locations,id'],
            'building_id' => ['required', 'integer', 'exists:buildings,id'],
            'room_id' => ['nullable', 'integer', 'exists:rooms,id'],
            'facility_id' => ['required', 'integer', 'exists:facilities,id'],
            'damage_category_id' => ['required', 'integer', 'exists:damage_categories,id'],
            'priority_id' => ['required', 'integer', 'exists:report_priorities,id'],
        ]);

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('location_id'))->toBeTrue()
            ->and($validator->errors()->has('building_id'))->toBeTrue()
            ->and($validator->errors()->has('facility_id'))->toBeTrue()
            ->and($validator->errors()->has('damage_category_id'))->toBeTrue()
            ->and($validator->errors()->has('priority_id'))->toBeTrue();
    });
});

// =========================================================
// VerifyReportRequest — validasi (dengan app context)
// =========================================================

describe('VerifyReportRequest validasi lengkap', function () {
    test('validasi gagal jika action tidak disertakan', function () {
        $validator = Validator::make([], [
            'action' => ['required', 'string', 'in:verify,reject'],
            'rejection_reason' => ['required_if:action,reject', 'nullable', 'string'],
        ]);

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('action'))->toBeTrue();
    });

    test('validasi berhasil dengan action verify tanpa rejection_reason', function () {
        $validator = Validator::make(['action' => 'verify'], [
            'action' => ['required', 'string', 'in:verify,reject'],
            'rejection_reason' => ['required_if:action,reject', 'nullable', 'string'],
        ]);

        expect($validator->fails())->toBeFalse();
    });

    test('validasi gagal jika action reject tanpa rejection_reason', function () {
        $validator = Validator::make(['action' => 'reject'], [
            'action' => ['required', 'string', 'in:verify,reject'],
            'rejection_reason' => ['required_if:action,reject', 'nullable', 'string'],
        ]);

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('rejection_reason'))->toBeTrue();
    });

    test('validasi berhasil dengan action reject dan rejection_reason terisi', function () {
        $validator = Validator::make(
            ['action' => 'reject', 'rejection_reason' => 'Laporan tidak sesuai prosedur.'],
            [
                'action' => ['required', 'string', 'in:verify,reject'],
                'rejection_reason' => ['required_if:action,reject', 'nullable', 'string'],
            ]
        );

        expect($validator->fails())->toBeFalse();
    });

    test('validasi gagal jika action tidak termasuk verify atau reject', function () {
        $validator = Validator::make(['action' => 'approve'], [
            'action' => ['required', 'string', 'in:verify,reject'],
            'rejection_reason' => ['required_if:action,reject', 'nullable', 'string'],
        ]);

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->has('action'))->toBeTrue();
    });
});

// =========================================================
// UpdateReportRequest — otorisasi HTTP (dengan auth)
// =========================================================

describe('UpdateReportRequest otorisasi HTTP', function () {
    test('pengguna tidak terautentikasi diarahkan ke halaman login', function () {
        $location = Location::factory()->create();
        $building = Building::factory()->create(['location_id' => $location->id]);
        $priority = ReportPriority::factory()->emergency()->create();
        $damageCategory = DamageCategory::factory()->create();
        $facility = Facility::factory()->create([
            'building_id' => $building->id,
            'location_id' => $location->id,
            'room_id' => null,
        ]);
        $reporter = User::factory()->create();
        $report = Report::factory()->create([
            'reporter_id' => $reporter->id,
            'location_id' => $location->id,
            'building_id' => $building->id,
            'room_id' => null,
            'facility_id' => $facility->id,
            'damage_category_id' => $damageCategory->id,
            'priority_id' => $priority->id,
            'status' => ReportStatus::Pending,
        ]);

        $this->withoutVite()
            ->put(route('reporting.reports.update', $report))
            ->assertRedirect(route('login'));
    });

    test('pengguna lain tidak bisa mengedit laporan milik orang lain', function () {
        $location = Location::factory()->create();
        $building = Building::factory()->create(['location_id' => $location->id]);
        $priority = ReportPriority::factory()->emergency()->create();
        $damageCategory = DamageCategory::factory()->create();
        $facility = Facility::factory()->create([
            'building_id' => $building->id,
            'location_id' => $location->id,
            'room_id' => null,
        ]);
        $reporter = User::factory()->create();
        $otherRole = Role::firstOrCreate(['slug' => UserRole::FacilityUser->value], ['name' => 'Facility User']);
        $otherUser = User::factory()->create(['role_id' => $otherRole->id]);

        $report = Report::factory()->create([
            'reporter_id' => $reporter->id,
            'location_id' => $location->id,
            'building_id' => $building->id,
            'room_id' => null,
            'facility_id' => $facility->id,
            'damage_category_id' => $damageCategory->id,
            'priority_id' => $priority->id,
            'status' => ReportStatus::Pending,
        ]);

        $this->withoutVite()
            ->actingAs($otherUser)
            ->put(route('reporting.reports.update', $report), [
                'title' => 'Judul Baru',
                'description' => 'Deskripsi.',
                'location_id' => $location->id,
                'building_id' => $building->id,
                'facility_id' => $facility->id,
                'damage_category_id' => $damageCategory->id,
                'priority_id' => $priority->id,
            ])
            ->assertForbidden();
    });
});
