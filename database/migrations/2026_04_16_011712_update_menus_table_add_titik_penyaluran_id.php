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
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('lokasi_distribusi');
            $table->foreignId('titik_penyaluran_id')->nullable()->constrained('titik_penyalurans')->nullOnDelete();
        });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
