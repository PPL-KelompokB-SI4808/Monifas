<?php

use App\Enums\AssignmentStatus;
use App\Enums\NotificationType;
use App\Enums\ReportStatus;
use App\Models\AreaZone;
use App\Models\Assignment;
use App\Models\FacilityCategory;
use App\Models\MaintenanceOfficer;
use App\Models\Notification;
use App\Models\Report;
use App\Models\ReportPriority;
use App\Models\User;
use App\Services\Maintenance\Assignments\AssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('recommends active available officers with matching skills and coverage below capacity in load order', function () {
    $report = Report::factory()->verified()->create();
    $areaZone = AreaZone::query()->create(['name' => 'Zone 01']);
    $report->building->update(['area_zone_id' => $areaZone->id]);
    $category = FacilityCategory::query()->findOrFail($report->facility->facility_category_id);

    $attachCoverage = function (MaintenanceOfficer $officer, FacilityCategory $skill, Report $coveredReport): void {
        $officer->skills()->attach($skill);
        $officer->zones()->attach(
            $coveredReport->location_id,
            ['area_zone_id' => $coveredReport->building->area_zone_id],
        );
    };

    $leastBusy = MaintenanceOfficer::factory()->create();
    $moreBusy = MaintenanceOfficer::factory()->create(['max_active_tasks' => 2]);
    $atCapacity = MaintenanceOfficer::factory()->create(['max_active_tasks' => 1]);
    $busy = MaintenanceOfficer::factory()->busy()->create();
    $inactive = MaintenanceOfficer::factory()->inactive()->create();
    $wrongSkill = MaintenanceOfficer::factory()->create();
    $wrongZone = MaintenanceOfficer::factory()->create();
    $otherAreaZone = AreaZone::query()->create(['name' => 'Zone 02']);

    foreach ([$leastBusy, $moreBusy, $atCapacity, $busy, $inactive, $wrongSkill, $wrongZone] as $officer) {
        $attachCoverage($officer, $category, $report);
    }

    $wrongSkill->skills()->detach($category);
    $wrongSkill->skills()->attach(FacilityCategory::factory()->create());
    $wrongZone->zones()->detach();
    $wrongZone->zones()->attach($report->location_id, ['area_zone_id' => $otherAreaZone->id]);

    Assignment::factory()->create([
        'officer_id' => $moreBusy->id,
        'status' => AssignmentStatus::InProgress,
    ]);
    Assignment::factory()->create([
        'officer_id' => $atCapacity->id,
        'status' => AssignmentStatus::Assigned,
    ]);

    $recommendedOfficers = app(AssignmentService::class)->recommendOfficersForReport($report);

    expect($recommendedOfficers->modelKeys())->toBe([$leastBusy->id, $moreBusy->id]);
});

it('calculates response and resolution deadlines from the report priority SLA hours', function () {
    $this->travelTo('2026-11-17 10:00:00');
    $priority = ReportPriority::factory()->high()->create();
    $report = Report::factory()->verified()->create(['priority_id' => $priority->id]);

    $deadlines = app(AssignmentService::class)->calculateSlaDeadlines($report);

    expect($deadlines['response_due_at']->toDateTimeString())->toBe('2026-11-17 14:00:00');
    expect($deadlines['resolution_due_at']->toDateTimeString())->toBe('2026-11-18 10:00:00');
});

it('assigns a verified report with its SLA deadlines and notifies the officer and reporter', function () {
    $this->travelTo('2026-11-17 10:00:00');
    $priority = ReportPriority::factory()->emergency()->create();
    $reporter = User::factory()->create();
    $supervisor = User::factory()->create();
    $officer = MaintenanceOfficer::factory()->create();
    $report = Report::factory()->verified()->create([
        'reporter_id' => $reporter->id,
        'priority_id' => $priority->id,
    ]);

    $assignment = app(AssignmentService::class)->assignReport($report, $officer, $supervisor);

    expect($assignment->status)->toBe(AssignmentStatus::Assigned);
    expect($assignment->sla_response_due_at->toDateTimeString())->toBe('2026-11-17 11:00:00');
    expect($assignment->sla_resolution_due_at->toDateTimeString())->toBe('2026-11-17 14:00:00');
    $this->assertDatabaseHas('reports', [
        'id' => $report->id,
        'status' => ReportStatus::Assigned->value,
    ]);
    $this->assertDatabaseHas('notifications', [
        'user_id' => $officer->user_id,
        'type' => NotificationType::NewAssignment->value,
        'title' => 'Penugasan Baru',
    ]);
    $this->assertDatabaseHas('notifications', [
        'user_id' => $reporter->id,
        'type' => NotificationType::ReportStatusChanged->value,
        'title' => 'Status Laporan Berubah',
    ]);
    expect(Notification::query()->count())->toBe(2);
});

it('rejects assigning a report that is not verified without creating records or notifications', function () {
    $report = Report::factory()->create(['status' => ReportStatus::Pending]);
    $officer = MaintenanceOfficer::factory()->create();
    $supervisor = User::factory()->create();

    expect(fn () => app(AssignmentService::class)->assignReport($report, $officer, $supervisor))
        ->toThrow(InvalidArgumentException::class, 'Hanya laporan berstatus diverifikasi yang dapat ditugaskan.');

    expect(Assignment::query()->count())->toBe(0);
    expect(Notification::query()->count())->toBe(0);
});

