<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController
{
    /**
     * Endpoint untuk Pendaftaran Pengguna Baru (Register)
     */
    public function register(Request $request)
    {
        // 1. Validasi input dari React Native
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

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // 2. Simpan data ke Database
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password), // Enkripsi password
                'kategori' => $request->kategori,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'phone' => $request->phone,
            ]);

            // 3. Buatkan Token Akses menggunakan Sanctum
            $token = $user->createToken('auth_token')->plainTextToken;

            // 4. Kembalikan Response Sukses ke React Native
            return response()->json([
                'status' => 'success',
                'message' => 'Pendaftaran berhasil',
                'data' => $user,
                'token' => $token
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server',
                'error_detail' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Endpoint untuk Masuk (Login)
     */
    public function login(Request $request)
    {
        // 1. Validasi input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email dan Password wajib diisi'
            ], 422);
        }

        // 2. Cek kecocokan Email dan Password di Database
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email atau Kata Sandi salah'
            ], 401);
        }

        // 3. Buatkan Token Akses
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil',
            'data' => $user,
            'token' => $token
        ], 200);
    }

    /**
     * Endpoint untuk Keluar (Logout)
     */
    public function logout(Request $request)
    {
        // Hapus token yang sedang digunakan di perangkat tersebut
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil'
        ], 200);
    }
}