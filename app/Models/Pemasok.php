<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemasok extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_usaha',
        'nama_pemilik',
        'no_wa',
        'email',
        'alamat',
        'kapasitas_produksi_harian',
        'status_akun',
        'alasan_laporan',
        'bahan_baku_tersedia',
        'is_halal',
        'no_sertifikat_halal',
        'file_sertifikat_halal',
        'deskripsi',
        'foto_dapur',
    ];

    protected function casts(): array
    {
        return [
            'is_halal' => 'boolean',
            'bahan_baku_tersedia' => 'array',
        ];
    }
}