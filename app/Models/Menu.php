<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    // Mass assignment
    protected $fillable = [
        'nama_menu',
        'tanggal_distribusi',
        'target_penerima',
        'lokasi_distribusi',
        'foto_makanan',
        'deskripsi',
        'kalori',
        'protein',
        'karbohidrat',
        'lemak',
        'status',
    ];

    // Relationship with Review
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}