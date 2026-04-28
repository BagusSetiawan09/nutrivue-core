<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\TitikPenyaluran;
use App\Models\Menu;

class JadwalDistribusiSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tarik semua data staf IT MBG yang ada di database
        $it_mbgs = User::where('role', 'it_mbg')->get();

        if ($it_mbgs->isEmpty()) {
            $this->command->warn('Tidak ada akun IT MBG ditemukan! Jalankan MitraSeeder dulu.');
            return;
        }

        // 2. Looping: Berikan setiap IT MBG jatah lokasi dan menu
        foreach ($it_mbgs as $it) {
            
            // Buat 3 Titik Penyaluran untuk IT ini
            $titikPenyalurans = TitikPenyaluran::factory()->count(3)->create([
                'created_by' => $it->id // Stempel Kepemilikan IT
            ]);

            // Untuk setiap titik penyaluran, buatkan 5 Jadwal Menu
            foreach ($titikPenyalurans as $titik) {
                Menu::factory()->count(5)->create([
                    'titik_penyaluran_id' => $titik->id, // Relasi ke Titik
                    'created_by' => $it->id              // Stempel Kepemilikan IT
                ]);
            }
            
        }

        $this->command->info('Berhasil mendistribusikan Titik Penyaluran dan Menu ke semua IT MBG!');
    }
}