<?php

use Illuminate\Support\Facades\Route;
use App\Models\Pemasok;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\User;

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

Route::get('/bagi-menu-it', function () {
    try {
        // Ambil semua ID pasukan IT MBG yang 10 orang tadi
        $itUsers = User::where('role', 'it_mbg')->pluck('id')->toArray();

        if (empty($itUsers)) {
            return "Pasukan IT MBG belum ada di radar!";
        }

        // Ambil semua katalog menu yang ada di database
        $menus = Menu::all();
        $count = 0;

        foreach ($menus as $menu) {
            // Pilih 1 personel IT MBG secara acak untuk setiap menu
            $randomItId = $itUsers[array_rand($itUsers)];
            
            // Serahkan kepemilikan menu ke personel tersebut
            $menu->created_by = $randomItId;
            $menu->save(); // Menggunakan save() agar aman dari mass-assignment
            
            $count++;
        }

        return "Berhasil: " . $count . " Katalog Menu sudah resmi didistribusikan ke 10 Pasukan IT MBG.";
    } catch (\Exception $e) {
        return "Gagal: " . $e->getMessage();
    }
});