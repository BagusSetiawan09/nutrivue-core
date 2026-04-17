<?php

namespace App\Http\Controllers\Api;

// use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Validator;

class AuthController
{
    // ==========================================
    // 1. FUNGSI UNTUK MENDAFTAR (REGISTER)
    // ==========================================
    public function register(Request $request)
    {
        try {
            // Memasukkan data ke dalam Database
            $user = User::create([
                'name'          => $request->name,
                'email'         => $request->email,
                'password'      => Hash::make($request->password), // Password wajib dienkripsi
                'kategori'      => $request->kategori,
                'tempat_lahir'  => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat'        => $request->alamat,
                'phone'         => $request->phone,
            ]);

            // Memberikan jawaban sukses ke HP (React Native)
            return response()->json([
                'status'  => 'success',
                'message' => 'Pendaftaran berhasil!',
                'data'    => $user
            ], 201);

        } catch (\Exception $error) {
            // Jika gagal (misal email sudah ada), kirim pesan error
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mendaftar: ' . $error->getMessage()
            ], 500);
        }
    }

    // ==========================================
    // 2. FUNGSI UNTUK MASUK (LOGIN)
    // ==========================================
    public function login(Request $request)
    {
        try {
            // Cari user berdasarkan email yang diinput
            $user = User::where('email', $request->email)->first();

            // Cek apakah user ada DAN apakah password yang diinput cocok dengan di database
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Email atau Kata Sandi salah!'
                ], 401); // 401 = Unauthorized (Ditolak)
            }

            // Jika cocok, buatkan Token Kunci (Sanctum) untuk sesi login ini
            $token = $user->createToken('auth_token')->plainTextToken;

            // Kirim Token dan Data User ke HP
            return response()->json([
                'status'  => 'success',
                'message' => 'Login berhasil!',
                'data'    => $user,
                'token'   => $token // Ini akan disimpan di AsyncStorage HP
            ], 200);

        } catch (\Exception $error) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $error->getMessage()
            ], 500);
        }
    }

    // ==========================================
    // 3. FUNGSI UNTUK KELUAR (LOGOUT)
    // ==========================================
    public function logout(Request $request)
    {
        try {
            // Hapus / hancurkan token yang sedang dipakai saat ini
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Anda telah berhasil keluar.'
            ], 200);

        } catch (\Exception $error) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal keluar: ' . $error->getMessage()
            ], 500);
        }
    }
}