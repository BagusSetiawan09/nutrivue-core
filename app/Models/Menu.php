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
        'titik_penyaluran_id',
        'foto_makanan',
        'deskripsi',
        'kalori',
        'protein',
        'karbohidrat',
        'lemak',
        'status',
        'created_by',
    ];

    public function titik_penyaluran()
    {
        return $this->belongsTo(TitikPenyaluran::class);
    }

    // Relationship with Review
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}