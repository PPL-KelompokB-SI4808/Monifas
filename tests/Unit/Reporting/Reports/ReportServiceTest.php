<?php

use App\DTOs\Reporting\ReportData;
use App\Enums\NotificationType;
use App\Enums\ReportStatus;
use App\Models\Building;
use App\Models\DamageCategory;
use App\Models\Facility;
use App\Models\Location;
use App\Models\Notification;
use App\Models\Report;
use App\Models\ReportPriority;
use App\Models\User;
use App\Services\Reporting\Reports\ReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->location = Location::factory()->create();
    $this->building = Building::factory()->create(['location_id' => $this->location->id]);
    $this->priority = ReportPriority::factory()->emergency()->create();
    $this->damageCategory = DamageCategory::factory()->create();
    $this->facility = Facility::factory()->create([
        'location_id' => $this->location->id,
        'building_id' => $this->building->id,
        'room_id' => null,
    ]);
    $this->reporter = User::factory()->create();
    $this->supervisor = User::factory()->create();

    $this->service = app(ReportService::class);
});

// =========================================================
// Generator Nomor Tiket
// =========================================================

describe('Generator Nomor Tiket Otomatis', function () {
    test('menghasilkan nomor tiket dengan format MNF-YYYYMMDD-0001 untuk laporan pertama di hari tersebut', function () {
        $today = Carbon::create(2026, 11, 13);
        Carbon::setTestNow($today);

        $ticketNumber = $this->service->generateTicketNumber($today);

        expect($ticketNumber)->toBe('MNF-20261113-0001');

        Carbon::setTestNow();
    });

    test('menghasilkan nomor tiket berurutan (0002, 0003) ketika tiket sebelumnya sudah ada', function () {
        $date = Carbon::create(2026, 11, 13);
        Carbon::setTestNow($date);

        Report::factory()->create([
            'ticket_number' => 'MNF-20261113-0001',
            'reporter_id' => $this->reporter->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
        ]);

        $ticketNumber = $this->service->generateTicketNumber($date);

        expect($ticketNumber)->toBe('MNF-20261113-0002');

        Carbon::setTestNow();
    });

    test('menghasilkan nomor tiket yang benar-benar unik meskipun ada nomor yang melompat', function () {
        $date = Carbon::create(2026, 11, 13);
        Carbon::setTestNow($date);

        Report::factory()->create([
            'ticket_number' => 'MNF-20261113-0005',
            'reporter_id' => $this->reporter->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
        ]);

        $ticketNumber = $this->service->generateTicketNumber($date);

        expect($ticketNumber)->toBe('MNF-20261113-0006');

        Carbon::setTestNow();
    });
});

// =========================================================
// Pembuatan Laporan (store)
// =========================================================

describe('Pembuatan Laporan (store)', function () {
    test('membuat laporan baru dalam transaksi DB dengan status Pending dan tiket otomatis', function () {
        $dto = new ReportData(
            title: 'Kran Air Patah',
            description: 'Kran air di toilet lantai 1 patah dan air mengalir deras.',
            locationId: $this->location->id,
            buildingId: $this->building->id,
            roomId: null,
            facilityId: $this->facility->id,
            damageCategoryId: $this->damageCategory->id,
            priorityId: $this->priority->id,
        );

        $report = $this->service->store($dto, $this->reporter);

        expect($report)->toBeInstanceOf(Report::class)
            ->and($report->id)->not->toBeNull()
            ->and($report->title)->toBe('Kran Air Patah')
            ->and($report->status)->toBe(ReportStatus::Pending)
            ->and($report->reporter_id)->toBe($this->reporter->id)
            ->and($report->ticket_number)->toMatch('/^MNF-\d{8}-\d{4}$/')
            ->and($report->verified_by)->toBeNull()
            ->and($report->verified_at)->toBeNull();

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'ticket_number' => $report->ticket_number,
            'status' => 'pending',
            'reporter_id' => $this->reporter->id,
        ]);
    });
});

// =========================================================
// Pembaruan Laporan (update)
// =========================================================

