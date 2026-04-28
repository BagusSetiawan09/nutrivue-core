<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    /**
     * Super Admin, IT MBG, dan Petugas boleh melihat daftar ulasan.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'it_mbg', 'petugas']);
    }

    /**
     * Super Admin, IT MBG, dan Petugas boleh melihat detail ulasan.
     */
    public function view(User $user, Review $review): bool
    {
        return in_array($user->role, ['super_admin', 'it_mbg', 'petugas']);
    }

    /**
     * Hanya Super Admin dan IT MBG yang boleh membuat ulasan manual dari Dasbor.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'it_mbg']);
    }

    /**
     * Super Admin, IT MBG, dan Petugas boleh memoderasi (Edit) status tayang ulasan.
     */
    public function update(User $user, Review $review): bool
    {
        return in_array($user->role, ['super_admin', 'it_mbg', 'petugas']);
    }

    /**
     * HANYA Super Admin yang boleh menghapus ulasan satuan.
     */
    public function delete(User $user, Review $review): bool
    {
        return $user->role === 'super_admin';
    }

    /**
     * HANYA Super Admin yang boleh menghapus ulasan massal (Bulk Delete).
     */
    public function deleteAny(User $user): bool
    {
        return $user->role === 'super_admin';
    }
}