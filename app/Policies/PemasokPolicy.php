<?php

namespace App\Policies;

use App\Models\Pemasok;
use App\Models\User;

class PemasokPolicy
{
    public function viewAny(User $user): bool {
        return in_array($user->role, ['super_admin', 'it_mbg']);
    }
    public function view(User $user, Pemasok $pemasok): bool {
        return in_array($user->role, ['super_admin', 'it_mbg']);
    }
    public function create(User $user): bool {
        return $user->role === 'super_admin';
    }
    public function update(User $user, Pemasok $pemasok): bool {
        return $user->role === 'super_admin';
    }
    public function delete(User $user, Pemasok $pemasok): bool {
        return $user->role === 'super_admin';
    }
}