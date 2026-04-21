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
        Schema::table('users', function (Blueprint $table) {
            // ⚡ Menyuntikkan 5 kolom kesehatan tanpa merusak data lama
            $table->decimal('berat_badan', 5, 2)->nullable()->after('phone');
            $table->decimal('tinggi_badan', 5, 2)->nullable()->after('berat_badan');
            $table->string('golongan_darah', 5)->nullable()->after('tinggi_badan');
            $table->text('catatan_medis')->nullable()->after('golongan_darah');
            $table->text('alergi')->nullable()->after('catatan_medis'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Jika di-rollback, hapus 5 kolom ini saja
            $table->dropColumn([
                'berat_badan', 
                'tinggi_badan', 
                'golongan_darah', 
                'catatan_medis', 
                'alergi'
            ]);
        });
    }
};