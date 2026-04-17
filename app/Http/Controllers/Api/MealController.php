<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MealClaim;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class MealController
{
    // ==========================================================
    // 1. FUNGSI UNTUK USER: GENERATE QR CODE AMAN (DI HP USER)
    // ==========================================================
    public function generateQr(Request $request)
    {
        $user = $request->user(); // Ambil data user yang sedang login dari Token
        $today = Carbon::today()->toDateString(); // Tanggal hari ini, misal: 2026-04-17

        // Cek apakah user sudah mengambil jatah hari ini
        $alreadyClaimed = MealClaim::where('user_id', $user->id)
                                   ->where('claim_date', $today)
                                   ->exists();

        if ($alreadyClaimed) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah mengambil jatah makan hari ini.'
            ], 403);
        }

        // BUAT PAYLOAD QR ANTI-MALING (Di-enkripsi oleh Laravel)
        // Format rahasia: "ID_USER|TANGGAL_HARI_INI"
        $rawPayload = $user->id . '|' . $today;
        $encryptedQr = Crypt::encryptString($rawPayload);

        return response()->json([
            'status' => 'success',
            'message' => 'QR Code berhasil di-generate',
            'qr_data' => $encryptedQr // String panjang acak ini yang akan diubah jadi Gambar QR di React Native
        ]);
    }

    // ==========================================================
    // 2. FUNGSI UNTUK MITRA: SCAN & VERIFIKASI QR DARI USER
    // ==========================================================
    public function verifyQr(Request $request)
    {
        try {
            // Mitra mengirimkan string QR hasil scan
            $encryptedQr = $request->qr_data; 
            
            // Bongkar enkripsi
            $decryptedPayload = Crypt::decryptString($encryptedQr);
            $payloadParts = explode('|', $decryptedPayload);

            $userId = $payloadParts[0];
            $qrDate = $payloadParts[1];
            $today = Carbon::today()->toDateString();

            // 1. CEK KEDALUWARSA: Apakah ini QR hari ini atau hasil screenshot kemarin?
            if ($qrDate !== $today) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'QR Code Kedaluwarsa! Ini adalah kode untuk tanggal ' . $qrDate
                ], 400);
            }

            // 2. CEK DATABASE: Apakah user ini mencoba double-claim?
            $alreadyClaimed = MealClaim::where('user_id', $userId)
                                       ->where('claim_date', $today)
                                       ->exists();

            if ($alreadyClaimed) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'DITOLAK! Pengguna ini SUDAH mengambil jatah hari ini.'
                ], 403);
            }

            // 3. AMBIL DATA REAL USER UNTUK PENCOCOKAN WAJAH OLEH MITRA
            $user = User::find($userId);

            return response()->json([
                'status' => 'success',
                'message' => 'VERIFIKASI BERHASIL. Silakan cocokkan data penerima.',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'kategori' => $user->kategori,
                    'alamat' => $user->alamat,
                    'tempat_lahir' => $user->tempat_lahir,
                    'photo_url' => $user->photo ? asset('storage/' . $user->photo) : 'no-photo', // Siap untuk fitur foto nanti
                ]
            ]);

        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // Jika seseorang mencoba memalsukan QR dengan string sembarangan
            return response()->json([
                'status' => 'error',
                'message' => 'QR CODE PALSU ATAU TIDAK VALID!'
            ], 400);
        }
    }
}