<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Inisialisasi pengontrol
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MealController;
use App\Http\Controllers\Api\ProfileController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    
    // Kelola otentikasi sesi dan profil
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/user', function (Request $request) {
        return response()->json([
            'status' => 'success',
            'data'   => $request->user()
        ]);
    });

    Route::post('/profile/update', [AuthController::class, 'updateProfile']);

    /**
     * Kelola distribusi makanan bergizi gratis (Otomatis ditambah /api/meal di depannya)
     */
    Route::prefix('meal')->group(function () {
        Route::post('/generate-qr', [MealController::class, 'generateQr']);
        Route::post('/verify-qr', [MealController::class, 'verifyQr']);
        Route::get('/today-menu', [MealController::class, 'getTodayMenu']);

        // Sebagai /api/meal/schedule
        Route::get('/schedule', [MealController::class, 'getSchedule']);

        // Sebagai /api/meal/statistics
        Route::get('/statistics', [MealController::class, 'getStatistics']);
    });

    // RUTE UNTUK DATA KESEHATAN
    Route::get('/profile/health', [ProfileController::class, 'getHealth']);
    Route::post('/profile/health', [ProfileController::class, 'updateHealth']);
    Route::post('/update-privacy', [ProfileController::class, 'updatePrivacy']);
    Route::post('/change-password', [ProfileController::class, 'changePassword']);
    Route::post('/profile/verify-institution', [ProfileController::class, 'verifyInstitution']);
});