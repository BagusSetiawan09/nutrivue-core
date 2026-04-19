<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Inisialisasi pengontrol layanan antarmuka pemrograman aplikasi
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MealController;

/**
 * Pengaturan Jalur Akses API Nourish
 * Mendefinisikan rute publik dan rute terproteksi untuk ekosistem aplikasi
 */

/**
 * Jalur Akses Publik
 * Rute yang dapat diakses tanpa memerlukan token otentikasi
 */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/**
 * Jalur Akses Terproteksi
 * Seluruh rute di bawah ini memerlukan validasi token melalui Laravel Sanctum
 */
Route::middleware('auth:sanctum')->group(function () {
    
    // Kelola otentikasi sesi dan profil pengguna aktif
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/user', function (Request $request) {
        return response()->json([
            'status' => 'success',
            'data'   => $request->user()
        ]);
    });

    /**
     * Kelola distribusi makanan bergizi gratis
     * Menggunakan awalan rute meal untuk pengelompokan fungsi distribusi
     */
    Route::prefix('meal')->group(function () {
        // Pembuatan kode qr bagi penerima jatah makanan
        Route::post('/generate-qr', [MealController::class, 'generateQr']);
        
        // Verifikasi kode qr oleh mitra atau petugas lapangan
        Route::post('/verify-qr', [MealController::class, 'verifyQr']);

        // Rute untuk mengambil data nutrisi hari ini
        Route::get('/today-menu', [MealController::class, 'getTodayMenu']);

        // Rute untuk melihat riwayat klaim makanan
        Route::get('/meal/schedule', [MealController::class, 'getSchedule']);
    });
});