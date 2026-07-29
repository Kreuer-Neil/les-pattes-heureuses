<?php

namespace App\Policies;

use App\Enums\Animals\Status;
use App\Enums\Roles;
use App\Models\Animal;
use App\Models\User;

class AnimalPolicy
{
    public function suggest(User $user): bool
    {
        return $this->isVolunteer($user);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function changeStatus(User $user, Animal $animal): bool
    {
        return $this->isVolunteer($user)
            && $animal->status->name !== Status::Deceased->value
            && $animal->status->name !== Status::Adopted->value;
    }

    public function setAnimalDeceased(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function review(User $user): bool
    {
        return $this->isAdmin($user);
    }

    private function isAdmin(User $user): bool
    {
        return $user->role() === Roles::Admin->value;
    }

    private function isVolunteer(User $user): bool
    {
        return in_array($user->role(), [Roles::Admin->value, Roles::Volunteer->value]);
    }
}
