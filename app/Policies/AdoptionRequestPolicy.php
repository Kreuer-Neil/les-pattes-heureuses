<?php

namespace App\Policies;

use App\Enums\Roles;
use App\Models\User;

class AdoptionRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function updateStatus(User $user): bool
    {
        return $this->isAdmin($user);
    }

    private function isAdmin(User $user): bool
    {
        return $user->role() === Roles::Admin->value;
    }
}
