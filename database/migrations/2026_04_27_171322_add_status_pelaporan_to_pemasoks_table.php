<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemasoks', function (Blueprint $table) {
            $table->string('status_akun')->default('Aktif')->after('kapasitas_produksi_harian');
            $table->text('alasan_laporan')->nullable()->after('status_akun');
        });
    }

    public function down(): void
    {
        Schema::table('pemasoks', function (Blueprint $table) {
            $table->dropColumn(['status_akun', 'alasan_laporan']);
        });
    }
};