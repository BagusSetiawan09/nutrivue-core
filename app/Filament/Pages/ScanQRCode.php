<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\MealClaim;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Filament\Notifications\Notification;

/**
 * Halaman Scanner Petugas Lapangan
 * Mengelola verifikasi QR Code penerima manfaat dan validasi klaim distribusi makanan
 */
class ScanQRCode extends Page
{
    // Konfigurasi ikon navigasi bilah sisi
    protected static ?string $navigationIcon = 'heroicon-o-qr-code';

    // Pengelompokan menu dalam kategori distribusi
    protected static ?string $navigationGroup = 'Distribusi Makanan';

    // Label navigasi yang tampil pada menu sidebar
    protected static ?string $navigationLabel = 'Scanner Petugas';

    // Judul halaman yang tampil pada header
    protected static ?string $title = 'Verifikasi Penerima';

    // Urutan tampilan menu pada navigasi
    protected static ?int $navigationSort = 1;

    // View yang digunakan untuk merender tampilan scanner
    protected static string $view = 'filament.pages.scan-q-r-code';

    // Data hasil pindaian pengguna
    public ?array $scannedData = null;

    // Status aktivitas proses pindaian
    public string $scanStatus = 'idle'; 

    // Pesan informatif untuk petugas operasional
    public string $scanMessage = 'Sistem Siap Menunggu pindaian';

    /**
     * Memproses dan memvalidasi data QR Code terenkripsi
     */
    public function verifyScannedQr($encryptedQr)
    {
        try {
            // Dekripsi muatan data QR Code
            $decryptedPayload = Crypt::decryptString($encryptedQr);
            $payloadParts = explode('|', $decryptedPayload);

            // Validasi integritas format data hasil dekripsi
            if (count($payloadParts) !== 2) {
                throw new \Exception('Format QR tidak valid');
            }

            $userId = $payloadParts[0];
            $qrDate = $payloadParts[1];
            $today = Carbon::today()->toDateString();

            // Pencarian data pengguna berdasarkan identitas QR
            $user = User::find($userId);
            if ($user) {
                $this->scannedData = [
                    'name' => $user->name,
                    'kategori' => $user->kategori,
                    'alamat' => $user->alamat ?? 'Data alamat tidak ditemukan',
                ];
            }

            /**
             * Tahap Validasi 1: Pemeriksaan Masa Berlaku QR Code
             * Mencegah penggunaan token dari tanggal yang berbeda
             */
            if ($qrDate !== $today) {
                $this->scanStatus = 'error';
                $this->scanMessage = "Token QR Code kedaluwarsa Kode diterbitkan khusus untuk tanggal {$qrDate}";
                
                Notification::make()
                    ->title('Validasi Gagal')
                    ->body('QR Code yang digunakan sudah kedaluwarsa')
                    ->danger()
                    ->send();

                $this->dispatch('scan-finished');
                return;
            }

            if (!$user) {
                throw new \Exception('Data Pengguna tidak ditemukan dalam sistem');
            }

            /**
             * Tahap Validasi 2: Pemeriksaan Klaim Ganda
             * Memastikan penerima belum mengambil jatah pada hari yang sama
             */
            $existingClaim = MealClaim::where('user_id', $userId)
                                       ->where('claim_date', $today)
                                       ->first();

            if ($existingClaim) {
                $this->scanStatus = 'error';
                $timeClaimed = $existingClaim->created_at->format('H:i WIB'); 
                
                $this->scanMessage = "Klaim Ganda Terdeteksi Penerima telah mengambil jatah pada pukul {$timeClaimed}";

                Notification::make()
                    ->title('Peringatan Klaim Ganda')
                    ->body("Jatah makan pengguna ini telah didistribusikan pada {$timeClaimed}")
                    ->warning()
                    ->send();

                $this->dispatch('scan-finished');
                return;
            }

            /**
             * Tahap 3: Eksekusi Pencatatan Klaim Berhasil
             * Menyimpan riwayat distribusi ke dalam database
             */
            MealClaim::create([
                'user_id' => $user->id,
                'claim_date' => $today,
                'status' => 'claimed',
                'mitra_id' => Auth::id(),
            ]);

            $this->scanStatus = 'success';
            $this->scanMessage = 'Otentikasi Berhasil Silakan distribusikan paket makanan kepada penerima';

            Notification::make()
                ->title('Verifikasi Sah')
                ->body("Akses diberikan untuk {$user->name}")
                ->success()
                ->send();

            $this->dispatch('scan-finished');

        } catch (\Exception $e) {
            // Penanganan kegagalan sistem atau indikasi manipulasi data
            $this->scanStatus = 'error';
            $this->scanMessage = 'Sistem menolak akses QR Code tidak dikenali atau rusak';
            $this->scannedData = null; 

            Notification::make()
                ->title('Akses Ditolak')
                ->body('Terjadi indikasi QR Code tidak valid atau dimanipulasi')
                ->danger()
                ->send();

            $this->dispatch('scan-finished');
        }
    }
}