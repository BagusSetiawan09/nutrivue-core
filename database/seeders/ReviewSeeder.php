<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Menu;
use App\Models\User;
use Faker\Factory as Faker;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Buat 10 akun masyarakat (User) dummy
        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $users[] = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password123'),
            ]);
        }

        // Ambil semua menu yang ada
        $menus = Menu::all();

        // Buat 40 ulasan acak
        $komentarBagus = ['Makanannya enak dan bergizi!', 'Porsinya pas untuk anak saya, terima kasih.', 'Sangat membantu gizi ibu hamil.', 'Sayurnya segar dan ayamnya matang sempurna.', 'Pelayanan sangat baik dan tepat waktu.'];
        $komentarKritik = ['Makanannya agak telat datang hari ini.', 'Sayurnya sedikit layu, mohon diperbaiki.', 'Nasinya agak keras.'];

        for ($i = 0; $i < 40; $i++) {
            $rating = $faker->numberBetween(3, 5);
            $komentar = $rating >= 4 ? $faker->randomElement($komentarBagus) : $faker->randomElement($komentarKritik);

            Review::create([
                'menu_id' => $menus->random()->id,
                'user_id' => $faker->randomElement($users)->id,
                'rating' => $rating,
                'komentar' => $komentar,
                'is_visible' => true,
            ]);
        }
    }
}