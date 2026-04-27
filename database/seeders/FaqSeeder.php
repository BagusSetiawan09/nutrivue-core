<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'Bagaimana cara memindai kode pengambilan gizi?',
                'answer' => 'Buka menu pemindai di halaman beranda lalu arahkan kamera perangkat Anda tepat ke arah kode matriks milik peserta. Sistem akan memverifikasi data secara otomatis.'
            ],
            [
                'question' => 'Apa yang harus dilakukan jika kode QR ditolak sistem?',
                'answer' => 'Penolakan biasanya terjadi jika kode telah kedaluwarsa atau jatah gizi sudah diambil sebelumnya. Pastikan peserta memperbarui halaman aplikasi mereka.'
            ],
            [
                'question' => 'Cara memperbarui informasi alergi medis?',
                'answer' => 'Navigasikan ke menu Profil lalu pilih opsi Data Kesehatan. Anda dapat menyesuaikan indikator alergi dan preferensi diet di halaman tersebut.'
            ],
            [
                'question' => 'Apakah aplikasi membutuhkan koneksi internet?',
                'answer' => 'Benar sekali. NutriVue membutuhkan konektivitas internet yang stabil untuk melakukan sinkronisasi data distribusi waktu nyata.'
            ],
            [
                'question' => 'Jam berapa jadwal menu makanan harian diperbarui?',
                'answer' => 'Menu makanan harian akan diperbarui secara otomatis di dalam aplikasi setiap pukul 06:00 WIB pagi sesuai dengan jadwal dapur pusat.'
            ],
            // --- BATAS 5 PERTAMA ---
            [
                'question' => 'Bagaimana jika siswa lupa membawa HP saat pengambilan?',
                'answer' => 'Petugas dapat melakukan pencarian manual menggunakan Nomor Induk Siswa (NIS) atau nama lengkap di dasbor web administrasi petugas.'
            ],
            [
                'question' => 'Apakah saya bisa mengambil jatah makanan untuk hari esok?',
                'answer' => 'Tidak bisa. Sistem distribusi NutriVue dikunci ketat per hari untuk memastikan kesegaran makanan dan mencegah kecurangan kuota.'
            ],
            [
                'question' => 'Bagaimana cara mengganti kata sandi akun saya?',
                'answer' => 'Buka menu Profil, gulir ke bagian bawah, dan pilih menu "Keamanan & Kata Sandi". Masukkan sandi lama Anda dan buat sandi baru yang kuat.'
            ],
            [
                'question' => 'Apa yang terjadi jika saya melewatkan jadwal makan siang?',
                'answer' => 'Jatah makanan yang tidak diambil hingga pukul 14:00 WIB akan otomatis dialihkan untuk didonasikan ke fasilitas sosial terdekat, dan QR Code Anda akan hangus untuk hari itu.'
            ],
            [
                'question' => 'Mengapa menu yang saya terima terkadang berbeda dengan teman saya?',
                'answer' => 'NutriVue dilengkapi fitur personalisasi gizi. Jika Anda memiliki riwayat alergi tertentu (misal: makanan laut), sistem otomatis memberikan menu alternatif yang aman.'
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create([
                'question' => $faq['question'],
                'answer' => $faq['answer'],
                'is_active' => true
            ]);
        }
    }
}