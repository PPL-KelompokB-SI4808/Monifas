<?php

use App\Enums\NotificationType;
use App\Enums\ReportStatus;
use App\Enums\UserRole;
use App\Models\Building;
use App\Models\DamageCategory;
use App\Models\Facility;
use App\Models\Location;
use App\Models\Notification;
use App\Models\Report;
use App\Models\ReportPriority;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->location = Location::factory()->create(['name' => 'Kampus Pusat']);
    $this->building = Building::factory()->create([
        'location_id' => $this->location->id,
        'name' => 'Gedung Rektorat',
    ]);
    $this->room = Room::factory()->create([
        'building_id' => $this->building->id,
        'name' => 'Ruang Rapat 101',
    ]);
    $this->emergencyPriority = ReportPriority::factory()->emergency()->create([
        'name' => 'Darurat Kebocoran',
        'sla_response_hours' => 1,
        'sla_resolution_hours' => 4,
    ]);
    $this->highPriority = ReportPriority::factory()->high()->create([
        'name' => 'Prioritas Tinggi',
        'sla_response_hours' => 2,
        'sla_resolution_hours' => 12,
    ]);
    $this->damageCategory = DamageCategory::factory()->create(['name' => 'Kerusakan Plumbing / Air']);
    $this->facility = Facility::factory()->create([
        'location_id' => $this->location->id,
        'building_id' => $this->building->id,
        'room_id' => $this->room->id,
        'name' => 'Pipa Saluran Utama Lantai 1',
    ]);

    $facilityUserRole = Role::firstOrCreate(
        ['slug' => UserRole::FacilityUser->value],
        ['name' => 'Facility User']
    );
    $supervisorRole = Role::firstOrCreate(
        ['slug' => UserRole::Supervisor->value],
        ['name' => 'Supervisor']
    );

    $this->reporter = User::factory()->create([
        'name' => 'Budi Santoso',
        'email' => 'budi.santoso@monifas.id',
        'role_id' => $facilityUserRole->id,
        'is_active' => true,
    ]);

    $this->supervisor = User::factory()->create([
        'name' => 'Hendra Setiawan',
        'email' => 'hendra.supervisor@monifas.id',
        'role_id' => $supervisorRole->id,
        'is_active' => true,
    ]);
});

// =========================================================
// Siklus Hidup Penuh: Happy Path (Pengajuan -> Verifikasi -> Penyelesaian)
// =========================================================

