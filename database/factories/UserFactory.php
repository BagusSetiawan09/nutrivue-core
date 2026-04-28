<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        // MENGAKTIFKAN MODE LOKAL INDONESIA
        $fakerIndo = fake('id_ID');

        return [
            'name' => $fakerIndo->name(),
            'email' => $fakerIndo->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => 'masyarakat', 
            'kategori' => $fakerIndo->randomElement(['Siswa', 'Balita', 'Ibu Hamil']), 
            'tempat_lahir' => $fakerIndo->city(),
            'tanggal_lahir' => $fakerIndo->dateTimeBetween('-30 years', '-5 years')->format('Y-m-d'),
            'alamat' => $fakerIndo->address(),
            'phone' => $fakerIndo->phoneNumber(),
            'remember_token' => Str::random(10),
        ];
    }
}