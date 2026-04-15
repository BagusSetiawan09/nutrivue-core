<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use Faker\Factory as Faker;
use Carbon\Carbon;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // Menggunakan Faker untuk menghasilkan data acak
        $faker = Faker::create('id_ID');

        // Daftar menu untuk masing-masing target penerima
        $menuSiswa = ['Nasi Kotak Ayam Bakar', 'Bento Nasi Sayur Telur', 'Nasi Goreng Sayuran', 'Nasi Ikan Dori Asam Manis', 'Sup Ayam Makaroni', 'Nasi Uduk Komplit'];
        $menuBalita = ['Bubur Kacang Hijau', 'Nasi Tim Ayam Kampung', 'Puree Buah dan Susu', 'Bubur Sumsum Kuah Gula Merah', 'Nasi Lembek Sup Salmon', 'Puding Susu Kedelai'];
        $menuIbuHamil = ['Nasi Merah Daging Sapi Lada Hitam', 'Sup Ikan Gabus', 'Nasi Pecel Sayur Lengkap', 'Ayam Rebus Jahe', 'Salad Sayur dan Telur Rebus', 'Sup Kacang Merah Daging'];

        $lokasiSiswa = ['SDN 060891', 'SDN 101877', 'SMPN 1', 'SD Muhammadiyah 01', 'SDIT Al-Ittihadiyah'];
        $lokasiBalita = ['Posyandu Melati', 'Posyandu Mawar', 'Posyandu Anggrek', 'Puskesmas Teladan', 'Puskesmas Tembung'];
        $lokasiIbuHamil = ['Puskesmas Sunggal', 'Klinik Bersalin Bunda', 'Puskesmas Padang Bulan', 'Posyandu Cempaka', 'RSIA Stella Maris'];

        $targets = ['Siswa', 'Balita', 'Ibu Hamil'];

        for ($i = 0; $i < 60; $i++) {
            $target = $faker->randomElement($targets);

            if ($target === 'Siswa') {
                $namaMenu = $faker->randomElement($menuSiswa);
                $lokasi = $faker->randomElement($lokasiSiswa);
            } elseif ($target === 'Balita') {
                $namaMenu = $faker->randomElement($menuBalita);
                $lokasi = $faker->randomElement($lokasiBalita);
            } else {
                $namaMenu = $faker->randomElement($menuIbuHamil);
                $lokasi = $faker->randomElement($lokasiIbuHamil);
            }

            $tanggalDistribusi = Carbon::parse($faker->dateTimeBetween('-1 week', '+2 weeks')->format('Y-m-d'));
            $hariIni = Carbon::today();

            $status = 'Menunggu';
            
            if ($tanggalDistribusi->lessThan($hariIni)) {
                $status = $faker->randomElement(['Selesai', 'Selesai', 'Selesai', 'Menunggu']);
            } elseif ($tanggalDistribusi->equalTo($hariIni)) {
                $status = $faker->randomElement(['Sedang Dikirim', 'Selesai']);
            } else {
                $status = 'Menunggu';
            }

            Menu::create([
                'nama_menu' => $namaMenu,
                'tanggal_distribusi' => $tanggalDistribusi->format('Y-m-d'),
                'target_penerima' => $target,
                'lokasi_distribusi' => $lokasi,
                'status' => $status,
                'foto_makanan' => null, 
                'deskripsi' => 'Menu sehat dan bergizi yang mengandung karbohidrat, protein, dan vitamin sesuai standar Kementerian Kesehatan untuk pemenuhan gizi.',
                'kalori' => $faker->numberBetween(300, 800),
                'protein' => $faker->numberBetween(10, 45),
                'karbohidrat' => $faker->numberBetween(10, 60),
                'lemak' => $faker->numberBetween(10, 60),
            ]);
        }
    }
}