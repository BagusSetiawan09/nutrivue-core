<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MitraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. BUAT 10 AKUN IT MBG (MITRA)
        User::factory()->count(10)->create([
            'role' => 'it_mbg',
            'kategori' => null, 
            'password' => Hash::make('rahasia123'), 
            
            // SUNTIKAN DOMAIN KHUSUS & USERNAME INDO
            'email' => fn () => fake('id_ID')->unique()->userName() . '@nutrivueapp.com',
        ])->each(function ($mitra) {
            
            // 2. RANTAI KOMANDO (MAGIC LOOP) UNTUK MASYARAKAT
            User::factory()->count(50)->create([
                'role' => 'masyarakat',
                'created_by' => $mitra->id, // Stempel Kunci
                
                // SUNTIKAN DOMAIN KHUSUS & USERNAME INDO
                'email' => fn () => fake('id_ID')->unique()->userName() . '@gmail.com',
            ]);

        });
    }
}