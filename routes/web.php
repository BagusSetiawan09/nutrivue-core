<?php

use Illuminate\Support\Facades\Route;
use App\Models\Pemasok;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

// 1. RUTE GET: Untuk menampilkan halaman formulir pendaftaran
Route::get('/pendaftaran-pemasok', function () {
    return view('pendaftaran-pemasok');
});

// 2. RUTE POST: Untuk memproses dan menyimpan data saat form di-submit
Route::post('/pendaftaran-pemasok', function (Request $request) {
    // Validasi Sederhana
    $validated = $request->validate([
        'nama_usaha' => 'required',
        'nama_pemilik' => 'required',
        'no_wa' => 'required',
        'foto_dapur' => 'required|image|max:2048',
    ]);

    // Handle Upload File
    $fotoDapurPath = $request->file('foto_dapur')->store('pemasok-dapur', 'public');
    
    $fileHalalPath = null;
    if ($request->hasFile('file_sertifikat_halal')) {
        $fileHalalPath = $request->file('file_sertifikat_halal')->store('pemasok-sertifikat', 'public');
    }

    // Simpan Ke Database
    Pemasok::create([
        'nama_usaha' => $request->nama_usaha,
        'nama_pemilik' => $request->nama_pemilik,
        'no_wa' => $request->no_wa,
        'email' => $request->email,
        'alamat' => $request->alamat,
        'kapasitas_produksi_harian' => $request->kapasitas_produksi_harian,
        'bahan_baku_tersedia' => $request->bahan_baku_tersedia, 
        'is_halal' => $request->has('is_halal'),
        'no_sertifikat_halal' => $request->no_sertifikat_halal,
        'file_sertifikat_halal' => $fileHalalPath,
        'foto_dapur' => $fotoDapurPath,
        'deskripsi' => $request->deskripsi,
        'status_akun' => 'Aktif', 
    ]);

    return redirect('/')->with('success', 'Pendaftaran Berhasil! Tim kami akan menghubungi Anda.');
});