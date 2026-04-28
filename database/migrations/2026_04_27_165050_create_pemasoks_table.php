<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemasoks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_usaha');
            $table->string('nama_pemilik');
            $table->string('no_wa');
            $table->string('email')->nullable();
            $table->text('alamat');
            $table->integer('kapasitas_produksi_harian')->default(0);
            
            // ⚡ Fitur Halal Transparan
            $table->boolean('is_halal')->default(false);
            $table->string('no_sertifikat_halal')->nullable();
            
            $table->text('deskripsi')->nullable();
            $table->string('foto_dapur')->nullable(); // Untuk survey visual
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemasoks');
    }
};