<?php

namespace App\Policies;

use App\Models\User;

class ContactMessagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function reply(User $user): bool
    {
        return $user->isAdmin();
    }

    public function markIgnored(User $user): bool
    {
        return $user->isAdmin();
    }
}
