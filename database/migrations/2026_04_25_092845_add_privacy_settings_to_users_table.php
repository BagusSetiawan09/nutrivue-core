<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('visibilitas_medis')->default(true)->after('alergi');
            $table->boolean('pelacakan_lokasi')->default(true)->after('visibilitas_medis');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['visibilitas_medis', 'pelacakan_lokasi']);
        });
    }
};