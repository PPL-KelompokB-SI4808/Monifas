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
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->location = Location::factory()->create();
    $this->building = Building::factory()->create(['location_id' => $this->location->id]);
    $this->room = Room::factory()->create(['building_id' => $this->building->id]);
    $this->priority = ReportPriority::factory()->emergency()->create();
    $this->damageCategory = DamageCategory::factory()->create();
    $this->facility = Facility::factory()->create([
        'location_id' => $this->location->id,
        'building_id' => $this->building->id,
        'room_id' => $this->room->id,
    ]);

    $facilityUserRole = Role::firstOrCreate(['slug' => UserRole::FacilityUser->value], ['name' => 'Facility User']);
    $supervisorRole = Role::firstOrCreate(['slug' => UserRole::Supervisor->value], ['name' => 'Supervisor']);
    $adminRole = Role::firstOrCreate(['slug' => UserRole::Admin->value], ['name' => 'Administrator']);

    $this->user = User::factory()->create([
        'name' => 'Warga Kampus',
        'email' => 'warga@monifas.id',
        'role_id' => $facilityUserRole->id,
        'is_active' => true,
    ]);

    $this->supervisor = User::factory()->create([
        'name' => 'Pak Supervisor',
        'email' => 'supervisor@monifas.id',
        'role_id' => $supervisorRole->id,
        'is_active' => true,
    ]);

    $this->admin = User::factory()->create([
        'name' => 'Admin Sistem',
        'email' => 'admin@monifas.id',
        'role_id' => $adminRole->id,
        'is_active' => true,
    ]);
});

// =========================================================
// Index & Read
// =========================================================

describe('Halaman Index Pelaporan', function () {
    test('tamu tidak dapat mengakses halaman index dan diarahkan ke login', function () {
        $this->get(route('reporting.reports.index'))
            ->assertRedirect(route('login'));
    });

    test('pengguna terautentikasi dapat melihat daftar laporan', function () {
        Report::factory()->create([
            'title' => 'Lampu Parkir Mati',
            'reporter_id' => $this->user->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
        ]);

        $this->withoutVite()
            ->actingAs($this->user)
            ->get(route('reporting.reports.index'))
            ->assertOk()
            ->assertViewIs('pages.dashboard.reporting.reports.index')
            ->assertSee('Lampu Parkir Mati');
    });

    test('pengguna dapat memfilter laporan berdasarkan status dan pencarian', function () {
        $report1 = Report::factory()->create([
            'title' => 'AC Ruang Kelas Bocor',
            'reporter_id' => $this->user->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
            'status' => ReportStatus::Pending,
        ]);

        $report2 = Report::factory()->create([
            'title' => 'Pipa Saluran Tersumbat',
            'reporter_id' => $this->user->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
            'status' => ReportStatus::Verified,
        ]);

        $this->withoutVite()
            ->actingAs($this->user)
            ->get(route('reporting.reports.index', ['search' => 'Kelas']))
            ->assertOk()
            ->assertSee('AC Ruang Kelas Bocor')
            ->assertDontSee('Pipa Saluran Tersumbat');
    });
});

// =========================================================
// Create & Store
// =========================================================

describe('Pembuatan Laporan (Create & Store)', function () {
    test('pengguna dapat melihat formulir pembuatan laporan', function () {
        $this->withoutVite()
            ->actingAs($this->user)
            ->get(route('reporting.reports.create'))
            ->assertOk()
            ->assertViewIs('pages.dashboard.reporting.reports.create');
    });

    test('pengguna dapat membuat laporan kerusakan baru dan diarahkan ke show', function () {
        $data = [
            'title' => 'Plafon Jebol di Koridor Utama',
            'description' => 'Plafon gypsum roboh terkena rembesan air.',
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'room_id' => $this->room->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('reporting.reports.store'), $data);

        $report = Report::where('title', 'Plafon Jebol di Koridor Utama')->first();
        expect($report)->not->toBeNull()
            ->and($report->status)->toBe(ReportStatus::Pending)
            ->and($report->reporter_id)->toBe($this->user->id);

        $response->assertRedirect(route('reporting.reports.show', $report))
            ->assertSessionHas('success');
    });
});

// =========================================================
// Show Detail
// =========================================================

describe('Detail Laporan (Show)', function () {
    test('pengguna dapat melihat detail laporan', function () {
        $report = Report::factory()->create([
            'title' => 'Pintu Kaca Retak',
            'reporter_id' => $this->user->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
        ]);

        $this->withoutVite()
            ->actingAs($this->user)
            ->get(route('reporting.reports.show', $report))
            ->assertOk()
            ->assertViewIs('pages.dashboard.reporting.reports.show')
            ->assertSee('Pintu Kaca Retak');
    });
});

// =========================================================
// Edit & Update
// =========================================================

