<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'kategori',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke tabel Review: Satu User bisa menulis banyak Review
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // 🛡️ JURUS IZIN MASUK DASHBOARD:
        // Hanya role yang kita tentukan yang boleh masuk ke Admin Panel.
        // Masyarakat TIDAK BOLEH masuk (Forbidden).
        
        return in_array($this->role, [
            'super_admin', 
            'petugas', 
            'pemerintah'
        ]);
    }
}