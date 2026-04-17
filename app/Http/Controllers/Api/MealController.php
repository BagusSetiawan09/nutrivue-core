<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MealClaim;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

/**
 * Pengaturan Kontroler Distribusi Makanan
 * Mengelola pembuatan kode qr bagi pengguna dan verifikasi pindaian oleh mitra lapangan
 */
class MealController
{
    /**
     * Menghasilkan muatan data qr code terenkripsi untuk sisi pengguna
     * Memastikan pembuatan kode hanya berlaku satu kali dalam periode hari yang sama
     */
    public function generateQr(Request $request)
    {
        // Identifikasi pengguna aktif melalui token akses
        $user = $request->user(); 
        $today = Carbon::today()->toDateString(); 

        // Validasi ketersediaan jatah makan berdasarkan tanggal distribusi hari ini
        $alreadyClaimed = MealClaim::where('user_id', $user->id)
                                   ->where('claim_date', $today)
                                   ->exists();

        if ($alreadyClaimed) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda telah tercatat menggunakan jatah makan hari ini'
            ], 403);
        }

        /**
         * Pembuatan muatan data enkripsi tingkat tinggi
         * Menggabungkan identitas pengguna dan tanggal guna mencegah duplikasi data
         */
        $rawPayload = $user->id . '|' . $today;
        $encryptedQr = Crypt::encryptString($rawPayload);

        return response()->json([
            'status' => 'success',
            'message' => 'Kode QR berhasil diterbitkan oleh sistem',
            'qr_data' => $encryptedQr 
        ]);
    }

    /**
     * Menjalankan proses verifikasi data qr code dari sisi mitra
     * Melakukan validasi integritas data masa berlaku dan status pengambilan jatah
     */
    public function verifyQr(Request $request)
    {
        try {
            // Menerima muatan data qr dari perangkat pindaian mitra
            $encryptedQr = $request->qr_data; 
            
            // Dekripsi muatan data rahasia sistem
            $decryptedPayload = Crypt::decryptString($encryptedQr);
            $payloadParts = explode('|', $decryptedPayload);

            $userId = $payloadParts[0];
            $qrDate = $payloadParts[1];
            $today = Carbon::today()->toDateString();

            /**
             * Pemeriksaan masa aktif kode qr
             * Memastikan data yang dipindai bukan merupakan tangkapan layar dari hari sebelumnya
             */
            if ($qrDate !== $today) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Masa berlaku kode qr telah berakhir untuk tanggal ' . $qrDate
                ], 400);
            }

            /**
             * Validasi data distribusi dalam basis data
             * Mencegah upaya klaim berulang oleh pengguna pada hari yang sama
             */
            $alreadyClaimed = MealClaim::where('user_id', $userId)
                                       ->where('claim_date', $today)
                                       ->exists();

            if ($alreadyClaimed) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Akses ditolak pengguna telah melakukan klaim hari ini'
                ], 403);
            }

            // Pengambilan data profil pengguna untuk proses validasi visual oleh mitra
            $user = User::find($userId);

            return response()->json([
                'status' => 'success',
                'message' => 'Verifikasi berhasil silakan sesuaikan data identitas penerima',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'kategori' => $user->kategori,
                    'alamat' => $user->alamat,
                    'tempat_lahir' => $user->tempat_lahir,
                    'photo_url' => $user->photo ? asset('storage/' . $user->photo) : 'no-photo',
                ]
            ]);

        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // Penanganan terhadap upaya manipulasi atau pemalsuan muatan data qr
            return response()->json([
                'status' => 'error',
                'message' => 'Kode QR tidak dikenali atau terdeteksi hasil manipulasi'
            ], 400);
        }
    }
}