describe('Siklus Hidup Penuh Laporan (Happy Path)', function () {
    test('alur lengkap: pengajuan oleh pelapor, revisi data awal, verifikasi persetujuan supervisor, hingga penutupan', function () {
        // --- 1. Pengajuan Laporan oleh Pelapor ---
        $submissionTime = Carbon::create(2026, 11, 13, 8, 30, 0);
        Carbon::setTestNow($submissionTime);

        $payload = [
            'title' => 'Pipa Air Utama Pecah dan Membanjiri Koridor',
            'description' => 'Air meluap deras dari dinding pipa sambungan utama lantai 1.',
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'room_id' => $this->room->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->emergencyPriority->id,
        ];

        $createResponse = $this->actingAs($this->reporter)
            ->post(route('reporting.reports.store'), $payload);

        $report = Report::where('title', 'Pipa Air Utama Pecah dan Membanjiri Koridor')->first();

        expect($report)->not->toBeNull()
            ->and($report->status)->toBe(ReportStatus::Pending)
            ->and($report->ticket_number)->toMatch('/^MNF-20261113-\d{4}$/')
            ->and($report->verified_by)->toBeNull()
            ->and($report->verified_at)->toBeNull()
            ->and($report->completed_at)->toBeNull();

        $createResponse->assertRedirect(route('reporting.reports.show', $report));

        // --- 2. Revisi Laporan oleh Pelapor saat masih Pending ---
        $revisedTime = Carbon::create(2026, 11, 13, 8, 45, 0);
        Carbon::setTestNow($revisedTime);

        $payload['title'] = 'Pipa Air Utama Pecah - Lantai 1 Tergenang';
        $payload['description'] = 'Air meluap deras dan panel listrik terdekat dimatikan untuk keamanan.';

        $updateResponse = $this->actingAs($this->reporter)
            ->put(route('reporting.reports.update', $report), $payload);

        $updateResponse->assertRedirect(route('reporting.reports.show', $report));
        expect($report->fresh()->title)->toBe('Pipa Air Utama Pecah - Lantai 1 Tergenang');

        // --- 3. Supervisor Memeriksa dan Menyetujui Laporan ---
        $verificationTime = Carbon::create(2026, 11, 13, 9, 15, 0);
        Carbon::setTestNow($verificationTime);

        $verifyResponse = $this->actingAs($this->supervisor)
            ->post(route('reporting.reports.verify', $report), [
                'action' => 'verify',
                'priority_id' => $this->emergencyPriority->id,
            ]);

        $verifyResponse->assertRedirect(route('reporting.reports.show', $report));

        $verifiedReport = $report->fresh();
        expect($verifiedReport->status)->toBe(ReportStatus::Verified)
            ->and($verifiedReport->verified_by)->toBe($this->supervisor->id)
            ->and($verifiedReport->verified_at->toDateTimeString())->toBe($verificationTime->toDateTimeString())
            ->and($verifiedReport->rejection_reason)->toBeNull();

        // Verifikasi pemicu notifikasi perubahan status ke pelapor
        $notification = Notification::where('user_id', $this->reporter->id)
            ->where('type', NotificationType::ReportStatusChanged)
            ->latest('id')
            ->first();

        expect($notification)->not->toBeNull()
            ->and($notification->title)->toBe('Laporan Diverifikasi')
            ->and($notification->message)->toContain($verifiedReport->ticket_number);

        // --- 4. Pelapor Tidak Lagi Dapat Mengubah atau Menghapus Laporan yang Sudah Diverifikasi ---
        $forbiddenEditResponse = $this->actingAs($this->reporter)
            ->put(route('reporting.reports.update', $verifiedReport), $payload);
        $forbiddenEditResponse->assertForbidden();

        $forbiddenDeleteResponse = $this->actingAs($this->reporter)
            ->delete(route('reporting.reports.destroy', $verifiedReport));
        $forbiddenDeleteResponse->assertRedirect(route('reporting.reports.show', $verifiedReport))
            ->assertSessionHas('error');

        // --- 5. Penugasan, Pengerjaan, dan Penyelesaian Laporan ---
        expect($verifiedReport->status->canBeAssigned())->toBeTrue();

        // Transisi status ke Assigned (Ditugaskan)
        $verifiedReport->update(['status' => ReportStatus::Assigned]);
        expect($verifiedReport->fresh()->status)->toBe(ReportStatus::Assigned);
        expect(Report::open()->where('id', $verifiedReport->id)->exists())->toBeTrue();

        // Transisi status ke InProgress (Dalam Pengerjaan)
        $verifiedReport->update(['status' => ReportStatus::InProgress]);
        expect($verifiedReport->fresh()->status)->toBe(ReportStatus::InProgress);
        expect(Report::open()->where('id', $verifiedReport->id)->exists())->toBeTrue();

        // Transisi status ke Completed (Selesai)
        $completionTime = Carbon::create(2026, 11, 13, 12, 0, 0);
        Carbon::setTestNow($completionTime);

        $verifiedReport->update([
            'status' => ReportStatus::Completed,
            'completed_at' => $completionTime,
        ]);

        $completedReport = $verifiedReport->fresh();
        expect($completedReport->status)->toBe(ReportStatus::Completed)
            ->and($completedReport->status->isClosed())->toBeTrue()
            ->and($completedReport->completed_at->toDateTimeString())->toBe($completionTime->toDateTimeString());

        // Laporan yang selesai tidak lagi masuk ke scope open
        expect(Report::open()->where('id', $completedReport->id)->exists())->toBeFalse();

        Carbon::setTestNow();
    });
});

// =========================================================
// Siklus Hidup Penolakan: Rejection Flow
// =========================================================

