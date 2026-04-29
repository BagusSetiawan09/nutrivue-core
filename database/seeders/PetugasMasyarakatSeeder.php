<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PetugasMasyarakatSeeder extends Seeder
{
    public function run(): void
    {
        // 1. BUMI HANGUS TERSELEKSI: Hapus semua user KECUALI elit (Super Admin, Pemerintah, IT MBG)
        User::whereNotIn('role', ['super_admin', 'pemerintah', 'it_mbg'])->delete();
        
        $this->command->info('Data Masyarakat dan Petugas lama berhasil dibersihkan tanpa menyentuh jajaran elit!');

        // 2. Tarik semua akun IT MBG dari database untuk mulai pembagian
        $itUsers = User::where('role', 'it_mbg')->get();

        if ($itUsers->isEmpty()) {
            $this->command->info('Pasukan IT MBG kosong! Misi dibatalkan.');
            return;
        }

        // 3. Gudang Kosakata Nama (Pengganti Faker)
        $namaDepan = ['Budi', 'Siti', 'Andi', 'Rina', 'Fajar', 'Dewi', 'Eko', 'Maya', 'Rizky', 'Larasati', 'Hendra', 'Ani', 'Dedi', 'Yanti', 'Agus', 'Putra', 'Putri', 'Bagus', 'Ayu', 'Galih', 'Reza', 'Dina', 'Joko', 'Sri'];
        $namaBelakang = ['Santoso', 'Aminah', 'Wijaya', 'Permata', 'Ramadhan', 'Lestari', 'Prasetyo', 'Indah', 'Pratama', 'Gunawan', 'Maryani', 'Kurniawan', 'Susanti', 'Prayitno', 'Setiawan', 'Wahyuni', 'Saputra', 'Sari', 'Hidayat', 'Nugroho', 'Siregar', 'Hasibuan'];
        $kategoriMasyarakat = ['Siswa', 'Balita', 'Ibu Hamil'];

        $totalPetugas = 0;
        $totalMasyarakat = 0;

        // Password seragam agar mudah diuji coba
        $passwordUmum = Hash::make('password123');

        // 4. Distribusi Pasukan ke masing-masing IT MBG
        foreach ($itUsers as $itUser) {
            
            // A. CETAK PETUGAS (6 - 9 Orang per IT MBG)
            $jumlahPetugas = rand(6, 9);
            for ($i = 0; $i < $jumlahPetugas; $i++) {
                $nama = $namaDepan[array_rand($namaDepan)] . ' ' . $namaBelakang[array_rand($namaBelakang)];
                
                User::create([
                    'created_by' => $itUser->id, // ISOLASI DATA: Petugas ini milik IT MBG
                    'name' => $nama . ' (Petugas)',
                    'email' => strtolower(str_replace(' ', '', $nama)) . rand(100, 999) . '@petugas.com',
                    'password' => $passwordUmum,
                    'role' => 'petugas',
                    'phone' => '0812' . rand(10000000, 99999999),
                    'alamat' => 'Kantor Distribusi Sektor ' . $itUser->id,
                ]);
                $totalPetugas++;
            }

            // B. CETAK MASYARAKAT (85 - 95 Orang per IT MBG)
            $jumlahMasyarakat = rand(85, 95);
            for ($i = 0; $i < $jumlahMasyarakat; $i++) {
                $nama = $namaDepan[array_rand($namaDepan)] . ' ' . $namaBelakang[array_rand($namaBelakang)];
                
                User::create([
                    'created_by' => $itUser->id, // ISOLASI DATA: Masyarakat ini diawasi oleh IT MBG
                    'name' => $nama,
                    'email' => strtolower(str_replace(' ', '', $nama)) . rand(1000, 9999) . '@gmail.com',
                    'password' => $passwordUmum,
                    'role' => 'masyarakat',
                    'kategori' => $kategoriMasyarakat[array_rand($kategoriMasyarakat)],
                    'phone' => '0852' . rand(10000000, 99999999),
                    'alamat' => 'Pemukiman Warga Sektor ' . $itUser->id,
                ]);
                $totalMasyarakat++;
            }
        }

        $this->command->info("Misi selesai! $totalPetugas Petugas dan $totalMasyarakat Masyarakat telah mendarat dan diisolasi ke masing-masing IT MBG.");
    }
}