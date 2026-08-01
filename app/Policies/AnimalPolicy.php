<?php

namespace App\Policies;

use App\Enums\Animals\Status;
use App\Models\Animal;
use App\Models\User;

class AnimalPolicy
{
    public function suggest(User $user): bool
    {
        return $user->exists();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user): bool
    {
        return $user->isAdmin();
    }

    public function changeStatus(User $user, Animal $animal): bool
    {
        return $user->exists()
            && $animal->status->name !== Status::Deceased->value
            && $animal->status->name !== Status::Adopted->value;
    }

    public function setAnimalDeceased(User $user): bool
    {
        return $user->isAdmin();
    }

    public function review(User $user): bool
    {
        return $user->isAdmin();
    }

    public function viewStatistics(User $user): bool
    {
        return $user->isAdmin();
    }
}
