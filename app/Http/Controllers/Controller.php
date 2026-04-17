<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * Pengaturan Kontroler Autentikasi API
 * Mengelola gerbang pendaftaran masuk dan keluar akun untuk integrasi aplikasi mobile
 */
class AuthController
{
    /**
     * Menangani pendaftaran pengguna baru melalui aplikasi klien
     * Melakukan validasi data menyeluruh sebelum proses penyimpanan ke basis data
     */
    public function register(Request $request)
    {
        // Proses validasi integritas data masukan dari perangkat pengguna
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'kategori' => 'required|string',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|string',
            'alamat' => 'required|string',
            'phone' => 'required|string',
        ]);

        // Penanganan respon jika data masukan tidak memenuhi syarat validasi
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Proses validasi data gagal dilakukan',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Pencatatan informasi profil pengguna baru ke dalam sistem
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password), 
                'kategori' => $request->kategori,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'phone' => $request->phone,
            ]);

            // Penerbitan token akses otomatis melalui mekanisme laravel sanctum
            $token = $user->createToken('auth_token')->plainTextToken;

            // Mengirimkan respon sukses pendaftaran beserta token otentikasi
            return response()->json([
                'status' => 'success',
                'message' => 'Pendaftaran akun berhasil diselesaikan',
                'data' => $user,
                'token' => $token
            ], 201);

        } catch (\Exception $e) {
            // Menangani kegagalan teknis pada operasional server internal
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kendala teknis pada server operasional',
                'error_detail' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menangani permintaan akses masuk pengguna ke dalam sistem
     * Memvalidasi kredensial keamanan dan menghasilkan token sesi aktif
     */
    public function login(Request $request)
    {
        // Validasi kelengkapan atribut email dan kata sandi pengguna
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Alamat email dan kata sandi wajib diisi secara lengkap'
            ], 422);
        }

        // Verifikasi keberadaan identitas pengguna dalam database pusat
        $user = User::where('email', $request->email)->first();

        // Validasi kecocokan identitas akun dan enkripsi kata sandi
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Alamat email atau kata sandi tidak ditemukan'
            ], 401);
        }

        // Penerbitan token akses sesi baru bagi perangkat pengguna
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Proses masuk ke dalam sistem berhasil',
            'data' => $user,
            'token' => $token
        ], 200);
    }

    /**
     * Menangani proses pengakhiran sesi akses pengguna
     * Melakukan terminasi pada token akses yang sedang digunakan pada perangkat
     */
    public function logout(Request $request)
    {
        // Penghapusan data token akses aktif dari sistem keamanan sanctum
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Sesi akses berhasil diakhiri secara aman'
        ], 200);
    }
}