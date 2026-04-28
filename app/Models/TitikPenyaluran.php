<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TitikPenyaluran extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_lokasi', 'jenis_lokasi', 'kode_rahasia', 'alamat', 'penanggung_jawab', 'kontak_person', 'map_url', 'created_by'
    ];

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }
}