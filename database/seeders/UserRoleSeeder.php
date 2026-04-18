<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Menginisialisasi akun staff fungsional dan data simulasi masyarakat.
     */
    public function run(): void
    {
        // 1. INITIALIZING FUNCTIONAL STAFF ACCOUNTS
        $staffAccounts = [
            [
                'name'  => 'Bagus Setiawan, S.Kom',
                'email' => 'superadmin@nutrivueapp.com',
                'role'  => 'super_admin',
            ],
            [
                'name'  => 'Drs. H. Ahmad Pejabat',
                'email' => 'pemerintah@nutrivueapp.com',
                'role'  => 'pemerintah',
            ],
            [
                'name'  => 'Sandi Operasional',
                'email' => 'petugas@nutrivueapp.com',
                'role'  => 'petugas',
            ],
        ];

        foreach ($staffAccounts as $staff) {
            User::updateOrCreate(
                ['email' => $staff['email']],
                [
                    'name'     => $staff['name'],
                    'password' => Hash::make('password123'),
                    'role'     => $staff['role'],
                    'phone'    => '081260' . rand(1000, 9999),
                    'alamat'   => 'Kantor Dinas Kesehatan, Kota Medan',
                ]
            );
        }

        // 2. GENERATING SIMULATED BENEFICIARY DATA (MASYARAKAT)
        $beneficiaries = [
            'Budi Santoso', 'Siti Aminah', 'Andi Wijaya', 'Rina Permata', 
            'Fajar Ramadhan', 'Dewi Lestari', 'Eko Prasetyo', 'Maya Indah',
            'Rizky Pratama', 'Larasati Putri', 'Hendra Gunawan', 'Ani Maryani',
            'Dedi Kurniawan', 'Yanti Susanti', 'Agus Prayitno'
        ];

        foreach ($beneficiaries as $index => $name) {
            $email = strtolower(str_replace(' ', '.', $name)) . '@gmail.com';
            
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name'          => $name,
                    'password'      => Hash::make('password123'),
                    'role'          => 'masyarakat',
                    'kategori'      => collect(['Siswa', 'Balita', 'Ibu Hamil'])->random(),
                    'tempat_lahir'  => 'Medan',
                    'tanggal_lahir' => now()->subYears(rand(5, 35))->format('Y-m-d'),
                    'alamat'        => 'Jl. Helvetia Raya No. ' . ($index + 1) . ', Medan Helvetia',
                    'phone'         => '085270' . rand(100000, 999999),
                ]
            );
        }
    }
}