it('rejects assigning a verified report to an inactive officer without changing the report', function () {
    $report = Report::factory()->verified()->create();
    $officer = MaintenanceOfficer::factory()->inactive()->create();
    $supervisor = User::factory()->create();

    expect(fn () => app(AssignmentService::class)->assignReport($report, $officer, $supervisor))
        ->toThrow(InvalidArgumentException::class, 'Petugas yang dipilih tidak aktif dan tidak dapat menerima penugasan.');

    $this->assertDatabaseHas('reports', [
        'id' => $report->id,
        'status' => ReportStatus::Verified->value,
    ]);
    expect(Assignment::query()->count())->toBe(0);
    expect(Notification::query()->count())->toBe(0);
});

it('reassigns an active assignment and records the trimmed reason', function () {
    $report = Report::factory()->assigned()->create();
    $oldOfficer = MaintenanceOfficer::factory()->create();
    $newOfficer = MaintenanceOfficer::factory()->create();
    $supervisor = User::factory()->create();
    $assignment = Assignment::factory()->create([
        'report_id' => $report->id,
        'officer_id' => $oldOfficer->id,
        'assigned_by' => $supervisor->id,
    ]);

    $newAssignment = app(AssignmentService::class)->reassignAssignment(
        $assignment,
        $newOfficer,
        '  Perlu keahlian khusus.  ',
        $supervisor,
    );

    $this->assertDatabaseHas('assignments', [
        'id' => $assignment->id,
        'status' => AssignmentStatus::Reassigned->value,
        'rejection_or_cancel_reason' => 'Perlu keahlian khusus.',
    ]);
    $this->assertDatabaseHas('assignments', [
        'id' => $newAssignment->id,
        'report_id' => $report->id,
        'officer_id' => $newOfficer->id,
        'status' => AssignmentStatus::Assigned->value,
    ]);
    $this->assertDatabaseHas('reports', [
        'id' => $report->id,
        'status' => ReportStatus::Assigned->value,
    ]);
    $this->assertDatabaseHas('notifications', [
        'user_id' => $newOfficer->user_id,
        'type' => NotificationType::NewAssignment->value,
    ]);
});

it('cancels an active assignment and returns its report to verified with a reason', function () {
    $report = Report::factory()->assigned()->create();
    $officer = MaintenanceOfficer::factory()->create();
    $supervisor = User::factory()->create();
    $assignment = Assignment::factory()->create([
        'report_id' => $report->id,
        'officer_id' => $officer->id,
        'assigned_by' => $supervisor->id,
    ]);

    $cancelledAssignment = app(AssignmentService::class)->cancelAssignment(
        $assignment,
        '  Tidak tersedia material.  ',
        $supervisor,
    );

    expect($cancelledAssignment->status)->toBe(AssignmentStatus::Cancelled);
    $this->assertDatabaseHas('assignments', [
        'id' => $assignment->id,
        'status' => AssignmentStatus::Cancelled->value,
        'rejection_or_cancel_reason' => 'Tidak tersedia material.',
    ]);
    $this->assertDatabaseHas('reports', [
        'id' => $report->id,
        'status' => ReportStatus::Verified->value,
    ]);
    expect(Notification::query()->count())->toBe(2);
});

it('rejects reassigning completed or cancelled assignments', function (AssignmentStatus $status) {
    $assignment = Assignment::factory()->create(['status' => $status]);
    $officer = MaintenanceOfficer::factory()->create();
    $supervisor = User::factory()->create();

    expect(fn () => app(AssignmentService::class)->reassignAssignment(
        $assignment,
        $officer,
        'Pengalihan diperlukan.',
        $supervisor,
    ))->toThrow(InvalidArgumentException::class, 'Penugasan yang sudah selesai atau dibatalkan tidak dapat dialihkan.');
})->with([
    'completed' => AssignmentStatus::Completed,
    'cancelled' => AssignmentStatus::Cancelled,
]);

it('rejects cancelling completed or cancelled assignments', function (AssignmentStatus $status) {
    $assignment = Assignment::factory()->create(['status' => $status]);
    $supervisor = User::factory()->create();

    expect(fn () => app(AssignmentService::class)->cancelAssignment(
        $assignment,
        'Pembatalan diperlukan.',
        $supervisor,
    ))->toThrow(InvalidArgumentException::class, 'Penugasan yang sudah selesai atau dibatalkan tidak dapat dibatalkan lagi.');
})->with([
    'completed' => AssignmentStatus::Completed,
    'cancelled' => AssignmentStatus::Cancelled,
]);

it('returns only verified reports without an active assignment', function () {
    $withoutAssignment = Report::factory()->verified()->create();
    $withCompletedAssignment = Report::factory()->verified()->create();
    $withActiveAssignment = Report::factory()->verified()->create();
    $pendingReport = Report::factory()->create(['status' => ReportStatus::Pending]);
    $assignedReport = Report::factory()->assigned()->create();

    Assignment::factory()->completed()->create(['report_id' => $withCompletedAssignment->id]);
    Assignment::factory()->assigned()->create(['report_id' => $withActiveAssignment->id]);

    $assignableReports = app(AssignmentService::class)->getAssignableReports();

    expect($assignableReports->modelKeys())->toBe([$withoutAssignment->id, $withCompletedAssignment->id]);
    expect($pendingReport->status)->toBe(ReportStatus::Pending);
    expect($assignedReport->status)->toBe(ReportStatus::Assigned);
});
