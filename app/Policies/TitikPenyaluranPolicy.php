<?php

namespace App\Policies;

use App\Models\TitikPenyaluran;
use App\Models\User;

class TitikPenyaluranPolicy
{
    /**
     * Semua role yang boleh masuk panel (Super Admin, IT MBG, Pemerintah) boleh MELIHAT daftar.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'it_mbg', 'pemerintah']);
    }

    /**
     * Semua role boleh MELIHAT DETAIL titik penyaluran.
     */
    public function view(User $user, TitikPenyaluran $titikPenyaluran): bool
    {
        return in_array($user->role, ['super_admin', 'it_mbg', 'pemerintah']);
    }

    /**
     * HANYA Super Admin dan IT MBG yang boleh MENAMBAH data (Pemerintah dilarang).
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'it_mbg']);
        // Ini menggantikan logika: return $user->role !== 'pemerintah';
    }

    /**
     * HANYA Super Admin dan IT MBG yang boleh MENGUBAH data.
     */
    public function update(User $user, TitikPenyaluran $titikPenyaluran): bool
    {
        return in_array($user->role, ['super_admin', 'it_mbg']);
    }

    /**
     * HANYA Super Admin dan IT MBG yang boleh MENGHAPUS data.
     */
    public function delete(User $user, TitikPenyaluran $titikPenyaluran): bool
    {
        return in_array($user->role, ['super_admin', 'it_mbg']);
    }
}