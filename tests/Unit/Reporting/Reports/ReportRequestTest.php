<?php

use App\DTOs\Reporting\ReportData;
use Tests\TestCase;

uses(TestCase::class);
use App\Enums\ReportStatus;
use App\Http\Requests\Reporting\Reports\UpdateReportRequest;
use App\Http\Requests\Reporting\Reports\VerifyReportRequest;
use App\Models\Report;
use Illuminate\Routing\Route;

// =========================================================
// ReportData DTO — tidak memerlukan database / app bootstrap
// =========================================================

describe('ReportData DTO', function () {
    test('fromArray membuat instance dengan nilai yang benar', function () {
        $data = [
            'title' => 'Atap Bocor Ruang Kelas 3A',
            'description' => 'Atap bocor saat hujan lebat.',
            'location_id' => 1,
            'building_id' => 2,
            'room_id' => 5,
            'facility_id' => 10,
            'damage_category_id' => 3,
            'priority_id' => 2,
            'status' => 'pending',
        ];

        $dto = ReportData::fromArray($data);

        expect($dto->title)->toBe('Atap Bocor Ruang Kelas 3A')
            ->and($dto->description)->toBe('Atap bocor saat hujan lebat.')
            ->and($dto->locationId)->toBe(1)
            ->and($dto->buildingId)->toBe(2)
            ->and($dto->roomId)->toBe(5)
            ->and($dto->facilityId)->toBe(10)
            ->and($dto->damageCategoryId)->toBe(3)
            ->and($dto->priorityId)->toBe(2)
            ->and($dto->status)->toBe(ReportStatus::Pending);
    });

    test('fromArray menetapkan status Pending sebagai default jika tidak disertakan', function () {
        $data = [
            'title' => 'AC Mati',
            'description' => 'AC tidak berfungsi.',
            'location_id' => 1,
            'building_id' => 2,
            'room_id' => null,
            'facility_id' => 7,
            'damage_category_id' => 1,
            'priority_id' => 1,
        ];

        $dto = ReportData::fromArray($data);

        expect($dto->status)->toBe(ReportStatus::Pending)
            ->and($dto->roomId)->toBeNull();
    });

    test('fromArray menerima room_id null untuk fasilitas tanpa ruangan spesifik', function () {
        $data = [
            'title' => 'Lampu Lorong Padam',
            'description' => 'Semua lampu di lorong mati.',
            'location_id' => 1,
            'building_id' => 2,
            'room_id' => null,
            'facility_id' => 8,
            'damage_category_id' => 2,
            'priority_id' => 1,
        ];

        $dto = ReportData::fromArray($data);

        expect($dto->roomId)->toBeNull();
    });

    test('toArray mengembalikan format array yang benar untuk persistensi model', function () {
        $dto = new ReportData(
            title: 'Pintu Rusak',
            description: 'Engsel pintu lepas.',
            locationId: 1,
            buildingId: 2,
            roomId: 3,
            facilityId: 4,
            damageCategoryId: 5,
            priorityId: 6,
            status: ReportStatus::Pending,
        );

        $array = $dto->toArray();

        expect($array)->toHaveKeys([
            'title', 'description', 'location_id', 'building_id', 'room_id',
            'facility_id', 'damage_category_id', 'priority_id', 'status',
        ])
            ->and($array['title'])->toBe('Pintu Rusak')
            ->and($array['status'])->toBe('pending')
            ->and($array['room_id'])->toBe(3);
    });

    test('fromArray trim whitespace pada title dan description', function () {
        $data = [
            'title' => '  Kebocoran Pipa  ',
            'description' => '  Air merembes dari dinding.  ',
            'location_id' => 1,
            'building_id' => 1,
            'room_id' => null,
            'facility_id' => 1,
            'damage_category_id' => 1,
            'priority_id' => 1,
        ];

        $dto = ReportData::fromArray($data);

        expect($dto->title)->toBe('Kebocoran Pipa')
            ->and($dto->description)->toBe('Air merembes dari dinding.');
    });

    test('fromArray memetakan status string verified dengan benar', function () {
        $data = [
            'title' => 'Test',
            'description' => 'Test desc',
            'location_id' => 1,
            'building_id' => 1,
            'room_id' => null,
            'facility_id' => 1,
            'damage_category_id' => 1,
            'priority_id' => 1,
            'status' => 'verified',
        ];

        $dto = ReportData::fromArray($data);

        expect($dto->status)->toBe(ReportStatus::Verified);
    });

    test('fromArray fallback ke Pending jika status tidak dikenali', function () {
        $data = [
            'title' => 'Test',
            'description' => 'Test desc',
            'location_id' => 1,
            'building_id' => 1,
            'room_id' => null,
            'facility_id' => 1,
            'damage_category_id' => 1,
            'priority_id' => 1,
            'status' => 'unknown_status',
        ];

        $dto = ReportData::fromArray($data);

        expect($dto->status)->toBe(ReportStatus::Pending);
    });
});

// =========================================================
// UpdateReportRequest — otorisasi (tanpa DB)
// =========================================================

describe('UpdateReportRequest otorisasi', function () {
    test('authorize mengembalikan false jika user tidak ada', function () {
        $request = new UpdateReportRequest;

        expect($request->authorize())->toBeFalse();
    });

    test('authorize mengembalikan false jika report bukan instance Report', function () {
        $request = new UpdateReportRequest;

        // Tanpa route, $this->route('report') = null
        expect($request->authorize())->toBeFalse();
    });

    test('authorize mengembalikan false jika report berstatus Verified', function () {
        $report = new Report;
        $report->status = ReportStatus::Verified;
        $report->reporter_id = 1;

        $request = new UpdateReportRequest;
        $request->setRouteResolver(fn () => tap(
            new Route('PUT', '/test/{report}', []),
            fn ($r) => $r->setParameter('report', $report)
        ));

        expect($request->authorize())->toBeFalse();
    });
});

// =========================================================
// VerifyReportRequest — helper methods (tanpa DB / auth)
// =========================================================

describe('VerifyReportRequest helper methods', function () {
    test('isVerify mengembalikan true jika action adalah verify', function () {
        $request = new VerifyReportRequest;
        $request->merge(['action' => 'verify']);

        expect($request->isVerify())->toBeTrue()
            ->and($request->isReject())->toBeFalse();
    });

    test('isReject mengembalikan true jika action adalah reject', function () {
        $request = new VerifyReportRequest;
        $request->merge(['action' => 'reject', 'rejection_reason' => 'Alasan penolakan.']);

        expect($request->isReject())->toBeTrue()
            ->and($request->isVerify())->toBeFalse();
    });

    test('rejectionReason mengembalikan string trim jika diisi', function () {
        $request = new VerifyReportRequest;
        $request->merge(['action' => 'reject', 'rejection_reason' => '  Tidak valid.  ']);

        expect($request->rejectionReason())->toBe('Tidak valid.');
    });

    test('rejectionReason mengembalikan null jika tidak ada input', function () {
        $request = new VerifyReportRequest;
        $request->merge(['action' => 'verify']);

        expect($request->rejectionReason())->toBeNull();
    });

    test('rejectionReason mengembalikan null jika hanya whitespace', function () {
        $request = new VerifyReportRequest;
        $request->merge(['action' => 'reject', 'rejection_reason' => '   ']);

        expect($request->rejectionReason())->toBeNull();
    });

    test('authorize mengembalikan false jika user tidak terautentikasi', function () {
        $request = new VerifyReportRequest;

        expect($request->authorize())->toBeFalse();
    });
});
