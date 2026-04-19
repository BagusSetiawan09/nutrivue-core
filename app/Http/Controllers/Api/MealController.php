<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MealClaim;
use App\Models\Menu;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

/**
 * Pengaturan Kontroler Distribusi Makanan
 */
class MealController
{
    /**
     * Mengambil RIWAYAT PENGAMBILAN (Misi Utama Kita!)
     * Menghubungkan data klaim user dengan detail menu untuk ditampilkan di HP
     */
    public function getHistory(Request $request)
    {
        try {
            $user = $request->user();

            // 1. Ambil data klaim 30 hari terakhir
            $claims = MealClaim::where('user_id', $user->id)
                ->with('menu') // Pastikan ada relasi 'menu' di model MealClaim
                ->orderBy('claim_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            // 2. Format data agar ramah untuk UI Mobile React Native
            $formattedData = $claims->map(function ($claim) {
                // Konversi tanggal ke format Indonesia (misal: 19 April 2026)
                $date = Carbon::parse($claim->claim_date);
                
                return [
                    'id' => $claim->id,
                    'tanggal' => $date->translatedFormat('d F Y'),
                    'waktu' => Carbon::parse($claim->created_at)->format('H:i') . ' WIB',
                    'menu' => $claim->menu->nama_menu ?? 'Menu Gizi Sehat',
                    'kalori' => ($claim->menu->kalori ?? 0) . ' kcal',
                    'status' => 'Berhasil Diambil',
                ];
            });

            // 3. Hitung total porsi bulan ini untuk statistik di kotak biru atas
            $totalMonth = MealClaim::where('user_id', $user->id)
                ->whereMonth('claim_date', Carbon::now()->month)
                ->whereYear('claim_date', Carbon::now()->year)
                ->count();

            return response()->json([
                'status' => 'success',
                'total_bulan_ini' => $totalMonth,
                'data' => $formattedData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menarik data riwayat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengambil Jadwal Menu 3 Hari ke Depan Berdasarkan Target Penerima
     * Diformat khusus untuk PieChart di React Native MenuScreen
     */
    public function getSchedule(Request $request)
    {
        try {
            $user = $request->user();

            // 1. Pemetaan cerdas kategori user ke target_penerima di tabel Menu
            $targetPenerima = 'Siswa'; // Default
            if (in_array(strtolower($user->kategori), ['ibu_hamil', 'ibu hamil'])) {
                $targetPenerima = 'Ibu Hamil';
            } elseif (in_array(strtolower($user->kategori), ['ibu_balita', 'balita'])) {
                $targetPenerima = 'Balita';
            } elseif (strtolower($user->kategori) === 'siswa') {
                $targetPenerima = 'Siswa';
            }

            // 2. Siapkan 3 Tanggal Utama
            $today = Carbon::today()->toDateString();
            $tomorrow = Carbon::tomorrow()->toDateString();
            $lusa = Carbon::today()->addDays(2)->toDateString();

            // 3. Tarik data menu dari database berdasarkan tanggal & target
            $menus = Menu::whereIn('tanggal_distribusi', [$today, $tomorrow, $lusa])
                        ->where('target_penerima', $targetPenerima)
                        ->get()
                        ->keyBy('tanggal_distribusi');

            // 4. Fungsi internal untuk merakit data sesuai selera frontend PieChart
            $formatMenu = function ($menu) {
                if (!$menu) return null;
                
                return [
                    'title' => $menu->nama_menu,
                    // Jika ada foto, jadikan link URL penuh. Jika tidak, pakai gambar default.
                    'image' => $menu->foto_makanan ? asset('storage/' . $menu->foto_makanan) : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=1000&auto=format&fit=crop',
                    'description' => $menu->deskripsi ?? 'Deskripsi menu belum dilengkapi.',
                    'calories' => $menu->kalori ?? 0,
                    'macros' => [
                        [
                            'name' => 'Protein',
                            'population' => (int) ($menu->protein ?? 0),
                            'color' => '#6366F1', // Indigo untuk Protein
                            'description' => 'Sangat penting untuk perbaikan sel dan pertumbuhan.'
                        ],
                        [
                            'name' => 'Lemak',
                            'population' => (int) ($menu->lemak ?? 0),
                            'color' => '#F43F5E', // Rose untuk Lemak
                            'description' => 'Sumber energi cadangan dan membantu penyerapan vitamin.'
                        ],
                        [
                            'name' => 'Karbohidrat',
                            'population' => (int) ($menu->karbohidrat ?? 0),
                            'color' => '#14B8A6', // Teal untuk Karbohidrat
                            'description' => 'Sumber energi utama untuk otak dan aktivitas harian.'
                        ]
                    ]
                ];
            };

            // 5. Susun hasil akhir sesuai kunci Hari yang diminta Frontend
            $data = [
                'Hari Ini' => $formatMenu($menus->get($today)),
                'Besok'    => $formatMenu($menus->get($tomorrow)),
                'Lusa'     => $formatMenu($menus->get($lusa)),
            ];

            // Bersihkan hari yang tidak ada jadwalnya (null) agar frontend merespon dengan rapi
            $data = array_filter($data);

            return response()->json([
                'status' => 'success',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil jadwal menu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghasilkan muatan data qr code terenkripsi
     */
    public function generateQr(Request $request)
    {
        $user = $request->user(); 
        $today = Carbon::today()->toDateString(); 

        $alreadyClaimed = MealClaim::where('user_id', $user->id)
                                   ->where('claim_date', $today)
                                   ->exists();

        if ($alreadyClaimed) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda telah tercatat menggunakan jatah makan hari ini'
            ], 403);
        }

        $rawPayload = $user->id . '|' . $today;
        $encryptedQr = Crypt::encryptString($rawPayload);

        return response()->json([
            'status' => 'success',
            'message' => 'Kode QR berhasil diterbitkan oleh sistem',
            'qr_data' => $encryptedQr 
        ]);
    }

    /**
     * Verifikasi pindaian QR oleh mitra lapangan
     */
    public function verifyQr(Request $request)
    {
        try {
            $encryptedQr = $request->qr_data; 
            $decryptedPayload = Crypt::decryptString($encryptedQr);
            $payloadParts = explode('|', $decryptedPayload);

            $userId = $payloadParts[0];
            $qrDate = $payloadParts[1];
            $today = Carbon::today()->toDateString();

            if ($qrDate !== $today) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Masa berlaku kode qr telah berakhir'
                ], 400);
            }

            $alreadyClaimed = MealClaim::where('user_id', $userId)
                                       ->where('claim_date', $today)
                                       ->exists();

            if ($alreadyClaimed) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Akses ditolak, pengguna telah melakukan klaim'
                ], 403);
            }

            $user = User::find($userId);

            return response()->json([
                'status' => 'success',
                'message' => 'Verifikasi berhasil',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'kategori' => $user->kategori,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode QR tidak valid'
            ], 400);
        }
    }

    /**
     * Mengambil informasi menu harian
     */
    public function getTodayMenu(Request $request)
    {
        try {
            $menu = Menu::latest()->first();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'kalori' => $menu->kalori ?? 0,
                    'protein' => $menu->protein ?? 0,
                    'lemak' => $menu->lemak ?? 0,
                    'nama_menu' => $menu->nama_menu ?? 'Menu Belum Ditentukan'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error'], 500);
        }
    }
}