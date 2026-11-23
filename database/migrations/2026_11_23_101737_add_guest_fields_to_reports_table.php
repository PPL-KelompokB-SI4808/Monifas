<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom guest agar laporan bisa dikirim tanpa akun (publik),
     * dan membuat reporter_id nullable sehingga tidak wajib memiliki akun.
     */
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Ubah reporter_id menjadi nullable (mendukung laporan publik tanpa akun)
            $table->foreignId('reporter_id')->nullable()->change();

            // Informasi pelapor tamu (opsional jika tidak login)
            $table->string('reporter_name', 150)->nullable()->after('reporter_id');
            $table->string('reporter_phone', 30)->nullable()->after('reporter_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['reporter_name', 'reporter_phone']);
            $table->foreignId('reporter_id')->nullable(false)->change();
        });
    }
};
