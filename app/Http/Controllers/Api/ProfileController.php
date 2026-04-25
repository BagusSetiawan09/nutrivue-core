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

            // 1. Validasi Bypass: Terima string apapun bentuknya
            $request->validate([
                'berat_badan' => 'nullable|numeric',
                'tinggi_badan' => 'nullable|numeric',
                'golongan_darah' => 'nullable|string|max:3',
                'catatan_medis' => 'nullable|string',
                'alergi' => 'nullable', // Dikosongkan aturannya agar lolos
            ]);

            // 2. Assign Manual (Bypass perlindungan $fillable)
            $user->berat_badan = $request->berat_badan;
            $user->tinggi_badan = $request->tinggi_badan;
            $user->golongan_darah = $request->golongan_darah;
            $user->catatan_medis = $request->catatan_medis;

            // 3. JURUS KUNCI: Apapun yang dikirim HP, pastikan tersimpan sebagai teks JSON
            $alergiRaw = $request->input('alergi');
            if (is_array($alergiRaw)) {
                $user->alergi = json_encode($alergiRaw);
            } else {
                $user->alergi = $alergiRaw; 
            }

            // Simpan paksa ke database
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data kesehatan berhasil diperbarui',
                'data' => $user
            ], 200);

        } catch (\Exception $e) {
            Log::error('Gagal update health: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan pengaturan privasi dan keamanan dari aplikasi
     */
    public function updatePrivacy(Request $request)
    {
        try {
            $user = $request->user();

            $request->validate([
                'visibilitas_medis' => 'required|boolean',
                'pelacakan_lokasi' => 'required|boolean',
            ]);

            $user->visibilitas_medis = $request->visibilitas_medis;
            $user->pelacakan_lokasi = $request->pelacakan_lokasi;
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Pengaturan privasi berhasil disinkronisasi',
                'data' => $user
            ], 200);

        } catch (\Exception $e) {
            Log::error('Gagal update privasi: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server saat menyimpan pengaturan'
            ], 500);
        }
    }
}