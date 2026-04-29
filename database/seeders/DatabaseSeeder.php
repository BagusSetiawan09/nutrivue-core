<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. AKUN STAFF KHUSUS (@nutrivueapp.com)
        $staffs = [
            [
                'name' => 'Bagus Setiawan, S.Kom',
                'email' => 'superadmin@nutrivueapp.com',
                'role' => 'super_admin',
            ],
            [
                'name' => 'Drs. H. Ahmad Pejabat',
                'email' => 'pemerintah@nutrivueapp.com',
                'role' => 'pemerintah',
            ],
            [
                'name' => 'Sandi Operasional',
                'email' => 'petugas@nutrivueapp.com',
                'role' => 'petugas',
            ],
        ];

        foreach ($staffs as $staff) {
            User::create([
                'name' => $staff['name'],
                'email' => $staff['email'],
                'password' => Hash::make('password123'),
                'role' => $staff['role'],
                'phone' => '0812' . rand(1000, 9999) . rand(100, 999),
                'alamat' => 'Kantor Dinas Kesehatan Kota Medan',
            ]);
        }

        // 2. AKUN MASYARAKAT REAL (15 Orang - @gmail.com)
        $masyarakatNames = [
            'Budi Santoso', 'Siti Aminah', 'Andi Wijaya', 'Rina Permata', 
            'Fajar Ramadhan', 'Dewi Lestari', 'Eko Prasetyo', 'Maya Indah',
            'Rizky Pratama', 'Larasati Putri', 'Hendra Gunawan', 'Ani Maryani',
            'Dedi Kurniawan', 'Yanti Susanti', 'Agus Prayitno'
        ];

        foreach ($masyarakatNames as $index => $name) {
            User::create([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . '@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'masyarakat',
                'kategori' => collect(['Siswa', 'Balita', 'Ibu Hamil'])->random(),
                'tempat_lahir' => 'Medan',
                'tanggal_lahir' => now()->subYears(rand(5, 35))->format('Y-m-d'),
                'alamat' => 'Jl. Helvetia Raya No. ' . ($index + 1) . ', Medan Helvetia',
                'phone' => '0852' . rand(10000000, 99999999),
            ]);
        }

        // 3. PANGGIL SEEDER LAIN
        $this->call([
            UserRoleSeeder::class,
            MenuSeeder::class,
            ReviewSeeder::class,
            FaqSeeder::class,
            PemasokSeeder::class,
            TitikPenyaluranSeeder::class,
            MenuSeeder::class,
            ReviewSeeder::class,
            FaqSeeder::class,
        ]);
    }
}