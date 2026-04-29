<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\TitikPenyaluran;
use Carbon\Carbon;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // 1. BUMI HANGUS: Kosongkan semua data menu lama beserta relasinya
        Menu::query()->delete();

        // 2. Gudang Data Manual Pengganti Faker
        $menuSiswa = ['Nasi Kotak Ayam Bakar', 'Bento Nasi Sayur Telur', 'Nasi Goreng Sayuran', 'Nasi Ikan Dori Asam Manis', 'Sup Ayam Makaroni', 'Nasi Uduk Komplit'];
        $menuBalita = ['Bubur Kacang Hijau', 'Nasi Tim Ayam Kampung', 'Puree Buah dan Susu', 'Bubur Sumsum Kuah Gula Merah', 'Nasi Lembek Sup Salmon', 'Puding Susu Kedelai'];
        $menuIbuHamil = ['Nasi Merah Daging Sapi Lada Hitam', 'Sup Ikan Gabus', 'Nasi Pecel Sayur Lengkap', 'Ayam Rebus Jahe', 'Salad Sayur dan Telur Rebus', 'Sup Kacang Merah Daging'];

        $targets = ['Siswa', 'Balita', 'Ibu Hamil'];
        $statuses = ['Menunggu', 'Sedang Dikirim', 'Selesai'];

        // 3. Ambil semua Titik Penyaluran yang baru saja kita buat tadi
        $titikPenyalurans = TitikPenyaluran::all();

        if ($titikPenyalurans->isEmpty()) {
            $this->command->info('Titik Penyaluran masih kosong! Harap jalankan TitikPenyaluranSeeder terlebih dahulu.');
            return;
        }

        $count = 0;

        // 4. Buatkan menu baru secara eksklusif untuk setiap titik penyaluran
        foreach ($titikPenyalurans as $titik) {
            // Setiap titik penyaluran kita buatkan 3 sampai 5 menu jadwal
            $jumlahMenu = rand(3, 5);

            for ($i = 0; $i < $jumlahMenu; $i++) {
                $target = $targets[array_rand($targets)];
                $namaMenu = '';

                if ($target === 'Siswa') {
                    $namaMenu = $menuSiswa[array_rand($menuSiswa)];
                } elseif ($target === 'Balita') {
                    $namaMenu = $menuBalita[array_rand($menuBalita)];
                } else {
                    $namaMenu = $menuIbuHamil[array_rand($menuIbuHamil)];
                }

                // Tanggal acak antara seminggu lalu sampai 2 minggu ke depan
                $hariOffset = rand(-7, 14);
                $tanggalDistribusi = Carbon::now()->addDays($hariOffset)->format('Y-m-d');

                Menu::create([
                    'created_by'          => $titik->created_by,
                    'titik_penyaluran_id' => $titik->id, 
                    
                    'nama_menu'          => $namaMenu,
                    'tanggal_distribusi' => $tanggalDistribusi,
                    'target_penerima'    => $target,
                    'status'             => $statuses[array_rand($statuses)],
                    'deskripsi'          => 'Menu sehat dan bergizi yang mengandung karbohidrat, protein, dan vitamin sesuai standar Kementerian Kesehatan untuk pemenuhan gizi.',
                    'kalori'             => rand(300, 800),
                    'protein'            => rand(10, 45),
                    'karbohidrat'        => rand(10, 60),
                    'lemak'              => rand(10, 60),
                ]);
                
                $count++;
            }
        }

        $this->command->info($count . ' Menu baru berhasil dibuat dan dikunci sesuai Titik Penyaluran IT MBG masing-masing (Tanpa Faker!).');
    }
}