describe('Siklus Hidup Penolakan Laporan (Rejection Flow)', function () {
    test('alur lengkap: pengajuan laporan, penolakan oleh supervisor dengan alasan, dan notifikasi ke pelapor', function () {
        $submissionTime = Carbon::create(2026, 11, 13, 10, 0, 0);
        Carbon::setTestNow($submissionTime);

        $report = Report::factory()->create([
            'title' => 'Kerusakan Genset Darurat',
            'reporter_id' => $this->reporter->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->emergencyPriority->id,
            'status' => ReportStatus::Pending,
        ]);

        // Supervisor menolak laporan
        $rejectionTime = Carbon::create(2026, 11, 13, 10, 30, 0);
        Carbon::setTestNow($rejectionTime);

        $reason = 'Laporan duplikat dengan tiket sebelumnya dan fasilitas sedang dalam masa garansi vendor resmi.';

        $rejectResponse = $this->actingAs($this->supervisor)
            ->post(route('reporting.reports.verify', $report), [
                'action' => 'reject',
                'rejection_reason' => $reason,
            ]);

        $rejectResponse->assertRedirect(route('reporting.reports.show', $report))
            ->assertSessionHas('success');

        $rejectedReport = $report->fresh();
        expect($rejectedReport->status)->toBe(ReportStatus::Rejected)
            ->and($rejectedReport->verified_by)->toBe($this->supervisor->id)
            ->and($rejectedReport->verified_at->toDateTimeString())->toBe($rejectionTime->toDateTimeString())
            ->and($rejectedReport->rejection_reason)->toBe($reason)
            ->and($rejectedReport->status->isClosed())->toBeTrue();

        // Notifikasi ke pelapor memuat alasan penolakan
        $notification = Notification::where('user_id', $this->reporter->id)
            ->where('type', NotificationType::ReportStatusChanged)
            ->latest('id')
            ->first();

        expect($notification)->not->toBeNull()
            ->and($notification->title)->toBe('Laporan Ditolak')
            ->and($notification->message)->toContain($reason);

        // Laporan yang ditolak tidak bisa diverifikasi ulang
        $reVerifyResponse = $this->actingAs($this->supervisor)
            ->post(route('reporting.reports.verify', $rejectedReport), [
                'action' => 'verify',
            ]);

        // Service melempar exception saat diverifikasi ulang
        expect($rejectedReport->fresh()->status)->toBe(ReportStatus::Rejected);

        // Laporan yang ditolak tidak masuk ke scope open
        expect(Report::open()->where('id', $rejectedReport->id)->exists())->toBeFalse();

        Carbon::setTestNow();
    });
});

// =========================================================
// Siklus SLA & Penghitungan Respon Waktu
// =========================================================

describe('Siklus SLA Laporan', function () {
    test('waktu respon verifikasi supervisor dapat diukur terhadap target SLA Response', function () {
        $startTime = Carbon::create(2026, 11, 13, 8, 0, 0);
        Carbon::setTestNow($startTime);

        $report = Report::factory()->create([
            'reporter_id' => $this->reporter->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->emergencyPriority->id,
            'status' => ReportStatus::Pending,
            'created_at' => $startTime,
        ]);

        // Verifikasi dilakukan 45 menit kemudian (dalam batas 1 jam SLA Response)
        $verifyTime = Carbon::create(2026, 11, 13, 8, 45, 0);
        Carbon::setTestNow($verifyTime);

        $this->actingAs($this->supervisor)
            ->post(route('reporting.reports.verify', $report), ['action' => 'verify']);

        $verified = $report->fresh();
        $responseMinutes = (int) $verified->created_at->diffInMinutes($verified->verified_at);

        expect($responseMinutes)->toBe(45);
        expect($responseMinutes)->toBeLessThanOrEqual($this->emergencyPriority->sla_response_hours * 60);

        Carbon::setTestNow();
    });

    test('penyesuaian prioritas oleh supervisor mengubah target SLA laporan', function () {
        $report = Report::factory()->create([
            'reporter_id' => $this->reporter->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->emergencyPriority->id, // Awalnya Darurat (4 jam)
            'status' => ReportStatus::Pending,
        ]);

        // Supervisor menurunkan urgensi ke High Priority (12 jam)
        $this->actingAs($this->supervisor)
            ->post(route('reporting.reports.verify', $report), [
                'action' => 'verify',
                'priority_id' => $this->highPriority->id,
            ]);

        $verified = $report->fresh();
        expect($verified->priority_id)->toBe($this->highPriority->id)
            ->and($verified->priority->sla_resolution_hours)->toBe(12);
    });
});
