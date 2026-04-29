<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TitikPenyaluran;
use App\Models\User;

class TitikPenyaluranSeeder extends Seeder
{
    public function run(): void
    {
        // Tarik semua akun IT MBG dari database
        $itUsers = User::where('role', 'it_mbg')->get();

        if ($itUsers->isEmpty()) {
            $this->command->info('Tidak ada akun IT MBG ditemukan. Pastikan Anda menjalankan ItMbgSeeder terlebih dahulu untuk membuat 10 akun IT MBG.');
            return;
        }

        // Hapus data titik penyaluran lama agar bersih dan tidak tumpang tindih
        TitikPenyaluran::query()->delete();

        $namaLokasi = ['SDN 101877', 'SMKS PAB 2', 'Posyandu Melati', 'Puskesmas Kasih', 'SDIT Al-Ikhlas', 'SMPN 1 Medan', 'Posyandu Mawar', 'Klinik Sehat'];
        $jalan = ['Jl. Beringin Raya', 'Jl. Veteran', 'Jl. Gatot Subroto', 'Jl. Sudirman', 'Jl. Merdeka'];
        $penanggungJawab = ['Budi Santoso', 'Siti Aminah', 'Andi Wijaya', 'Rina Permata', 'Dewi Lestari', 'Agus Prayitno'];

        // Distribusikan Titik Penyaluran ke masing-masing IT MBG
        foreach ($itUsers as $itUser) {
            // Setiap IT MBG diberikan tanggung jawab mengelola 3 Titik Penyaluran
            for ($i = 0; $i < 3; $i++) {
                
                // Ambil data acak dari gudang data manual
                $lokasiAcak = $namaLokasi[array_rand($namaLokasi)] . ' - Sektor ' . rand(1, 99);
                $jenis = (strpos($lokasiAcak, 'SD') !== false || strpos($lokasiAcak, 'SMP') !== false || strpos($lokasiAcak, 'SMK') !== false) ? 'Sekolah' : 'Posyandu';
                
                TitikPenyaluran::create([
                    // KUNCI ISOLASI DATA: Titik ini mutlak milik IT MBG yang sedang di-loop
                    'created_by' => $itUser->id, 
                    
                    'nama_lokasi' => $lokasiAcak,
                    'jenis_lokasi' => $jenis,
                    'alamat' => $jalan[array_rand($jalan)] . ' No. ' . rand(1, 100),
                    'penanggung_jawab' => $penanggungJawab[array_rand($penanggungJawab)],
                    'kontak_person' => '0812' . rand(10000000, 99999999),
                    'map_url' => 'https://maps.google.com/?q=.1,100.' . rand(100, 999),
                ]);
            }
        }

        $this->command->info('Titik Penyaluran berhasil didistribusikan ke 10 IT MBG secara eksklusif (Tanpa Faker!).');
    }
}