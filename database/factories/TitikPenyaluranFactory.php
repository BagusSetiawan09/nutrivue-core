<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TitikPenyaluranFactory extends Factory
{
    public function definition(): array
    {
        $faker = fake('id_ID');
        $jenis = $faker->randomElement(['Sekolah', 'Posyandu', 'Puskesmas']);
        
        $namaLokasi = match($jenis) {
            'Sekolah' => 'SDN ' . $faker->numberBetween(1, 100) . ' ' . $faker->city(),
            'Posyandu' => 'Posyandu Mawar ' . $faker->numberBetween(1, 20),
            'Puskesmas' => 'Puskesmas ' . $faker->city(),
        };

        return [
            'nama_lokasi' => $namaLokasi,
            'jenis_lokasi' => $jenis,
            'kode_rahasia' => strtoupper(Str::random(6)),
            'alamat' => $faker->address(),
            'penanggung_jawab' => $faker->name(),
            'kontak_person' => $faker->phoneNumber(),
            'map_url' => 'https://maps.google.com/?q=' . $faker->latitude() . ',' . $faker->longitude(),
        ];
    }
}