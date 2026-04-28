<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MenuFactory extends Factory
{
    public function definition(): array
    {
        $faker = fake('id_ID');
        return [
            'nama_menu' => $faker->randomElement(['Nasi Ayam Teriyaki', 'Bubur Kacang Hijau', 'Nasi Ikan Kembung', 'Susu Formula & Biskuit', 'Nasi Telur Sayur Bayam']),
            'tanggal_distribusi' => $faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'target_penerima' => $faker->randomElement(['Siswa', 'Balita', 'Ibu Hamil']),
            // titik_penyaluran_id akan disuntik dari seeder
            'foto_makanan' => null,
            'deskripsi' => 'Paket makanan bergizi seimbang sesuai standar Kemenkes.',
            'kalori' => $faker->numberBetween(300, 700),
            'protein' => $faker->numberBetween(10, 40),
            'karbohidrat' => $faker->numberBetween(30, 80),
            'lemak' => $faker->numberBetween(5, 20),
            'status' => $faker->randomElement(['Menunggu', 'Sedang Dikirim', 'Selesai']),
        ];
    }
}