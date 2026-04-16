<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TitikPenyaluran;
use Faker\Factory as Faker;

class TitikPenyaluranSeeder extends Seeder
{
    public function run(): void
    {
        $dataUtama = [
            [
                'nama_lokasi' => 'SDN 101877 Helvetia',
                'jenis_lokasi' => 'Sekolah',
                'alamat' => 'Jl. Beringin Raya, Helvetia, Kec. Medan Helvetia, Kota Medan, Sumatera Utara',
                'penanggung_jawab' => 'Siti Aminah, S.Pd',
                'kontak_person' => '081234567001',
                'map_url' => 'https://www.google.com/maps/search/?api=1&query=' . urlencode('SDN 101877 Helvetia Medan'),
            ],
            [
                'nama_lokasi' => 'Puskesmas Helvetia',
                'jenis_lokasi' => 'Puskesmas',
                'alamat' => 'Jl. Beringin X No.23, Helvetia, Kec. Medan Helvetia, Kota Medan',
                'penanggung_jawab' => 'dr. Budi Santoso',
                'kontak_person' => '081234567002',
                'map_url' => 'https://www.google.com/maps/search/?api=1&query=' . urlencode('Puskesmas Helvetia Medan'),
            ],
            [
                'nama_lokasi' => 'Posyandu Melati Indah',
                'jenis_lokasi' => 'Posyandu',
                'alamat' => 'Gg. Pembangunan Raya, Tanjung Gusta, Kec. Medan Helvetia, Kota Medan',
                'penanggung_jawab' => 'Bidan Ningsih',
                'kontak_person' => '081234567003',
                'map_url' => 'https://www.google.com/maps/search/?api=1&query=' . urlencode('Posyandu Tanjung Gusta Medan'),
            ],
            [
                'nama_lokasi' => 'SMKS PAB 2 Helvetia', 
                'jenis_lokasi' => 'Sekolah',
                'alamat' => 'Jl. Veteran, Helvetia, Kec. Labuhan Deli, Kabupaten Deli Serdang',
                'penanggung_jawab' => 'Kepala Sekolah SMKS PAB 2',
                'kontak_person' => '081234567004',
                'map_url' => 'https://www.google.com/maps/search/?api=1&query=' . urlencode('SMKS PAB 2 Helvetia Deli Serdang'),
            ],
            [
                'nama_lokasi' => 'SMK Swasta Tritech Indonesia', 
                'jenis_lokasi' => 'Sekolah',
                'alamat' => 'Jl. Bhayangkara No.484, Indra Kasih, Kec. Medan Tembung, Kota Medan',
                'penanggung_jawab' => 'Kepala Sekolah SMK Tritech',
                'kontak_person' => '081234567008',
                'map_url' => 'https://www.google.com/maps/search/?api=1&query=' . urlencode('SMK Tritech Indonesia Medan'),
            ],
        ];

        foreach ($dataUtama as $lokasi) {
            TitikPenyaluran::firstOrCreate(['nama_lokasi' => $lokasi['nama_lokasi']], $lokasi);
        }

        // Generate 45 data dummy dengan link Maps pencarian yang berfungsi
        $faker = Faker::create('id_ID');
        $jenisLokasi = ['Sekolah', 'Posyandu', 'Puskesmas'];

        for ($i = 1; $i <= 45; $i++) {
            $jenis = $faker->randomElement($jenisLokasi);
            
            if ($jenis === 'Sekolah') {
                $nama = $faker->randomElement(['SDN ', 'SMPN ', 'SMKN ']) . $faker->numberBetween(1, 100) . ' ' . $faker->city();
            } elseif ($jenis === 'Posyandu') {
                $nama = 'Posyandu ' . $faker->colorName() . ' ' . $faker->numberBetween(1, 9);
            } else {
                $nama = 'Puskesmas ' . $faker->streetName();
            }

            TitikPenyaluran::firstOrCreate(
                ['nama_lokasi' => $nama],
                [
                    'jenis_lokasi' => $jenis,
                    'alamat' => $faker->address(),
                    'penanggung_jawab' => $faker->name(),
                    'kontak_person' => $faker->phoneNumber(),
                    // Trik magis: Bikin link pencarian Google Maps berdasarkan nama yang di-generate!
                    'map_url' => 'https://www.google.com/maps/search/?api=1&query=' . urlencode($nama . ' Indonesia'),
                ]
            );
        }
    }
}