describe('Ubah Laporan (Edit & Update)', function () {
    test('pelapor dapat melihat form edit laporan miliknya saat status Pending', function () {
        $report = Report::factory()->create([
            'reporter_id' => $this->user->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
            'status' => ReportStatus::Pending,
        ]);

        $this->withoutVite()
            ->actingAs($this->user)
            ->get(route('reporting.reports.edit', $report))
            ->assertOk()
            ->assertViewIs('pages.dashboard.reporting.reports.edit');
    });

    test('pengguna lain tidak diizinkan membuka halaman edit laporan milik orang lain', function () {
        $facilityUserRole = Role::firstOrCreate(['slug' => UserRole::FacilityUser->value], ['name' => 'Facility User']);
        $otherUser = User::factory()->create(['role_id' => $facilityUserRole->id]);

        $report = Report::factory()->create([
            'reporter_id' => $this->user->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
            'status' => ReportStatus::Pending,
        ]);

        $this->actingAs($otherUser)
            ->get(route('reporting.reports.edit', $report))
            ->assertForbidden();
    });

    test('pelapor dapat memperbarui laporan miliknya saat status Pending', function () {
        $report = Report::factory()->create([
            'title' => 'Judul Awal',
            'reporter_id' => $this->user->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
            'status' => ReportStatus::Pending,
        ]);

        $updateData = [
            'title' => 'Judul Telah Direvisi',
            'description' => 'Deskripsi tambahan.',
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'room_id' => $this->room->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
        ];

        $this->actingAs($this->user)
            ->put(route('reporting.reports.update', $report), $updateData)
            ->assertRedirect(route('reporting.reports.show', $report))
            ->assertSessionHas('success');

        expect($report->fresh()->title)->toBe('Judul Telah Direvisi');
    });

    test('laporan yang sudah diverifikasi tidak dapat dibuka pada form edit', function () {
        $report = Report::factory()->create([
            'reporter_id' => $this->user->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
            'status' => ReportStatus::Verified,
        ]);

        $this->actingAs($this->user)
            ->get(route('reporting.reports.edit', $report))
            ->assertRedirect(route('reporting.reports.show', $report))
            ->assertSessionHas('error');
    });
});

// =========================================================
// Verifikasi Supervisor
// =========================================================

describe('Alur Verifikasi Supervisor (Verify & Reject)', function () {
    test('supervisor dapat memverifikasi laporan pending dan status berubah menjadi Verified', function () {
        $report = Report::factory()->create([
            'reporter_id' => $this->user->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
            'status' => ReportStatus::Pending,
        ]);

        $this->actingAs($this->supervisor)
            ->post(route('reporting.reports.verify', $report), [
                'action' => 'verify',
            ])
            ->assertRedirect(route('reporting.reports.show', $report))
            ->assertSessionHas('success');

        $fresh = $report->fresh();
        expect($fresh->status)->toBe(ReportStatus::Verified)
            ->and($fresh->verified_by)->toBe($this->supervisor->id)
            ->and($fresh->verified_at)->not->toBeNull();
    });

    test('supervisor dapat menyesuaikan tingkat prioritas SLA saat verifikasi disetujui', function () {
        $newPriority = ReportPriority::factory()->high()->create();

        $report = Report::factory()->create([
            'reporter_id' => $this->user->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
            'status' => ReportStatus::Pending,
        ]);

        $this->actingAs($this->supervisor)
            ->post(route('reporting.reports.verify', $report), [
                'action' => 'verify',
                'priority_id' => $newPriority->id,
            ])
            ->assertRedirect(route('reporting.reports.show', $report));

        $fresh = $report->fresh();
        expect($fresh->status)->toBe(ReportStatus::Verified)
            ->and($fresh->priority_id)->toBe($newPriority->id);
    });

    test('supervisor dapat menolak laporan dengan mengisi alasan penolakan', function () {
        $report = Report::factory()->create([
            'reporter_id' => $this->user->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
            'status' => ReportStatus::Pending,
        ]);

        $reason = 'Foto bukti tidak jelas dan lokasi tidak ditemukan.';

        $this->actingAs($this->supervisor)
            ->post(route('reporting.reports.verify', $report), [
                'action' => 'reject',
                'rejection_reason' => $reason,
            ])
            ->assertRedirect(route('reporting.reports.show', $report))
            ->assertSessionHas('success');

        $fresh = $report->fresh();
        expect($fresh->status)->toBe(ReportStatus::Rejected)
            ->and($fresh->rejection_reason)->toBe($reason)
            ->and($fresh->verified_by)->toBe($this->supervisor->id);
    });

    test('warga kampus / pengguna biasa tidak diizinkan memverifikasi laporan (403)', function () {
        $report = Report::factory()->create([
            'reporter_id' => $this->user->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
            'status' => ReportStatus::Pending,
        ]);

        $this->actingAs($this->user)
            ->post(route('reporting.reports.verify', $report), [
                'action' => 'verify',
            ])
            ->assertForbidden();
    });
});

// =========================================================
// Hapus Laporan (Destroy)
// =========================================================

describe('Hapus Laporan (Destroy)', function () {
    test('pelapor dapat menghapus laporannya sendiri saat status Pending', function () {
        $report = Report::factory()->create([
            'reporter_id' => $this->user->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
            'status' => ReportStatus::Pending,
        ]);

        $this->actingAs($this->user)
            ->delete(route('reporting.reports.destroy', $report))
            ->assertRedirect(route('reporting.reports.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('reports', ['id' => $report->id]);
    });

    test('pengguna lain tidak diizinkan menghapus laporan milik orang lain', function () {
        $facilityUserRole = Role::firstOrCreate(['slug' => UserRole::FacilityUser->value], ['name' => 'Facility User']);
        $otherUser = User::factory()->create(['role_id' => $facilityUserRole->id]);

        $report = Report::factory()->create([
            'reporter_id' => $this->user->id,
            'location_id' => $this->location->id,
            'building_id' => $this->building->id,
            'facility_id' => $this->facility->id,
            'damage_category_id' => $this->damageCategory->id,
            'priority_id' => $this->priority->id,
            'status' => ReportStatus::Pending,
        ]);

        $this->actingAs($otherUser)
            ->delete(route('reporting.reports.destroy', $report))
            ->assertForbidden();
    });
});
