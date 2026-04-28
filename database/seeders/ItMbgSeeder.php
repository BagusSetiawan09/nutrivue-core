<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ItMbgSeeder extends Seeder
{
    public function run(): void
    {
        // Taktik Perulangan: Mencetak 10 Personel IT MBG sekaligus
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'name' => 'Tim IT MBG ' . $i,
                'email' => 'it' . $i . '@nutrivueapp.com', // Hasil: it1@..., it2@..., dst
                'password' => Hash::make('password123'),
                'role' => 'it_mbg',
                'alamat' => 'Markas Pusat IT MBG Sektor ' . $i,
                'phone' => '0811223344' . str_pad($i, 2, '0', STR_PAD_LEFT),
            ]);
        }
    }
}