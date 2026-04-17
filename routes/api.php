<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Import Semua Controller Di Sini
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MealController;

/*
|--------------------------------------------------------------------------
| API Routes Nourish
|--------------------------------------------------------------------------
*/

// ==========================================================
// 1. PUBLIC ROUTES (Tanpa perlu Token / GUEST)
// ==========================================================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// ==========================================================
// 2. PROTECTED ROUTES (Hanya bisa diakses jika punya Token)
// ==========================================================
Route::middleware('auth:sanctum')->group(function () {
    
    // --- A. PROFILE & AUTH ---
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/user', function (Request $request) {
        return response()->json([
            'status' => 'success',
            'data'   => $request->user()
        ]);
    });

    // --- B. MEAL & DISTRIBUTION (MBG) ---
    // Menggunakan Prefix 'meal' agar URL otomatis menjadi /meal/generate-qr dll
    Route::prefix('meal')->group(function () {
        Route::post('/generate-qr', [MealController::class, 'generateQr']);
        Route::post('/verify-qr', [MealController::class, 'verifyQr']);
    });

    
});