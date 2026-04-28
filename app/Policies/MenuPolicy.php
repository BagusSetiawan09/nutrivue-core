<?php

namespace App\Policies;

use App\Models\Menu;
use App\Models\User;

class MenuPolicy
{
    /**
     * Semua peran boleh melihat daftar menu.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'it_mbg', 'petugas', 'pemerintah']);
    }

    /**
     * Semua peran boleh melihat detail menu.
     */
    public function view(User $user, Menu $menu): bool
    {
        return in_array($user->role, ['super_admin', 'it_mbg', 'petugas', 'pemerintah']);
    }

    /**
     * Hanya Super Admin dan IT MBG yang boleh membuat jadwal menu.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'it_mbg']);
    }

    /**
     * Super Admin, IT MBG, dan Petugas boleh mengubah data (Pemerintah dilarang).
     */
    public function update(User $user, Menu $menu): bool
    {
        return in_array($user->role, ['super_admin', 'it_mbg', 'petugas']);
    }

    /**
     * Super Admin, IT MBG, dan Petugas boleh menghapus data satuan.
     */
    public function delete(User $user, Menu $menu): bool
    {
        return in_array($user->role, ['super_admin', 'it_mbg', 'petugas']);
    }

    /**
     * Hanya Super Admin yang boleh melakukan hapus massal (Bulk Delete).
     */
    public function deleteAny(User $user): bool
    {
        return $user->role === 'super_admin';
    }
}