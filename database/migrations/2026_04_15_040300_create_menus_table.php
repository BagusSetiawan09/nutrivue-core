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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('nama_menu');
            $table->date('tanggal_distribusi');
            $table->enum('target_penerima', ['Siswa', 'Balita', 'Ibu Hamil']);
            $table->string('lokasi_distribusi')->nullable();
            $table->string('foto_makanan')->nullable();
            $table->text('deskripsi')->nullable();
            $table->integer('kalori')->nullable();
            $table->integer('protein')->nullable();
            $table->integer('karbohidrat')->nullable();
            $table->integer('lemak')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
