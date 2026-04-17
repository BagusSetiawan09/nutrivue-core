<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\MealClaim;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Filament\Notifications\Notification; // <-- KITA PANGGIL KEMBALI NOTIFIKASI FILAMENT!

class ScanQRCode extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationGroup = 'Distribusi Makanan';
    protected static ?string $navigationLabel = 'Scanner Petugas';
    protected static ?string $title = 'Verifikasi Penerima';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.scan-q-r-code';

    public ?array $scannedData = null;
    public string $scanStatus = 'idle'; 
    public string $scanMessage = 'Sistem Siap. Menunggu pindaian...';

    public function verifyScannedQr($encryptedQr)
    {
        try {
            $decryptedPayload = Crypt::decryptString($encryptedQr);
            $payloadParts = explode('|', $decryptedPayload);

            if (count($payloadParts) !== 2) {
                throw new \Exception('Format QR tidak valid.');
            }

            $userId = $payloadParts[0];
            $qrDate = $payloadParts[1];
            $today = Carbon::today()->toDateString();

            // Coba cari data User
            $user = User::find($userId);
            if ($user) {
                $this->scannedData = [
                    'name' => $user->name,
                    'kategori' => $user->kategori,
                    'alamat' => $user->alamat ?? 'Tidak ada data alamat terdaftar',
                ];
            }

            // 1. CEK KEDALUWARSA (EXPIRED)
            if ($qrDate !== $today) {
                $this->scanStatus = 'error';
                $this->scanMessage = "Token QR Code telah kedaluwarsa. Kode ini secara eksklusif diterbitkan untuk tanggal {$qrDate}.";
                
                Notification::make()
                    ->title('Validasi Gagal')
                    ->body('QR Code yang digunakan sudah kedaluwarsa.')
                    ->danger()
                    ->send();

                $this->dispatch('scan-finished');
                return;
            }

            if (!$user) {
                throw new \Exception('Data Pengguna tidak ditemukan di Database.');
            }

            // 2. CEK KLAIM GANDA (DOUBLE CLAIM)
            $existingClaim = MealClaim::where('user_id', $userId)
                                       ->where('claim_date', $today)
                                       ->first();

            if ($existingClaim) {
                $this->scanStatus = 'error';
                // Ambil jam berapa dia tadi ngambil makanannya
                $timeClaimed = $existingClaim->created_at->format('H:i WIB'); 
                
                $this->scanMessage = "Klaim Ganda Terdeteksi! Penerima ini tercatat telah mengambil jatah makanan pada pukul {$timeClaimed}.";

                Notification::make()
                    ->title('Peringatan: Klaim Ganda')
                    ->body("Jatah makan pengguna ini telah didistribusikan pada {$timeClaimed}.")
                    ->warning() // Menggunakan warna kuning/oranye peringatan
                    ->send();

                $this->dispatch('scan-finished');
                return;
            }

            // 3. PROSES KLAIM SUKSES!
            MealClaim::create([
                'user_id' => $user->id,
                'claim_date' => $today,
                'status' => 'claimed',
                'mitra_id' => Auth::id(),
            ]);

            $this->scanStatus = 'success';
            $this->scanMessage = 'Otentikasi Berhasil. Silakan distribusikan paket makanan kepada penerima bersangkutan.';

            Notification::make()
                ->title('Verifikasi Sah ✅')
                ->body("Akses diberikan untuk: {$user->name}")
                ->success()
                ->send();

            $this->dispatch('scan-finished');

        } catch (\Exception $e) {
            $this->scanStatus = 'error';
            $this->scanMessage = 'Sistem menolak akses. QR Code tidak dikenali, rusak, atau telah dimanipulasi secara ilegal.';
            $this->scannedData = null; 

            Notification::make()
                ->title('Akses Ditolak')
                ->body('Terjadi indikasi QR Code palsu atau tidak valid.')
                ->danger()
                ->send();

            $this->dispatch('scan-finished');
        }
    }
}