<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemasoks', function (Blueprint $table) {
            // Kolom JSON untuk menyimpan banyak bahan sekaligus (Repeater)
            $table->json('bahan_baku_tersedia')->nullable()->after('kapasitas_produksi_harian');
            // Kolom untuk menyimpan file/foto sertifikat halal
            $table->string('file_sertifikat_halal')->nullable()->after('no_sertifikat_halal');
        });
    }

    public function down(): void
    {
        Schema::table('pemasoks', function (Blueprint $table) {
            $table->dropColumn(['bahan_baku_tersedia', 'file_sertifikat_halal']);
        });
    }
};