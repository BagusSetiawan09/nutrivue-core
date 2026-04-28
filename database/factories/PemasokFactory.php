<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pemasok>
 */
class PemasokFactory extends Factory
{
    public function definition(): array
    {
        // Aktifkan mode lokal Indonesia
        $fakerIndo = fake('id_ID');
        
        // Peluang 80% pemasok memiliki sertifikat Halal
        $isHalal = $fakerIndo->boolean(80); 

        return [
            'nama_usaha' => $fakerIndo->company() . ' Catering',
            'nama_pemilik' => $fakerIndo->name(),
            'no_wa' => $fakerIndo->phoneNumber(),
            'email' => $fakerIndo->unique()->companyEmail(),
            'alamat' => $fakerIndo->address(),
            'kapasitas_produksi_harian' => $fakerIndo->numberBetween(100, 2000), // Antara 100 s.d 2000 porsi
            'is_halal' => $isHalal,
            'no_sertifikat_halal' => $isHalal ? 'ID' . $fakerIndo->numerify('################') : null,
            'file_sertifikat_halal' => null,
            'foto_dapur' => null,
            'deskripsi' => 'Kami adalah pemasok bahan makanan segar dan katering siap saji yang telah beroperasi selama lebih dari 5 tahun melayani pesanan partai besar.',
            
            // SUNTIKAN DATA REPEATER (JSON) UNTUK BAHAN BAKU
            'bahan_baku_tersedia' => [
                [
                    'nama_bahan' => 'Beras Premium',
                    'kuantitas' => $fakerIndo->numberBetween(50, 500),
                    'satuan' => 'Kg',
                ],
                [
                    'nama_bahan' => 'Daging Ayam Segar',
                    'kuantitas' => $fakerIndo->numberBetween(20, 150),
                    'satuan' => 'Kg',
                ],
                [
                    'nama_bahan' => 'Sayur Mayur Campur',
                    'kuantitas' => $fakerIndo->numberBetween(10, 100),
                    'satuan' => 'Ikat',
                ]
            ],
        ];
    }
}