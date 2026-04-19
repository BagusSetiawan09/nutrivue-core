<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * Pengaturan Autentikasi API
 * Mengelola pendaftaran masuk dan keluar akun melalui protokol Sanctum
 */
class AuthController
{
    /**
     * Menangani pendaftaran pengguna baru ke dalam sistem
     */
    public function register(Request $request)
    {
        // 1. PENJAGA GERBANG (VALIDASI)
        // Mencegah error SQL bocor ke frontend
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users,email', // KUNCI UTAMA: Cek duplikat email
            'password'      => 'required|string|min:8',
            'kategori'      => 'required|string',
            'tempat_lahir'  => 'required|string',
            'tanggal_lahir' => 'required|string',
            'alamat'        => 'required|string',
            'phone'         => 'required|string',
        ], [
            // Pesan error custom dalam Bahasa Indonesia
            'email.unique'   => 'Email ini sudah terdaftar. Silakan gunakan email lain atau coba masuk (Login).',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'password.min'   => 'Kata sandi minimal 8 karakter.',
        ]);

        // 2. JIKA VALIDASI GAGAL (Contoh: Email sudah ada)
        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(), // Mengambil pesan error pertama (misal: "Email ini sudah terdaftar...")
                'errors'  => $validator->errors()
            ], 422); // Gunakan kode 422 Unprocessable Entity
        }

        try {
            // 3. JIKA LOLOS VALIDASI, SIMPAN KE DATABASE
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

            // Mengirim respon sukses
            return response()->json([
                'status'  => 'success',
                'message' => 'Pendaftaran akun berhasil dilakukan',
                'data'    => $user
            ], 201);

        } catch (\Exception $error) {
            // Menangani kegagalan sistem (Database down, dll)
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal melakukan pendaftaran. Kesalahan server.'
            ], 500);
        }
    }

    /**
     * Menangani proses masuk pengguna ke dalam sistem
     */
    public function login(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Alamat email atau kata sandi tidak valid'
                ], 401); 
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status'  => 'success',
                'message' => 'Proses masuk sistem berhasil',
                'data'    => $user,
                'token'   => $token 
            ], 200);

        } catch (\Exception $error) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan sistem internal.'
            ], 500);
        }
    }

    /**
     * Menangani proses keluar pengguna dari sistem
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Berhasil keluar dari sesi aplikasi'
            ], 200);

        } catch (\Exception $error) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengakhiri sesi akses.'
            ], 500);
        }
    }

    /**
     * Menangani pembaruan data profil pengguna
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user(); // Mengambil data user yang sedang login

        // 1. VALIDASI DATA
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            // KUNCI PENTING: Cek duplikat email, KECUALI email milik user ini sendiri
            'email'        => 'required|string|email|max:255|unique:users,email,' . $user->id, 
            'phone'        => 'nullable|string',
            'alamat'       => 'nullable|string',
            'tempat_lahir' => 'nullable|string',
        ], [
            'email.unique'   => 'Email ini sudah terdaftar oleh pengguna lain.',
            'email.required' => 'Alamat email wajib diisi.',
            'name.required'  => 'Nama lengkap wajib diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            // 2. PROSES PEMBARUAN KE DATABASE
            $user->update([
                'name'         => $request->name,
                'email'        => $request->email,
                'phone'        => $request->phone ?? $user->phone,
                'alamat'       => $request->alamat ?? $user->alamat,
                'tempat_lahir' => $request->tempat_lahir ?? $user->tempat_lahir,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Profil berhasil diperbarui',
                'data'    => $user // Mengembalikan data terbaru untuk disimpan di AsyncStorage
            ], 200);

        } catch (\Exception $error) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal memperbarui profil. Kesalahan server.'
            ], 500);
        }
    }
}