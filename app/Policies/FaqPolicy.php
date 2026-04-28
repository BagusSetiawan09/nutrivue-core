<?php

namespace App\Policies;

use App\Models\Faq;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FaqPolicy
{
    /**
     * Menentukan siapa yang boleh MELIHAT DAFTAR menu Manajemen FAQ.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'it_mbg']);
    }

    /**
     * Menentukan siapa yang boleh MELIHAT DETAIL (View) satu FAQ tertentu.
     */
    public function view(User $user, Faq $faq): bool
    {
        return in_array($user->role, ['super_admin', 'it_mbg']);
    }

    /**
     * Menentukan siapa yang boleh MEMBUAT FAQ baru.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'it_mbg']);
    }

    /**
     * Menentukan siapa yang boleh MENGUBAH (Edit) FAQ.
     */
    public function update(User $user, Faq $faq): bool
    {
        return in_array($user->role, ['super_admin', 'it_mbg']);
    }

    /**
     * Menentukan siapa yang boleh MENGHAPUS (Delete) FAQ.
     */
    public function delete(User $user, Faq $faq): bool
    {
        // Contoh aturan lebih ketat: Hanya Super Admin yang boleh menghapus
        // Jika Bapak ingin IT MBG juga boleh menghapus, ganti menjadi seperti yang di atas.
        return $user->role === 'super_admin'; 
    }
}