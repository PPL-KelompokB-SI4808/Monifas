```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repairs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('assignment_id')
                ->unique()
                ->constrained('assignments')
                ->cascadeOnDelete();

            $table->foreignId('report_id')
                ->nullable()
                ->constrained('reports')
                ->nullOnDelete();

            $table->foreignId('facility_id')
                ->constrained('facilities')
                ->restrictOnDelete();

            $table->foreignId('officer_id')
                ->constrained('maintenance_officers')
                ->cascadeOnDelete();

            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();

            $table->text('technical_action');
            $table->text('repair_result')->nullable();

            $table->string('resulting_facility_condition', 30)
                ->default('good');

            $table->string('status', 20)->default('draft');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repairs');
    }
};