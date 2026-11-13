<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_materials', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('repair_id')
                ->constrained('repairs')
                ->cascadeOnDelete();

            $table->string('material_name', 150);
            $table->string('material_type', 100);
            $table->string('unit', 30);
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_materials');
    }
};
