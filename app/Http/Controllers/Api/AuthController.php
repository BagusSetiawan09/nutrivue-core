<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Pengaturan Autentikasi API
 * Mengelola pendaftaran masuk dan keluar akun melalui protokol Sanctum
 */
class AuthController
{
    /**
     * Menangani pendaftaran pengguna baru ke dalam sistem
     * Menyimpan data profil serta melakukan enkripsi pada kata sandi
     */
    public function register(Request $request)
    {
        try {
            // Proses penyimpanan data ke tabel pengguna
            $user = User::create([
                'name'          => $request->name,
                'email'         => $request->email,
                'password'      => Hash::make($request->password), 
                'kategori'      => $request->kategori,
                'tempat_lahir'  => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat'        => $request->alamat,
                'phone'         => $request->phone,
            ]);

            // Mengirim respon sukses pendaftaran dalam format json
            return response()->json([
                'status'  => 'success',
                'message' => 'Pendaftaran akun berhasil dilakukan',
                'data'    => $user
            ], 201);

        } catch (\Exception $error) {
            // Menangani kegagalan proses pendaftaran akun
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal melakukan pendaftaran ' . $error->getMessage()
            ], 500);
        }
    }

    /**
     * Menangani proses masuk pengguna ke dalam sistem
     * Memvalidasi kredensial dan menerbitkan token akses api
     */
    public function login(Request $request)
    {
        try {
            // Identifikasi pengguna melalui alamat email terdaftar
            $user = User::where('email', $request->email)->first();

            // Validasi keberadaan akun dan kecocokan kata sandi
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Alamat email atau kata sandi tidak valid'
                ], 401); 
            }

            // Penerbitan token akses baru melalui Laravel Sanctum
            $token = $user->createToken('auth_token')->plainTextToken;

            // Mengirim respon sukses login beserta token akses
            return response()->json([
                'status'  => 'success',
                'message' => 'Proses masuk sistem berhasil',
                'data'    => $user,
                'token'   => $token 
            ], 200);

        } catch (\Exception $error) {
            // Menangani kesalahan sistem saat proses masuk
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan sistem internal ' . $error->getMessage()
            ], 500);
        }
    }

    /**
     * Menangani proses keluar pengguna dari sistem
     * Menghapus token akses yang digunakan pada sesi aktif
     */
    public function logout(Request $request)
    {
        try {
            // Penghapusan token akses aktif pada perangkat pengguna
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Berhasil keluar dari sesi aplikasi'
            ], 200);

        } catch (\Exception $error) {
            // Menangani kegagalan saat proses penghapusan sesi
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengakhiri sesi akses ' . $error->getMessage()
            ], 500);
        }
    }
}