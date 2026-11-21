<?php

use App\Enums\CampaignStatus;
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
        Schema::create('maintenance_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('building_id')->constrained('buildings')->cascadeOnDelete();
            $table->foreignId('facility_category_id')->constrained('facility_categories')->cascadeOnDelete();
            $table->string('title', 200);
            $table->text('objective');
            $table->text('content');
            $table->string('banner_path', 255)->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status', 20)->default(CampaignStatus::Scheduled->value);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_campaigns');
    }
};
