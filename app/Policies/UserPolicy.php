<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    // Super Admin & IT MBG boleh buka menu ini
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'it_mbg']);
    }

    public function view(User $user, User $model): bool
    {
        if ($user->role === 'super_admin') return true;
        // IT MBG HANYA boleh melihat detail jika dia yang membuat akun tersebut
        return $user->role === 'it_mbg' && $model->created_by === $user->id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'it_mbg']);
    }

    public function update(User $user, User $model): bool
    {
        if ($user->role === 'super_admin') return true;
        // IT MBG HANYA boleh mengubah data jika dia yang membuat akun tersebut
        return $user->role === 'it_mbg' && $model->created_by === $user->id;
    }

    public function delete(User $user, User $model): bool
    {
        if ($user->role === 'super_admin') return true;
        // IT MBG HANYA boleh menghapus jika dia yang membuat akun tersebut
        return $user->role === 'it_mbg' && $model->created_by === $user->id;
    }
}