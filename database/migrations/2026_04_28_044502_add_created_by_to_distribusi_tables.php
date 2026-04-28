<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tanam pelacak di tabel Titik Penyaluran
        Schema::table('titik_penyalurans', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('id');
        });

        // Tanam pelacak di tabel Menu
        Schema::table('menus', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('titik_penyalurans', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });
    }
};