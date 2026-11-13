<?php

use App\Enums\AssignmentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_id')->nullable()->index();
            // TODO: Add the foreign key after maintenance_schedules is created in PBI-MNT-04.
            $table->unsignedBigInteger('maintenance_schedule_id')->nullable()->index();
            $table->foreignId('officer_id')->constrained('maintenance_officers')->cascadeOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            $table->string('status', 30)->default(AssignmentStatus::Assigned->value);
            $table->timestamp('sla_response_due_at');
            $table->timestamp('sla_resolution_due_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('rejection_or_cancel_reason')->nullable();
            $table->text('instruction_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
