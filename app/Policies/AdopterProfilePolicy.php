<?php

namespace App\Policies;

use App\Enums\Roles;
use App\Models\User;

class AdopterProfilePolicy
{
    public function update(User $user): bool
    {
        return $this->isAdmin($user);
    }

    private function isAdmin(User $user): bool
    {
        return $user->role() === Roles::Admin->value;
    }
}
