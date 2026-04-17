<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealClaim extends Model
{
    use HasFactory;

    // Menghubungkan secara eksplisit ke tabel 'meal_claims'
    protected $table = 'meal_claims';

    // Kolom-kolom yang diizinkan untuk diisi secara massal (Mass Assignment)
    protected $fillable = [
        'user_id',
        'claim_date',
        'status',
        'mitra_id',
    ];

    // ==========================================
    // RELASI ANTAR TABEL (Sangat Penting)
    // ==========================================
    
    /**
     * Relasi ke model User.
     * Artinya: Setiap 1 data klaim makan, pasti dimiliki oleh 1 User.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}