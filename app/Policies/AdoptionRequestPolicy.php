<?php

namespace App\Policies;

use App\Models\User;

class AdoptionRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function updateStatus(User $user): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user): bool
    {
        return $user->isAdmin();
    }

    public function reply(User $user): bool
    {
        return $user->isAdmin();
    }
}
