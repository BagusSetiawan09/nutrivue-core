<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users,email',
            'password'      => 'required|string|min:8',
            'kategori'      => 'required|string',
            'tempat_lahir'  => 'required|string',
            'tanggal_lahir' => 'required|string',
            'alamat'        => 'required|string',
            'phone'         => 'required|string',
            'kode_instansi' => 'nullable|string',
        ], [
            'email.unique'   => 'Email ini sudah terdaftar silakan gunakan email lain',
            'email.required' => 'Alamat email wajib diisi',
            'password.min'   => 'Kata sandi minimal delapan karakter',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors()
            ], 422);
        }

        // Pengecekan dinamis ke tabel titik_penyalurans
        $namaInstansi = null;
        if (strtolower($request->kategori) === 'siswa') {
            if (empty($request->kode_instansi)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Kode instansi wajib diisi untuk pendaftaran siswa'
                ], 400);
            }

            // Mencari kecocokan kode di tabel Titik Penyaluran
            $instansi = \App\Models\TitikPenyaluran::where('kode_rahasia', $request->kode_instansi)->first();

            if (!$instansi) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Kode identifikasi sekolah tidak valid atau tidak terdaftar'
                ], 400);
            }
            // Jika valid, ambil nama sekolahnya
            $namaInstansi = $instansi->nama_lokasi;
        }

        try {
            $user = User::create([
                'name'          => $request->name,
                'email'         => $request->email,
                'password'      => Hash::make($request->password), 
                'kategori'      => $request->kategori,
                'instansi'      => $namaInstansi,
                'tempat_lahir'  => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat'        => $request->alamat,
                'phone'         => $request->phone,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Pendaftaran akun berhasil dilakukan',
                'data'    => $user
            ], 201);

        } catch (\Exception $error) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal melakukan pendaftaran akibat kesalahan server internal'
            ], 500);
        }
    }

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
                'message' => 'Terjadi kesalahan sistem internal'
            ], 500);
        }
    }

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
                'message' => 'Gagal mengakhiri sesi akses'
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user(); 

        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'email'        => 'required|string|email|max:255|unique:users,email,' . $user->id, 
            'phone'        => 'nullable|string',
            'alamat'       => 'nullable|string',
            'tempat_lahir' => 'nullable|string',
        ], [
            'email.unique'   => 'Email ini sudah terdaftar oleh pengguna lain',
            'email.required' => 'Alamat email wajib diisi',
            'name.required'  => 'Nama lengkap wajib diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
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
                'data'    => $user 
            ], 200);

        } catch (\Exception $error) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal memperbarui profil kesalahan server'
            ], 500);
        }
    }
}