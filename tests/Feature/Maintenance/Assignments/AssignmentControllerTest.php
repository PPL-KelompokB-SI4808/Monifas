<?php

use App\Enums\AssignmentStatus;
use App\Enums\NotificationType;
use App\Enums\ReportStatus;
use App\Enums\UserRole;
use App\Models\Assignment;
use App\Models\MaintenanceOfficer;
use App\Models\Notification;
use App\Models\Report;
use App\Models\ReportPriority;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

function assignmentControllerSupervisor(): User
{
    return User::factory()->withRole(UserRole::Supervisor)->create();
}

it('allows a supervisor to open the assignment index, creation form, and details', function () {
    $supervisor = assignmentControllerSupervisor();
    $report = Report::factory()->verified()->create();
    $assignment = Assignment::factory()->create();

    $indexResponse = $this->withoutVite()
        ->actingAs($supervisor)
        ->get(route('maintenance.assignments.index'));
    $createResponse = $this->withoutVite()
        ->actingAs($supervisor)
        ->get(route('maintenance.assignments.create', $report));
    $showResponse = $this->withoutVite()
        ->actingAs($supervisor)
        ->get(route('maintenance.assignments.show', $assignment));

    $indexResponse->assertOk();
    $createResponse->assertOk();
    $showResponse->assertOk();
});

it('stores an assignment and redirects the supervisor to the index', function () {
    $supervisor = assignmentControllerSupervisor();
    $priority = ReportPriority::factory()->high()->create();
    $officer = MaintenanceOfficer::factory()->create();
    $report = Report::factory()->verified()->create(['priority_id' => $priority->id]);

    $response = $this->actingAs($supervisor)
        ->post(route('maintenance.assignments.store'), [
            'report_id' => $report->id,
            'officer_id' => $officer->id,
            'instruction_notes' => 'Periksa kondisi fasilitas.',
        ]);

    $response->assertRedirect(route('maintenance.assignments.index'));
    $response->assertSessionHas('status', 'Laporan berhasil ditugaskan kepada petugas.');
    $this->assertDatabaseHas('assignments', [
        'report_id' => $report->id,
        'officer_id' => $officer->id,
        'assigned_by' => $supervisor->id,
        'status' => AssignmentStatus::Assigned->value,
        'instruction_notes' => 'Periksa kondisi fasilitas.',
    ]);
    $this->assertDatabaseHas('reports', [
        'id' => $report->id,
        'status' => ReportStatus::Assigned->value,
    ]);
    expect(Notification::query()->where('type', NotificationType::NewAssignment->value)->count())->toBe(1);
});

it('reassigns an assignment and redirects the supervisor to the index', function () {
    $supervisor = assignmentControllerSupervisor();
    $assignment = Assignment::factory()->create();
    $newOfficer = MaintenanceOfficer::factory()->create();

    $response = $this->actingAs($supervisor)
        ->patch(route('maintenance.assignments.reassign', $assignment), [
            'officer_id' => $newOfficer->id,
            'reason' => 'Perlu keahlian khusus.',
        ]);

    $response->assertRedirect(route('maintenance.assignments.index'));
    $response->assertSessionHas('status', 'Penugasan berhasil dialihkan kepada petugas baru.');
    $this->assertDatabaseHas('assignments', [
        'id' => $assignment->id,
        'status' => AssignmentStatus::Reassigned->value,
        'rejection_or_cancel_reason' => 'Perlu keahlian khusus.',
    ]);
    $this->assertDatabaseHas('assignments', [
        'report_id' => $assignment->report_id,
        'officer_id' => $newOfficer->id,
        'status' => AssignmentStatus::Assigned->value,
    ]);
});

it('cancels an assignment and redirects the supervisor to the index', function () {
    $supervisor = assignmentControllerSupervisor();
    $assignment = Assignment::factory()->create();

    $response = $this->actingAs($supervisor)
        ->patch(route('maintenance.assignments.cancel', $assignment), [
            'reason' => 'Perbaikan ditunda.',
        ]);

    $response->assertRedirect(route('maintenance.assignments.index'));
    $response->assertSessionHas('status', 'Penugasan berhasil dibatalkan.');
    $this->assertDatabaseHas('assignments', [
        'id' => $assignment->id,
        'status' => AssignmentStatus::Cancelled->value,
        'rejection_or_cancel_reason' => 'Perbaikan ditunda.',
    ]);
    $this->assertDatabaseHas('reports', [
        'id' => $assignment->report_id,
        'status' => ReportStatus::Verified->value,
    ]);
    expect(Notification::query()->count())->toBe(2);
});

it('returns a validation error when the store request references a missing officer', function () {
    $supervisor = assignmentControllerSupervisor();
    $report = Report::factory()->verified()->create();

    $response = $this->from(route('maintenance.assignments.index'))
        ->actingAs($supervisor)
        ->post(route('maintenance.assignments.store'), [
            'report_id' => $report->id,
            'officer_id' => 999999,
        ]);

    $response->assertRedirect(route('maintenance.assignments.index'));
    $response->assertSessionHasErrors([
        'officer_id' => 'Petugas yang dipilih tidak valid.',
    ]);
    expect(Assignment::query()->count())->toBe(0);
});

it('returns a validation error when a cancellation reason is empty', function () {
    $supervisor = assignmentControllerSupervisor();
    $assignment = Assignment::factory()->create();

    $response = $this->from(route('maintenance.assignments.show', $assignment))
        ->actingAs($supervisor)
        ->patch(route('maintenance.assignments.cancel', $assignment), [
            'reason' => '',
        ]);

    $response->assertRedirect(route('maintenance.assignments.show', $assignment));
    $response->assertSessionHasErrors([
        'reason' => 'Alasan pembatalan penugasan wajib diisi.',
    ]);
    $this->assertDatabaseHas('assignments', [
        'id' => $assignment->id,
        'status' => AssignmentStatus::Assigned->value,
    ]);
});

it('forbids a user without the supervisor role from accessing assignments', function () {
    $technician = User::factory()->withRole(UserRole::Technician)->create();

    $response = $this->actingAs($technician)
        ->get(route('maintenance.assignments.index'));

    $response->assertForbidden();
});