describe('Pembaruan Laporan (update)', function () {
    test('dapat memperbarui laporan yang berstatus Pending', function () {
        $report = Report::factory()->create([
            'reporter_id' => $this->reporter->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
            'status' => ReportStatus::Pending,
            'title' => 'Judul Lama',
        ]);

        $dto = new ReportData(
            title: 'Judul Baru yang Diperbarui',
            description: 'Deskripsi baru.',
            locationId: $this->location->id,
            buildingId: $this->building->id,
            roomId: null,
            facilityId: $this->facility->id,
            damageCategoryId: $this->damageCategory->id,
            priorityId: $this->priority->id,
        );

        $updated = $this->service->update($report, $dto);

        expect($updated->title)->toBe('Judul Baru yang Diperbarui')
            ->and($updated->status)->toBe(ReportStatus::Pending);

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'title' => 'Judul Baru yang Diperbarui',
        ]);
    });

    test('gagal memperbarui laporan jika status bukan Pending', function () {
        $report = Report::factory()->create([
            'reporter_id' => $this->reporter->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
            'status' => ReportStatus::Verified,
        ]);

        $dto = new ReportData(
            title: 'Coba Update',
            description: 'Deskripsi.',
            locationId: $this->location->id,
            buildingId: $this->building->id,
            roomId: null,
            facilityId: $this->facility->id,
            damageCategoryId: $this->damageCategory->id,
            priorityId: $this->priority->id,
        );

        expect(fn () => $this->service->update($report, $dto))
            ->toThrow(InvalidArgumentException::class, 'Hanya laporan berstatus menunggu verifikasi (Pending) yang dapat diperbarui.');
    });
});

// =========================================================
// Alur Verifikasi Supervisor: Setuju (verify)
// =========================================================

describe('Alur Verifikasi Supervisor: Setuju (verify)', function () {
    test('supervisor dapat menyetujui laporan pending dan memicu notifikasi ke pelapor', function () {
        $now = Carbon::create(2026, 11, 13, 10, 0, 0);
        Carbon::setTestNow($now);

        $report = Report::factory()->create([
            'reporter_id' => $this->reporter->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
            'status' => ReportStatus::Pending,
        ]);

        $verifiedReport = $this->service->verify($report, $this->supervisor);

        expect($verifiedReport->status)->toBe(ReportStatus::Verified)
            ->and($verifiedReport->verified_by)->toBe($this->supervisor->id)
            ->and($verifiedReport->verified_at->toDateTimeString())->toBe($now->toDateTimeString())
            ->and($verifiedReport->rejection_reason)->toBeNull();

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => 'verified',
            'verified_by' => $this->supervisor->id,
        ]);

        // Verifikasi pemicu notifikasi ke pelapor
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->reporter->id,
            'type' => NotificationType::ReportStatusChanged->value,
            'title' => 'Laporan Diverifikasi',
        ]);

        Carbon::setTestNow();
    });

    test('supervisor dapat menyesuaikan tingkat prioritas SLA saat memverifikasi laporan', function () {
        $newPriority = ReportPriority::factory()->high()->create();

        $report = Report::factory()->create([
            'reporter_id' => $this->reporter->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
            'status' => ReportStatus::Pending,
        ]);

        $verifiedReport = $this->service->verify($report, $this->supervisor, $newPriority->id);

        expect($verifiedReport->priority_id)->toBe($newPriority->id)
            ->and($verifiedReport->status)->toBe(ReportStatus::Verified);

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'priority_id' => $newPriority->id,
            'status' => 'verified',
        ]);
    });

    test('gagal memverifikasi jika prioritas penyesuaian SLA tidak valid', function () {
        $report = Report::factory()->create([
            'reporter_id' => $this->reporter->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
            'status' => ReportStatus::Pending,
        ]);

        expect(fn () => $this->service->verify($report, $this->supervisor, 99999))
            ->toThrow(InvalidArgumentException::class, 'Prioritas SLA yang dipilih tidak valid.');
    });

    test('gagal memverifikasi laporan yang statusnya bukan Pending', function () {
        $report = Report::factory()->create([
            'reporter_id' => $this->reporter->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
            'status' => ReportStatus::Verified,
        ]);

        expect(fn () => $this->service->verify($report, $this->supervisor))
            ->toThrow(InvalidArgumentException::class, 'Hanya laporan berstatus menunggu verifikasi yang dapat disetujui.');
    });
});

// =========================================================
// Alur Verifikasi Supervisor: Tolak (reject)
// =========================================================

