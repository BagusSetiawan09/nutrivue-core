<?php

namespace App\Policies;

use App\Models\ClientApp;
use App\Models\User;

class ClientAppPolicy
{
    /**
     * HANYA Super Admin yang boleh melihat daftar menu API Keys.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'super_admin';
    }

    /**
     * HANYA Super Admin yang boleh melihat detail API Keys.
     */
    public function view(User $user, ClientApp $clientApp): bool
    {
        return $user->role === 'super_admin';
    }

    /**
     * HANYA Super Admin yang boleh membuat API Keys baru.
     */
    public function create(User $user): bool
    {
        return $user->role === 'super_admin';
    }

    /**
     * HANYA Super Admin yang boleh mengubah API Keys.
     */
    public function update(User $user, ClientApp $clientApp): bool
    {
        return $user->role === 'super_admin';
    }

    /**
     * HANYA Super Admin yang boleh menghapus API Keys.
     */
    public function delete(User $user, ClientApp $clientApp): bool
    {
        return $user->role === 'super_admin';
    }
}