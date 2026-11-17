<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_officer_skills', function (Blueprint $table) {
            $table->foreignId('maintenance_officer_id')
                ->constrained('maintenance_officers')
                ->cascadeOnDelete();
            $table->foreignId('facility_category_id')
                ->constrained('facility_categories')
                ->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['maintenance_officer_id', 'facility_category_id'], 'officer_skills_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_officer_skills');
    }
};