describe('Alur Verifikasi Supervisor: Tolak (reject)', function () {
    test('supervisor dapat menolak laporan pending dengan alasan dan memicu notifikasi ke pelapor', function () {
        $now = Carbon::create(2026, 11, 13, 11, 0, 0);
        Carbon::setTestNow($now);

        $report = Report::factory()->create([
            'reporter_id' => $this->reporter->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
            'status' => ReportStatus::Pending,
        ]);

        $reason = 'Laporan duplikat dengan tiket #MNF-20261113-0001.';
        $rejectedReport = $this->service->reject($report, $this->supervisor, $reason);

        expect($rejectedReport->status)->toBe(ReportStatus::Rejected)
            ->and($rejectedReport->verified_by)->toBe($this->supervisor->id)
            ->and($rejectedReport->verified_at->toDateTimeString())->toBe($now->toDateTimeString())
            ->and($rejectedReport->rejection_reason)->toBe($reason);

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => 'rejected',
            'verified_by' => $this->supervisor->id,
            'rejection_reason' => $reason,
        ]);

        // Verifikasi pemicu notifikasi ke pelapor beserta alasannya
        $notification = Notification::where('user_id', $this->reporter->id)->latest('id')->first();
        expect($notification)->not->toBeNull()
            ->and($notification->type)->toBe(NotificationType::ReportStatusChanged)
            ->and($notification->title)->toBe('Laporan Ditolak')
            ->and($notification->message)->toContain($reason);

        Carbon::setTestNow();
    });

    test('gagal menolak laporan jika alasan penolakan kosong', function () {
        $report = Report::factory()->create([
            'reporter_id' => $this->reporter->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
            'status' => ReportStatus::Pending,
        ]);

        expect(fn () => $this->service->reject($report, $this->supervisor, '   '))
            ->toThrow(InvalidArgumentException::class, 'Alasan penolakan laporan wajib diisi.');
    });

    test('gagal menolak laporan yang statusnya bukan Pending', function () {
        $report = Report::factory()->create([
            'reporter_id' => $this->reporter->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
            'status' => ReportStatus::Rejected,
        ]);

        expect(fn () => $this->service->reject($report, $this->supervisor, 'Alasan baru.'))
            ->toThrow(InvalidArgumentException::class, 'Hanya laporan berstatus menunggu verifikasi yang dapat ditolak.');
    });
});

// =========================================================
// handleVerification, delete & pencarian
// =========================================================

describe('Metode Tambahan ReportService', function () {
    test('handleVerification memanggil alur verifikasi dengan action verify', function () {
        $report = Report::factory()->create([
            'reporter_id' => $this->reporter->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
            'status' => ReportStatus::Pending,
        ]);

        $result = $this->service->handleVerification($report, $this->supervisor, 'verify');

        expect($result->status)->toBe(ReportStatus::Verified);
    });

    test('handleVerification memanggil alur penolakan dengan action reject', function () {
        $report = Report::factory()->create([
            'reporter_id' => $this->reporter->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
            'status' => ReportStatus::Pending,
        ]);

        $result = $this->service->handleVerification($report, $this->supervisor, 'reject', 'Kerusakan tidak ditemukan.');

        expect($result->status)->toBe(ReportStatus::Rejected)
            ->and($result->rejection_reason)->toBe('Kerusakan tidak ditemukan.');
    });

    test('handleVerification melempar exception untuk aksi tidak dikenal', function () {
        $report = Report::factory()->create([
            'reporter_id' => $this->reporter->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
            'status' => ReportStatus::Pending,
        ]);

        expect(fn () => $this->service->handleVerification($report, $this->supervisor, 'invalid_action'))
            ->toThrow(InvalidArgumentException::class, "Aksi verifikasi 'invalid_action' tidak dikenal.");
    });

    test('delete berhasil menghapus laporan Pending', function () {
        $report = Report::factory()->create([
            'reporter_id' => $this->reporter->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
            'status' => ReportStatus::Pending,
        ]);

        $result = $this->service->delete($report);

        expect($result)->toBeTrue();
        $this->assertDatabaseMissing('reports', ['id' => $report->id]);
    });

    test('delete melempar exception jika status bukan Pending', function () {
        $report = Report::factory()->create([
            'reporter_id' => $this->reporter->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
            'status' => ReportStatus::Completed,
        ]);

        expect(fn () => $this->service->delete($report))
            ->toThrow(InvalidArgumentException::class, 'Hanya laporan berstatus menunggu verifikasi (Pending) yang dapat dihapus.');
    });

    test('getPaginatedList memfilter berdasarkan search dan status', function () {
        $report1 = Report::factory()->create([
            'title' => 'Lampu Koridor Putus',
            'reporter_id' => $this->reporter->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
            'status' => ReportStatus::Pending,
        ]);

        $report2 = Report::factory()->create([
            'title' => 'AC Bocor',
            'reporter_id' => $this->reporter->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
            'status' => ReportStatus::Verified,
        ]);

        $list = $this->service->getPaginatedList(['search' => 'Koridor']);
        expect($list->total())->toBe(1)
            ->and($list->first()->id)->toBe($report1->id);

        $listStatus = $this->service->getPaginatedList(['status' => 'verified']);
        expect($listStatus->total())->toBe(1)
            ->and($listStatus->first()->id)->toBe($report2->id);
    });

    test('findByTicketNumber menemukan laporan yang tepat', function () {
        $report = Report::factory()->create([
            'ticket_number' => 'MNF-20261113-9999',
            'reporter_id' => $this->reporter->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
        ]);

        $found = $this->service->findByTicketNumber('MNF-20261113-9999');

        expect($found)->not->toBeNull()
            ->and($found->id)->toBe($report->id);
    });
});
