<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProfileController
{
    /**
     * Menarik data kesehatan saat halaman dibuka di HP
     */
    public function getHealth(Request $request)
    {
        try {
            $user = $request->user();
            
            return response()->json([
                'status' => 'success',
                'data' => $user
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menarik data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan data kesehatan baru dari HP ke Database
     */
    public function updateHealth(Request $request)
    {
        try {
            $user = $request->user();

            // 1. Validasi data yang masuk dari React Native
            $request->validate([
                'berat_badan' => 'nullable|numeric',
                'tinggi_badan' => 'nullable|numeric',
                'golongan_darah' => 'nullable|string|max:3',
                'catatan_medis' => 'nullable|string',
                'alergi' => 'nullable|array',
            ]);

            // 2. Simpan ke database (Pastikan kolom-kolom ini ada di tabel users Anda)
            $user->update([
                'berat_badan' => $request->berat_badan,
                'tinggi_badan' => $request->tinggi_badan,
                'golongan_darah' => $request->golongan_darah,
                'catatan_medis' => $request->catatan_medis,
                'alergi' => $request->alergi,
            ]);

            // 3. Kirim status sukses ke HP
            return response()->json([
                'status' => 'success',
                'message' => 'Data kesehatan berhasil diperbarui',
                'data' => $user
            ], 200);

        } catch (\Exception $e) {
            Log::error('Gagal update health: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server.'
            ], 500);
        }
    }
}