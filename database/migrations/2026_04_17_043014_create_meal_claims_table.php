<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('meal_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('claim_date'); // Menyimpan tanggal pengambilan (contoh: 2026-04-17)
            $table->string('status')->default('claimed'); // status: claimed
            $table->string('mitra_id')->nullable(); // Siapa mitra yang memberikan makanannya
            $table->timestamps();

            // Mencegah 1 user klaim 2 kali di hari yang sama di level database
            $table->unique(['user_id', 'claim_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_claims');
    }
};
