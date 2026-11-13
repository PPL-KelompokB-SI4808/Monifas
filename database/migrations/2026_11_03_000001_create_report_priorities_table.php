<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('report_priorities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('level', 20)->unique();
            $table->unsignedInteger('sla_response_hours');
            $table->unsignedInteger('sla_resolution_hours');
            $table->string('badge_variant', 30);
            $table->unsignedTinyInteger('sort_order');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_priorities');
    }
};
