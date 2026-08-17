<?php

namespace App\Policies;

use App\Models\User;

class AdopterProfilePolicy
{
    public function update(User $user): bool
    {
        return $user->isAdmin();
    }
}
