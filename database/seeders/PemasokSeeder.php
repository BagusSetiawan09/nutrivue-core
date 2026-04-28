<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pemasok;

class PemasokSeeder extends Seeder
{
    public function run(): void
    {
        // Memerintahkan pabrik mencetak 50 data pemasok fiktif
        Pemasok::factory()->count(50)->create();
    }
}