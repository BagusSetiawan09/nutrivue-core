<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TitikPenyaluran extends Model
{
    protected $fillable = [
        'nama_lokasi', 'jenis_lokasi', 'alamat', 'penanggung_jawab', 'kontak_person', 'map_url'
    ];

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }
}
