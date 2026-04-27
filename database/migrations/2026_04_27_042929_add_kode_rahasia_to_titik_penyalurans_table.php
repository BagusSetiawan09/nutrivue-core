<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('titik_penyalurans', function (Blueprint $table) {
            $table->string('kode_rahasia')->nullable()->unique()->after('jenis_lokasi');
        });
    }

    public function down(): void
    {
        Schema::table('titik_penyalurans', function (Blueprint $table) {
            $table->dropColumn('kode_rahasia');
        });
    }
};